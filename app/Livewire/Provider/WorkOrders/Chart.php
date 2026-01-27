<?php

namespace App\Livewire\Provider\WorkOrders;

use App\Models\WorkOrder;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Chart extends Component
{
    #[Computed]
    public function workOrdersByStatus(): array
    {
        return WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    #[Computed]
    public function workOrdersByMonth(): array
    {
        return WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    #[Computed]
    public function totalRevenue(): float
    {
        return (float) WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->whereNotNull('final_price')
            ->sum('final_price');
    }

    #[Computed]
    public function averageRating(): float
    {
        $ratings = WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->with('ratings')
            ->get()
            ->flatMap->ratings;

        if ($ratings->isEmpty()) {
            return 0.0;
        }

        return round($ratings->avg('rating'), 2);
    }

    public function mount(): void
    {
        abort_unless(! auth()->user()->isGuest() && ! auth()->user()->isClient(), 403);
    }

    public function render()
    {
        return view('livewire.provider.work-orders.chart')
            ->layout('layouts.app', ['title' => __('Gráficos de Órdenes de Trabajo')]);
    }
}
