<?php

namespace App\Livewire\Services;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Browse extends Component
{
    public int|string|null $categoryId = null;

    #[Computed]
    public function categories(): Collection
    {
        return ServiceCategory::query()->orderBy('name')->get();
    }

    #[Computed]
    public function serviceRequests(): Collection
    {
        $userId = auth()->id();

        return ServiceRequest::query()
            ->where('status', ServiceRequestStatus::Published->value)
            ->when($this->categoryId, fn ($q) => $q->where('category_id', (int) $this->categoryId))
            ->when($userId, function ($q) use ($userId) {
                $q->whereDoesntHave('paymentSimulations', function ($sub) use ($userId) {
                    $sub->where('user_id', $userId)->where('status', 'approved');
                });
            })
            ->with(['category', 'tenant', 'region', 'commune'])
            ->latest('published_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.services.browse')
            ->layout('layouts.app', ['title' => __('Solicitudes de cotización')]);
    }
}
