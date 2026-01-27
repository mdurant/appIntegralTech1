<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Mis Órdenes de Trabajo') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Gestiona todas tus órdenes de trabajo adjudicadas') }}
        </flux:text>
    </div>

    <div class="space-y-4">
        @forelse ($this->workOrders as $workOrder)
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1 space-y-3">
                        <div>
                            <flux:heading size="md">{{ $workOrder->serviceRequest->title }}</flux:heading>
                            <flux:text class="text-sm text-app-muted">
                                {{ $workOrder->serviceRequest->category?->name }}
                                · {{ __('Cliente') }}: {{ $workOrder->tenant->name }}
                            </flux:text>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <flux:text class="text-xs text-app-muted">{{ __('Estado') }}</flux:text>
                                <flux:text class="font-medium">{{ ucfirst($workOrder->status->value) }}</flux:text>
                            </div>
                            @if ($workOrder->serviceBid)
                                <div>
                                    <flux:text class="text-xs text-app-muted">{{ __('Monto Ofertado') }}</flux:text>
                                    <flux:text class="font-medium">${{ $workOrder->serviceBid->formatted_amount }} CLP</flux:text>
                                </div>
                            @endif
                            @if ($workOrder->final_price)
                                <div>
                                    <flux:text class="text-xs text-app-muted">{{ __('Precio Final') }}</flux:text>
                                    <flux:text class="font-medium">${{ $workOrder->formatted_final_price }} CLP</flux:text>
                                </div>
                            @endif
                            @if ($workOrder->started_at)
                                <div>
                                    <flux:text class="text-xs text-app-muted">{{ __('Iniciada') }}</flux:text>
                                    <flux:text class="font-medium">{{ $workOrder->started_at->format('d/m/Y') }}</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="ml-4 flex gap-2">
                        <a href="{{ route('provider.work-orders.show', $workOrder) }}" wire:navigate>
                            <flux:button variant="primary" size="sm">{{ __('Ver Detalle') }}</flux:button>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-app-border bg-app-surface p-12 text-center">
                <flux:text class="text-app-muted">{{ __('No tienes órdenes de trabajo aún.') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
