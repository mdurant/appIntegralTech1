<?php

namespace App\Livewire\Client;

use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\ServiceRequestStatus;
use App\WorkOrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function requestsWithNewBids(): Collection
    {
        return ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->where('status', ServiceRequestStatus::Published->value)
            ->whereNull('awarded_bid_id')
            ->whereHas('bids', fn ($q) => $q->where('status', 'submitted'))
            ->with(['category', 'bids' => fn ($q) => $q->where('status', 'submitted')])
            ->latest('updated_at')
            ->get();
    }

    #[Computed]
    public function unreadBidNotificationsCount(): int
    {
        return auth()->user()
            ->unreadNotifications()
            ->where('type', \App\Notifications\BidReceivedNotification::class)
            ->count();
    }

    #[Computed]
    public function workOrders(): Collection
    {
        return WorkOrder::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->with(['serviceRequest.category', 'awardedTo'])
            ->latest('id')
            ->get();
    }

    #[Computed]
    public function workOrdersByStatus(): array
    {
        $orders = $this->workOrders;

        return [
            'pending' => $orders->where('status', WorkOrderStatus::Open->value)->count(),
            'in_progress' => $orders->where('status', WorkOrderStatus::InProgress->value)->count(),
            'completed' => $orders->where('status', WorkOrderStatus::Completed->value)->count(),
            'paid' => $orders->where('status', WorkOrderStatus::Paid->value)->count(),
        ];
    }

    #[Computed]
    public function expensesByCategory(): array
    {
        return WorkOrder::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->whereNotNull('final_price')
            ->with('serviceRequest.category')
            ->get()
            ->groupBy(fn ($wo) => $wo->serviceRequest->category?->name ?? 'Sin categoría')
            ->map(fn ($group) => $group->sum('final_price'))
            ->toArray();
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->isClient(), 403);
        abort_unless(auth()->user()->current_tenant_id, 403);
    }

    public function render()
    {
        return view('livewire.client.dashboard')
            ->layout('layouts.app', ['title' => __('Dashboard Cliente')]);
    }
}
