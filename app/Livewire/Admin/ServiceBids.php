<?php

namespace App\Livewire\Admin;

use App\Models\ServiceBid;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ServiceBids extends Component
{
    #[Computed]
    public function bids(): Collection
    {
        return ServiceBid::query()
            ->with(['user', 'serviceRequest'])
            ->latest('id')
            ->get();
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    public function delete(int $bidId): void
    {
        ServiceBid::query()->findOrFail($bidId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.service-bids')
            ->layout('layouts.app', ['title' => __('Admin · Presupuestos')]);
    }
}

