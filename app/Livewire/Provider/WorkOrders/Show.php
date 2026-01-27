<?php

namespace App\Livewire\Provider\WorkOrders;

use App\Models\WorkOrder;
use App\WorkOrderStatus;
use Livewire\Component;

class Show extends Component
{
    public WorkOrder $workOrder;

    public string $price = '';

    public function mount(WorkOrder $workOrder): void
    {
        // Verificar que la OT pertenezca al usuario autenticado
        abort_unless($workOrder->awarded_to_user_id === auth()->id(), 403);

        $this->workOrder = $workOrder->load([
            'serviceRequest.category',
            'serviceRequest.region',
            'serviceRequest.commune',
            'serviceRequest.attachments',
            'serviceBid',
            'tenant',
            'ratings.user',
        ]);

        $this->price = $workOrder->final_price ? (string) $workOrder->final_price : '';
    }

    public function updateFinalPrice(): void
    {
        $this->validate([
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->workOrder->update([
            'final_price' => (float) $this->price,
        ]);

        $this->workOrder->refresh();

        session()->flash('message', __('Precio final actualizado exitosamente.'));
    }

    public function markAsStarted(): void
    {
        $this->workOrder->update([
            'status' => WorkOrderStatus::InProgress,
            'started_at' => now(),
        ]);

        $this->workOrder->refresh();

        session()->flash('message', __('Orden de trabajo marcada como iniciada.'));
    }

    public function markAsCompleted(): void
    {
        $this->workOrder->update([
            'status' => WorkOrderStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->workOrder->refresh();

        session()->flash('message', __('Orden de trabajo marcada como completada.'));
    }

    public function render()
    {
        return view('livewire.provider.work-orders.show')
            ->layout('layouts.app', ['title' => __('Detalle de Orden de Trabajo')]);
    }
}
