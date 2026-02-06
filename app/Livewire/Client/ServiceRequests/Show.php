<?php

namespace App\Livewire\Client\ServiceRequests;

use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\Services\ServiceBidService;
use App\Services\ServiceRequestService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public ServiceRequest $serviceRequest;

    public function mount(ServiceRequest $serviceRequest): void
    {
        $this->serviceRequest = $serviceRequest->load([
            'category',
            'category.parent',
            'tenant',
            'bids.user',
        ]);

        abort_unless(auth()->user()->isClient(), 403);
        abort_unless(auth()->user()->belongsToTenant($this->serviceRequest->tenant), 403);

        auth()->user()
            ->unreadNotifications()
            ->where('type', \App\Notifications\BidReceivedNotification::class)
            ->where('data->service_request_id', $this->serviceRequest->id)
            ->update(['read_at' => now()]);
    }

    #[Computed]
    public function bids(): Collection
    {
        return $this->serviceRequest->bids()->with('user')->latest('id')->get();
    }

    public function reopen(ServiceRequestService $serviceRequestService): void
    {
        $this->authorize('reopen', $this->serviceRequest);

        $serviceRequestService->reopen($this->serviceRequest);

        $this->serviceRequest->refresh();
    }

    public function award(int $bidId, ServiceBidService $serviceBidService, ServiceRequestService $serviceRequestService): void
    {
        $bid = ServiceBid::query()
            ->where('service_request_id', $this->serviceRequest->id)
            ->findOrFail($bidId);

        $this->authorize('accept', $bid);

        $serviceBidService->accept($bid);

        $this->serviceRequest->refresh();

        $this->dispatch('toast', [['message' => __('Orden de Trabajo creada. Tú y el proveedor pueden verla en sus paneles.'), 'type' => 'success']]);
    }

    public function render()
    {
        return view('livewire.client.service-requests.show')
            ->layout('layouts.app', ['title' => __('Solicitud')]);
    }
}

