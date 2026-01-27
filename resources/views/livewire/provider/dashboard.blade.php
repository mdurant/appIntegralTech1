<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Dashboard Profesional') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Gestiona tus cotizaciones, visualiza tu rendimiento y configura tu perfil profesional') }}
        </flux:text>
    </div>

    {{-- Métricas principales --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->totalBids }}</div>
            <div class="text-sm text-app-muted">{{ __('Cotizaciones Enviadas') }}</div>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->acceptedBids }}</div>
            <div class="text-sm text-app-muted">{{ __('Cotizaciones Aceptadas') }}</div>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->conversionRate }}%</div>
            <div class="text-sm text-app-muted">{{ __('Tasa de Conversión') }}</div>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->workOrders->count() }}</div>
            <div class="text-sm text-app-muted">{{ __('Órdenes de Trabajo') }}</div>
        </div>
    </div>

    {{-- Comparativa Monto Ofertado vs Real --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <flux:heading size="md" class="mb-4">{{ __('Análisis Financiero') }}</flux:heading>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <flux:text class="text-sm text-app-muted">{{ __('Total Ofertado') }}</flux:text>
                <flux:text class="text-xl font-bold">${{ \App\Helpers\ChileanDataHelper::formatChileanCurrency($this->totalOfferedAmount) }} CLP</flux:text>
            </div>
            <div>
                <flux:text class="text-sm text-app-muted">{{ __('Total Real (OTs completadas)') }}</flux:text>
                <flux:text class="text-xl font-bold">${{ \App\Helpers\ChileanDataHelper::formatChileanCurrency($this->totalFinalAmount) }} CLP</flux:text>
            </div>
        </div>
        <div class="mt-4 h-48 flex items-center justify-center text-app-muted border-t border-app-border pt-4">
            <flux:text>{{ __('Gráfico Monto Ofertado vs Real - Se implementará con Chart.js') }}</flux:text>
        </div>
    </div>

    {{-- Listado de OTs --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <flux:heading size="md" class="mb-4">{{ __('Mis Órdenes de Trabajo') }}</flux:heading>
        <div class="space-y-3">
            @forelse ($this->workOrders as $workOrder)
                <div class="rounded-lg border border-app-border p-4">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <flux:heading size="sm">{{ $workOrder->serviceRequest->title }}</flux:heading>
                            <flux:text class="text-sm text-app-muted">
                                {{ $workOrder->serviceRequest->category?->name }}
                                · {{ __('Estado') }}: {{ $workOrder->status->value }}
                            </flux:text>
                            @if ($workOrder->serviceBid)
                                <flux:text class="text-sm">
                                    {{ __('Monto ofertado') }}: ${{ $workOrder->serviceBid->formatted_amount }} CLP
                                </flux:text>
                            @endif
                        </div>
                        <a href="{{ route('services.show', $workOrder->serviceRequest) }}" wire:navigate>
                            <flux:button variant="ghost" size="sm">{{ __('Ver') }}</flux:button>
                        </a>
                    </div>
                </div>
            @empty
                <flux:text class="text-app-muted">{{ __('No tienes órdenes de trabajo aún.') }}</flux:text>
            @endforelse
        </div>
    </div>
</section>
