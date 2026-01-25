<?php

namespace App\Livewire\Marketing;

use App\Models\ServiceCategory;
use App\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Landing extends Component
{
    #[Computed]
    public function topCategories(): Collection
    {
        return ServiceCategory::query()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function popularSubcategories(): Collection
    {
        return ServiceCategory::query()
            ->whereNotNull('parent_id')
            ->withCount([
                'serviceRequests as published_requests_count' => fn ($q) => $q->where('status', ServiceRequestStatus::Published->value),
            ])
            ->orderByDesc('published_requests_count')
            ->orderBy('name')
            ->limit(12)
            ->get();
    }

    public function render()
    {
        return view('livewire.marketing.landing')
            ->layout('layouts.marketing', ['title' => 'Integral Service Tech']);
    }
}

