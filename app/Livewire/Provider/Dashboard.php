<?php

namespace App\Livewire\Provider;

use App\Models\ServiceBid;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function totalBids(): int
    {
        return ServiceBid::query()
            ->where('user_id', auth()->id())
            ->count();
    }

    #[Computed]
    public function acceptedBids(): int
    {
        return ServiceBid::query()
            ->where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->count();
    }

    #[Computed]
    public function conversionRate(): float
    {
        if ($this->totalBids === 0) {
            return 0.0;
        }

        return round(($this->acceptedBids / $this->totalBids) * 100, 2);
    }

    #[Computed]
    public function workOrders(): Collection
    {
        return WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->with(['serviceRequest.category', 'serviceBid'])
            ->latest('id')
            ->get();
    }

    #[Computed]
    public function totalOfferedAmount(): float
    {
        return (float) ServiceBid::query()
            ->where('user_id', auth()->id())
            ->sum('amount');
    }

    #[Computed]
    public function totalFinalAmount(): float
    {
        return (float) WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->whereNotNull('final_price')
            ->sum('final_price');
    }

    public function mount(): void
    {
        abort_unless(! auth()->user()->isGuest() && ! auth()->user()->isClient(), 403);
    }

    public function render()
    {
        return view('livewire.provider.dashboard')
            ->layout('layouts.app', ['title' => __('Dashboard Profesional')]);
    }
}
