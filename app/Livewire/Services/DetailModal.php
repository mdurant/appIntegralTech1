<?php

namespace App\Livewire\Services;

use App\Models\ServiceRequest;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DetailModal extends Component
{
    public ?int $serviceRequestId = null;

    public bool $show = false;

    #[Computed]
    public function serviceRequest(): ?ServiceRequest
    {
        if (! $this->serviceRequestId) {
            return null;
        }

        return ServiceRequest::query()
            ->where('id', $this->serviceRequestId)
            ->where('status', 'published')
            ->with([
                'category',
                'category.parent',
                'region',
                'commune',
                'fieldAnswers.field.options',
                'attachments',
            ])
            ->first();
    }

    #[Computed]
    public function detailRows(): Collection
    {
        if (! $this->serviceRequest) {
            return collect();
        }

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

    public function open(int $serviceRequestId): void
    {
        $this->serviceRequestId = $serviceRequestId;
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->serviceRequestId = null;
    }

    public function goToPayment(): void
    {
        if (! $this->serviceRequest) {
            return;
        }

        $this->close();
        $this->redirect(route('services.payment', $this->serviceRequest), navigate: true);
    }

    protected function getListeners(): array
    {
        return [
            'open-detail-modal' => 'open',
        ];
    }

    public function render()
    {
        return view('livewire.services.detail-modal');
    }
}
