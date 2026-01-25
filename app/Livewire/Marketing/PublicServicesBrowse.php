<?php

namespace App\Livewire\Marketing;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class PublicServicesBrowse extends Component
{
    public int|string|null $categoryId = null;

    public string $q = '';

    public function mount(): void
    {
        $this->q = (string) request()->query('q', $this->q);
        $this->categoryId = request()->query('categoryId', $this->categoryId);
    }

    public function getCategoriesProperty(): Collection
    {
        return ServiceCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    public function getServiceRequestsProperty(): Collection
    {
        $term = trim($this->q);

        return ServiceRequest::query()
            ->where('status', ServiceRequestStatus::Published->value)
            ->when($this->categoryId, fn ($q) => $q->where('category_id', (int) $this->categoryId))
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($q) use ($term) {
                    $q->where('title', 'like', '%'.$term.'%')
                        ->orWhere('description', 'like', '%'.$term.'%')
                        ->orWhere('notes', 'like', '%'.$term.'%');
                });
            })
            ->with(['category', 'tenant'])
            ->latest('published_at')
            ->limit(30)
            ->get();
    }

    public function render()
    {
        return view('livewire.marketing.public-services-browse')
            ->layout('layouts.marketing', ['title' => 'Explorar servicios']);
    }
}

