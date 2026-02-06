<?php

namespace App\Livewire\Services;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PaidContacts extends Component
{
    public function mount(): void
    {
        $user = auth()->user();
        if ($user?->isClient() || $user?->isGuest()) {
            abort(403, __('Solo proveedores pueden ver sus contactos comprados.'));
        }
    }

    /**
     * Solicitudes de cotización cuyo contacto ya compró el usuario (pago aprobado).
     *
     * @return Collection<int, ServiceRequest>
     */
    #[Computed]
    public function paidServiceRequests(): Collection
    {
        $userId = auth()->id();

        return ServiceRequest::query()
            ->where('status', 'published')
            ->whereHas('paymentSimulations', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('status', 'approved');
            })
            ->with([
                'category',
                'region',
                'commune',
                'paymentSimulations' => fn ($q) => $q->where('user_id', $userId)->where('status', 'approved'),
            ])
            ->latest('published_at')
            ->get();
    }

    /**
     * Obtiene el pago (simulación) del usuario actual para una solicitud.
     */
    public function paymentForRequest(ServiceRequest $request): ?\App\Models\PaymentSimulation
    {
        return $request->paymentSimulations
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->first();
    }

    public function render()
    {
        return view('livewire.services.paid-contacts')
            ->layout('layouts.app', ['title' => __('Contactos comprados')]);
    }
}
