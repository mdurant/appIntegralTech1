<?php

namespace App\Livewire\Services;

use App\Models\PaymentSimulation;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ContactDetails extends Component
{
    public ServiceRequest $serviceRequest;

    public function mount(ServiceRequest $serviceRequest): void
    {
        // Verificar autorización usando Gate directamente
        if (! Gate::allows('viewContactDetails', [PaymentSimulation::class, $serviceRequest])) {
            abort(403, __('Debes realizar el pago para ver los datos de contacto.'));
        }

        $this->serviceRequest = $serviceRequest->load([
            'category',
            'region',
            'commune',
            'fieldAnswers.field.options',
            'attachments',
        ]);
    }

    public function render()
    {
        return view('livewire.services.contact-details')
            ->layout('layouts.app', ['title' => __('Datos de Contacto')]);
    }
}
