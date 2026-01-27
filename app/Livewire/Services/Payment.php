<?php

namespace App\Livewire\Services;

use App\Models\PaymentSimulation;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Payment extends Component
{
    public ServiceRequest $serviceRequest;

    public string $cardholder_name = '';

    public string $card_number = '';

    public string $expiry_month = '';

    public string $expiry_year = '';

    public string $cvv = '';

    public string $email = '';

    public float $amount = 6105.0; // Monto fijo por ahora

    public function mount(ServiceRequest $serviceRequest): void
    {
        // Verificar autorización usando Gate directamente
        if (! Gate::allows('create', [PaymentSimulation::class, $serviceRequest])) {
            abort(403, __('No tienes permiso para realizar el pago.'));
        }

        // Verificar que no haya pagado ya
        $existingPayment = PaymentSimulation::where('user_id', auth()->id())
            ->where('service_request_id', $serviceRequest->id)
            ->where('status', 'approved')
            ->first();

        if ($existingPayment) {
            $this->redirect(route('services.contact', $serviceRequest), navigate: true);
        }

        $this->serviceRequest = $serviceRequest->load(['category', 'region', 'commune']);
        $this->email = auth()->user()->email;
    }

    public function updatedCardNumber(): void
    {
        // Limpiar espacios y caracteres no numéricos
        $this->card_number = preg_replace('/\D/', '', $this->card_number);
    }

    public function processPayment(): void
    {
        // Limpiar el número de tarjeta antes de validar
        $this->card_number = preg_replace('/\D/', '', $this->card_number);

        $this->validate([
            'cardholder_name' => ['required', 'string', 'min:3', 'max:255'],
            'card_number' => ['required', 'string', 'regex:/^\d{16}$/', 'size:16'],
            'expiry_month' => ['required', 'string', 'size:2', 'regex:/^(0[1-9]|1[0-2])$/'],
            'expiry_year' => ['required', 'string', 'size:4'],
            'cvv' => ['required', 'string', 'regex:/^\d{3,4}$/', 'min:3', 'max:4'],
            'email' => ['required', 'email', 'max:255'],
        ], [
            'card_number.regex' => __('El número de tarjeta debe tener 16 dígitos.'),
            'card_number.size' => __('El número de tarjeta debe tener exactamente 16 dígitos.'),
            'expiry_month.regex' => __('El mes debe ser entre 01 y 12.'),
            'expiry_year.size' => __('El año debe tener 4 dígitos.'),
            'cvv.regex' => __('El CVV debe tener 3 o 4 dígitos.'),
        ]);

        // Validar que la fecha no esté expirada
        $expiryDate = Carbon::createFromDate((int) $this->expiry_year, (int) $this->expiry_month, 1)->endOfMonth();
        if ($expiryDate->isPast()) {
            $this->addError('expiry_month', __('La tarjeta ha expirado.'));
            $this->addError('expiry_year', __('La tarjeta ha expirado.'));

            return;
        }

        // Simular procesamiento de pago (siempre exitoso en simulación)
        $payment = PaymentSimulation::create([
            'user_id' => auth()->id(),
            'service_request_id' => $this->serviceRequest->id,
            'amount' => $this->amount,
            'card_last_four' => substr($this->card_number, -4),
            'cardholder_name' => $this->cardholder_name,
            'status' => 'approved',
            'paid_at' => now(),
        ]);

        session()->flash('payment-success', __('Pago procesado exitosamente. Ahora puedes ver los datos de contacto.'));

        $this->redirect(route('services.contact', $this->serviceRequest), navigate: true);
    }

    public function render()
    {
        return view('livewire.services.payment')
            ->layout('layouts.app', ['title' => __('Procesar Pago')]);
    }
}
