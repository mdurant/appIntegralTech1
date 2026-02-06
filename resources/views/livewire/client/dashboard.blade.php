<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Dashboard Cliente') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Gestiona tus órdenes de trabajo y visualiza tus gastos por categoría') }}
        </flux:text>
    </div>

    {{-- Propuestas económicas recibidas (destacado) --}}
    @if ($this->requestsWithNewBids->isNotEmpty())
        <div class="rounded-xl border-2 border-brand-200 bg-brand-50/50 p-6 dark:border-brand-700 dark:bg-brand-900/20">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-brand-600 text-white">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <flux:heading size="md">{{ __('Has recibido propuesta(s) económica(s)') }}</flux:heading>
                        <flux:text class="text-sm text-app-muted">
                            {{ __('Revisa y adjudica para crear la Orden de Trabajo') }}
                        </flux:text>
                    </div>
                </div>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ($this->requestsWithNewBids as $request)
                    <a href="{{ route('client.requests.show', $request) }}" wire:navigate class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-brand-200 bg-white p-4 transition hover:border-brand-400 hover:shadow-sm dark:border-brand-700 dark:bg-brand-900/30 dark:hover:border-brand-500">
                        <div class="min-w-0 flex-1">
                            <flux:text class="font-medium">{{ $request->title }}</flux:text>
                            <flux:text class="text-xs text-app-muted">
                                {{ $request->category?->name }}
                                @if ($request->reference_id)
                                    · {{ $request->reference_id }}
                                @endif
                                · {{ $request->bids->count() }} {{ $request->bids->count() === 1 ? __('propuesta') : __('propuestas') }}
                            </flux:text>
                        </div>
                        <flux:button variant="primary" size="sm">{{ __('Ver y adjudicar') }}</flux:button>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

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
