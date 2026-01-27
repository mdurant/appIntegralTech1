<?php

namespace App\Livewire\Services;

use App\Models\PaymentSimulation;
use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\ServiceRequestStatus;
use App\Services\ServiceBidService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public ServiceRequest $serviceRequest;

    public string $amount = '';

    public string $message = '';

    public function mount(ServiceRequest $serviceRequest): void
    {
        $this->serviceRequest = $serviceRequest->load([
            'category',
            'category.parent',
            'tenant',
            'region',
            'commune',
            'fieldAnswers.field.options',
            'attachments',
        ]);

        $this->authorize('view', $this->serviceRequest);
    }

    #[Computed]
    public function hasPayment(): bool
    {
        if (auth()->user()->isClient() || auth()->user()->isGuest()) {
            return false;
        }

        return PaymentSimulation::where('user_id', auth()->id())
            ->where('service_request_id', $this->serviceRequest->id)
            ->where('status', 'approved')
            ->exists();
    }

    #[Computed]
    public function detailRows(): Collection
    {
        $rows = collect();

        foreach ($this->serviceRequest->fieldAnswers as $answer) {
            $field = $answer->field;
            $value = $answer->value;

            if ($field && $field->type?->value === 'select') {
                $label = $field->options->firstWhere('value', $value)?->label;
                $value = $label ?? $value;
            }

            $rows->push([
                'label' => $field?->label ?? 'Campo',
                'value' => (string) $value,
            ]);
        }

        return $rows;
    }

    #[Computed]
    public function attachmentUrls(): array
    {
        return $this->serviceRequest->attachments
            ->map(fn ($a) => route('attachments.show', $a))
            ->all();
    }

    #[Computed]
    public function myBid(): ?ServiceBid
    {
        return ServiceBid::query()
            ->where('service_request_id', $this->serviceRequest->id)
            ->where('user_id', auth()->id())
            ->latest('id')
            ->first();
    }

    #[Computed]
    public function bidsForClient(): Collection
    {
        if (! auth()->user()->isClient()) {
            return collect();
        }

        if (! auth()->user()->belongsToTenant($this->serviceRequest->tenant)) {
            return collect();
        }

        return ServiceBid::query()
            ->where('service_request_id', $this->serviceRequest->id)
            ->with('user')
            ->latest('id')
            ->get();
    }

    public function submit(ServiceBidService $serviceBidService): void
    {
        abort_unless(! auth()->user()->isGuest() && ! auth()->user()->isClient(), 403);
        abort_unless($this->serviceRequest->status === ServiceRequestStatus::Published, 403);

        $validated = $this->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $serviceBidService->submit(
            actor: auth()->user(),
            serviceRequest: $this->serviceRequest,
            amount: (string) (int) $validated['amount'],
            message: $validated['message'] ?? null,
        );

        $this->reset('amount', 'message');
    }

    public function render()
    {
        return view('livewire.services.show')
            ->layout('layouts.app', ['title' => $this->serviceRequest->title]);
    }
}
