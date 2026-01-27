<?php

namespace App\Policies;

use App\Models\PaymentSimulation;
use App\Models\ServiceRequest;
use App\Models\User;

class PaymentSimulationPolicy
{
    /**
     * Determine whether the user can create a payment simulation.
     */
    public function create(User $user, ServiceRequest $serviceRequest): bool
    {
        // Solo usuarios no clientes pueden pagar
        if ($user->isClient() || $user->isGuest()) {
            return false;
        }

        // Solo solicitudes publicadas
        if ($serviceRequest->status->value !== 'published') {
            return false;
        }

        // No puede pagar si ya pagó
        return ! PaymentSimulation::where('user_id', $user->id)
            ->where('service_request_id', $serviceRequest->id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Determine whether the user can view contact details.
     */
    public function viewContactDetails(User $user, ServiceRequest $serviceRequest): bool
    {
        // Solo usuarios no clientes pueden ver contactos
        if ($user->isClient() || $user->isGuest()) {
            return false;
        }

        // Debe tener un pago aprobado
        return PaymentSimulation::where('user_id', $user->id)
            ->where('service_request_id', $serviceRequest->id)
            ->where('status', 'approved')
            ->exists();
    }
}
