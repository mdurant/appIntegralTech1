<?php

namespace App\Livewire\Provider\WorkOrders;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    #[Computed]
    public function workOrders(): Collection
    {
        return WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->with(['serviceRequest.category', 'serviceBid', 'tenant'])
            ->latest('id')
            ->get();
    }

    public function mount(): void
    {
        abort_unless(! auth()->user()->isGuest() && ! auth()->user()->isClient(), 403);

        auth()->user()
            ->unreadNotifications()
            ->where('type', \App\Notifications\BidAcceptedNotification::class)
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.provider.work-orders.index')
            ->layout('layouts.app', ['title' => __('Mis Órdenes de Trabajo')]);
    }
}
