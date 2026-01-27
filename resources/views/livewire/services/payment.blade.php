<section class="mx-auto w-full max-w-2xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Procesar Pago') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Completa los datos de tu tarjeta para acceder a los datos de contacto') }}
        </flux:text>
    </div>

    {{-- Resumen del servicio --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-4">
        <flux:heading size="sm" class="mb-2">{{ $serviceRequest->title }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ $serviceRequest->category?->name }}
            · {{ $serviceRequest->location_display }}
        </flux:text>
    </div>

    {{-- Formulario de pago --}}
    <form wire:submit="processPayment" class="space-y-6">
        <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-6">
            <flux:heading size="md">{{ __('Datos de la Tarjeta') }}</flux:heading>

            {{-- Titular --}}
            <flux:input
                wire:model="cardholder_name"
                label="{{ __('Titular de la tarjeta') }}"
                placeholder="Juan Pérez"
                required
            />
            @error('cardholder_name')
                <flux:text class="text-sm text-red-600">{{ $message }}</flux:text>
            @enderror

            {{-- Número de tarjeta --}}
            <div>
                <flux:input
                    wire:model.live="card_number"
                    label="{{ __('Número de tarjeta') }}"
                    placeholder="1234567890123456"
                    type="text"
                    inputmode="numeric"
                    maxlength="16"
                    required
                />
                @error('card_number')
                    <flux:text class="text-sm text-red-600">{{ $message }}</flux:text>
                @enderror
                <flux:text class="mt-1 text-xs text-app-muted">{{ __('Ingresa 16 dígitos sin espacios') }}</flux:text>
            </div>

            {{-- Fecha de expiración y CVV --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <flux:input
                        wire:model="expiry_month"
                        label="{{ __('Mes') }}"
                        placeholder="12"
                        type="text"
                        inputmode="numeric"
                        maxlength="2"
                        required
                    />
                    @error('expiry_month')
                        <flux:text class="text-sm text-red-600">{{ $message }}</flux:text>
                    @enderror
                </div>
                <div class="sm:col-span-1">
                    <flux:input
                        wire:model="expiry_year"
                        label="{{ __('Año') }}"
                        placeholder="2025"
                        type="text"
                        inputmode="numeric"
                        maxlength="4"
                        required
                    />
                    @error('expiry_year')
                        <flux:text class="text-sm text-red-600">{{ $message }}</flux:text>
                    @enderror
                </div>
                <div class="sm:col-span-1">
                    <flux:input
                        wire:model="cvv"
                        label="{{ __('CVV') }}"
                        placeholder="123"
                        type="text"
                        inputmode="numeric"
                        maxlength="4"
                        required
                    />
                    @error('cvv')
                        <flux:text class="text-sm text-red-600">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <flux:input
                wire:model="email"
                label="{{ __('Correo electrónico') }}"
                type="email"
                required
            />
            @error('email')
                <flux:text class="text-sm text-red-600">{{ $message }}</flux:text>
            @enderror
        </div>

        {{-- Monto a pagar --}}
        <div class="rounded-xl border-2 border-brand-600 bg-brand-50 p-6 dark:bg-brand-950">
            <div class="flex items-center justify-between">
                <flux:text class="text-sm font-medium">{{ __('Monto a pagar') }}</flux:text>
                <flux:text class="text-2xl font-bold text-brand-700">
                    ${{ \App\Helpers\ChileanDataHelper::formatChileanCurrency($amount) }} CLP
                </flux:text>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('services.show', $serviceRequest) }}" wire:navigate>
                <flux:button variant="ghost">{{ __('Cancelar') }}</flux:button>
            </a>
            <flux:button variant="primary" type="submit" class="min-h-[44px] px-6 py-2.5">
                {{ __('Pagar') }}
            </flux:button>
        </div>
    </form>

    {{-- Nota de seguridad --}}
    <div class="rounded-lg border border-app-border bg-app-surface-2 p-4">
        <flux:text class="text-xs text-app-muted">
            {{ __('Este es un pago simulado. No se procesará ningún cargo real. Los datos ingresados son solo para demostración.') }}
        </flux:text>
    </div>
</section>
