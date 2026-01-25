<?php

namespace App\Livewire\Marketing;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LandingSearch extends Component
{
    public string $q = '';

    #[Computed]
    public function categories(): Collection
    {
        $term = trim($this->q);

        if ($term === '') {
            return ServiceCategory::query()
                ->whereNotNull('parent_id')
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        return ServiceCategory::query()
            ->whereNotNull('parent_id')
            ->where('name', 'like', '%'.$term.'%')
            ->orderBy('name')
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function requests(): Collection
    {
        $term = trim($this->q);

        if ($term === '') {
            return collect();
        }

        return ServiceRequest::query()
            ->where('status', ServiceRequestStatus::Published->value)
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%');
            })
            ->with('category')
            ->latest('published_at')
            ->limit(6)
            ->get();
    }

    public function search(): void
    {
        $term = trim($this->q);

        $params = [];
        if ($term !== '') {
            $params['q'] = $term;
        }

        $this->redirectRoute('public.services.browse', $params);
    }

    public function render()
    {
        return view('livewire.marketing.landing-search');
    }
}

