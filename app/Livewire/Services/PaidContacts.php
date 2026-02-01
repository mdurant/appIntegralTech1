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
     * Servicios cuyo contacto ya compró el usuario (pago aprobado).
     *
     * @return Collection<int, ServiceRequest>
     */
    #[Computed]
    public function paidServiceRequests(): Collection
    {
        return ServiceRequest::query()
            ->where('status', 'published')
            ->whereHas('paymentSimulations', function ($q) {
                $q->where('user_id', auth()->id())
                    ->where('status', 'approved');
            })
            ->with(['category', 'region', 'commune'])
            ->latest('published_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.services.paid-contacts')
            ->layout('layouts.app', ['title' => __('Contactos comprados')]);
    }
}
