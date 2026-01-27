<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Gráficos de Órdenes de Trabajo') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Visualiza estadísticas y tendencias de tus órdenes de trabajo') }}
        </flux:text>
    </div>

    {{-- Métricas Principales --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('Total de OTs') }}</flux:text>
            <flux:text class="text-2xl font-bold">{{ array_sum($this->workOrdersByStatus) }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('Ingresos Totales') }}</flux:text>
            <flux:text class="text-2xl font-bold">${{ \App\Helpers\ChileanDataHelper::formatChileanCurrency($this->totalRevenue) }} CLP</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('Valoración Promedio') }}</flux:text>
            <flux:text class="text-2xl font-bold">{{ $this->averageRating > 0 ? number_format($this->averageRating, 1) : '—' }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('OTs Completadas') }}</flux:text>
            <flux:text class="text-2xl font-bold">{{ $this->workOrdersByStatus['completed'] ?? 0 }}</flux:text>
        </div>
    </div>

    {{-- Gráfico por Estado --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <flux:heading size="md" class="mb-4">{{ __('Distribución por Estado') }}</flux:heading>
        <div class="h-64 flex items-center justify-center text-app-muted">
            <flux:text>{{ __('Gráfico de distribución - Se implementará con Chart.js') }}</flux:text>
        </div>
        <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->workOrdersByStatus as $status => $count)
                <div class="flex items-center justify-between rounded-lg border border-app-border p-3">
                    <flux:text class="text-sm">{{ ucfirst($status) }}</flux:text>
                    <flux:text class="font-bold">{{ $count }}</flux:text>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Gráfico por Mes --}}
    @if (count($this->workOrdersByMonth) > 0)
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:heading size="md" class="mb-4">{{ __('Órdenes de Trabajo por Mes') }}</flux:heading>
            <div class="h-64 flex items-center justify-center text-app-muted">
                <flux:text>{{ __('Gráfico temporal - Se implementará con Chart.js') }}</flux:text>
            </div>
            <div class="mt-4 space-y-2">
                @foreach ($this->workOrdersByMonth as $month => $count)
                    <div class="flex items-center justify-between rounded-lg border border-app-border p-3">
                        <flux:text class="text-sm">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</flux:text>
                        <flux:text class="font-bold">{{ $count }}</flux:text>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
