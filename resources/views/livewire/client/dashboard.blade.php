<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Dashboard Cliente') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Gestiona tus órdenes de trabajo y visualiza tus gastos por categoría') }}
        </flux:text>
    </div>

    {{-- Métricas por estado --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->workOrdersByStatus['pending'] }}</div>
            <div class="text-sm text-app-muted">{{ __('Pendientes') }}</div>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->workOrdersByStatus['in_progress'] }}</div>
            <div class="text-sm text-app-muted">{{ __('En Curso') }}</div>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->workOrdersByStatus['completed'] }}</div>
            <div class="text-sm text-app-muted">{{ __('Completadas') }}</div>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <div class="text-2xl font-bold text-app-text">{{ $this->workOrdersByStatus['paid'] }}</div>
            <div class="text-sm text-app-muted">{{ __('Pagadas') }}</div>
        </div>
    </div>

    {{-- Listado de OTs --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <flux:heading size="md" class="mb-4">{{ __('Órdenes de Trabajo') }}</flux:heading>
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
                            @if ($workOrder->final_price)
                                <flux:text class="text-sm font-medium">
                                    {{ __('Precio final') }}: ${{ $workOrder->formatted_final_price }} CLP
                                </flux:text>
                            @endif
                        </div>
                        <a href="{{ route('client.requests.show', $workOrder->serviceRequest) }}" wire:navigate>
                            <flux:button variant="ghost" size="sm">{{ __('Ver') }}</flux:button>
                        </a>
                    </div>
                </div>
            @empty
                <flux:text class="text-app-muted">{{ __('No hay órdenes de trabajo.') }}</flux:text>
            @endforelse
        </div>
    </div>

    {{-- Gráfico de gastos por categoría (placeholder para Chart.js) --}}
    @if (count($this->expensesByCategory) > 0)
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:heading size="md" class="mb-4">{{ __('Distribución de Gastos por Categoría') }}</flux:heading>
            <div class="h-64 flex items-center justify-center text-app-muted">
                <flux:text>{{ __('Gráfico de gastos - Se implementará con Chart.js') }}</flux:text>
            </div>
        </div>
    @endif
</section>
