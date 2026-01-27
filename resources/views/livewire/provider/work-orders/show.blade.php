<section class="mx-auto w-full max-w-4xl space-y-6 px-4 py-6 sm:px-6">
    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    <div class="flex items-center justify-between">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Detalle de Orden de Trabajo') }}</flux:heading>
            <flux:text class="text-sm text-app-muted">
                {{ __('Información completa de la orden de trabajo') }}
            </flux:text>
        </div>
        <a href="{{ route('provider.work-orders.index') }}" wire:navigate>
            <flux:button variant="ghost" size="sm">{{ __('Volver') }}</flux:button>
        </a>
    </div>

    {{-- Información Principal --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-6">
        <div>
            <flux:heading size="md">{{ $workOrder->serviceRequest->title }}</flux:heading>
            <flux:text class="text-sm text-app-muted mt-1">
                {{ $workOrder->serviceRequest->category?->name }}
            </flux:text>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <flux:text class="text-xs text-app-muted">{{ __('Cliente') }}</flux:text>
                <flux:text class="font-medium">{{ $workOrder->tenant->name }}</flux:text>
            </div>
            <div>
                <flux:text class="text-xs text-app-muted">{{ __('Estado') }}</flux:text>
                <flux:text class="font-medium">{{ ucfirst($workOrder->status->value) }}</flux:text>
            </div>
            @if ($workOrder->serviceRequest->region)
                <div>
                    <flux:text class="text-xs text-app-muted">{{ __('Región') }}</flux:text>
                    <flux:text class="font-medium">{{ $workOrder->serviceRequest->region->name }}</flux:text>
                </div>
            @endif
            @if ($workOrder->serviceRequest->commune)
                <div>
                    <flux:text class="text-xs text-app-muted">{{ __('Comuna') }}</flux:text>
                    <flux:text class="font-medium">{{ $workOrder->serviceRequest->commune->name }}</flux:text>
                </div>
            @endif
            @if ($workOrder->serviceRequest->address)
                <div class="sm:col-span-2">
                    <flux:text class="text-xs text-app-muted">{{ __('Dirección') }}</flux:text>
                    <flux:text class="font-medium">{{ $workOrder->serviceRequest->address }}</flux:text>
                </div>
            @endif
        </div>

        @if ($workOrder->serviceRequest->description)
            <div>
                <flux:text class="text-xs text-app-muted mb-2">{{ __('Descripción') }}</flux:text>
                <flux:text class="text-sm">{{ $workOrder->serviceRequest->description }}</flux:text>
            </div>
        @endif
    </div>

    {{-- Información Financiera --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
        <flux:heading size="md">{{ __('Información Financiera') }}</flux:heading>
        <div class="grid gap-4 sm:grid-cols-2">
            @if ($workOrder->serviceBid)
                <div>
                    <flux:text class="text-xs text-app-muted">{{ __('Monto Ofertado') }}</flux:text>
                    <flux:text class="text-lg font-bold">${{ $workOrder->serviceBid->formatted_amount }} CLP</flux:text>
                </div>
            @endif
            @if ($workOrder->budget_estimated)
                <div>
                    <flux:text class="text-xs text-app-muted">{{ __('Presupuesto Estimado del Cliente') }}</flux:text>
                    <flux:text class="text-lg font-bold">${{ $workOrder->formatted_budget_estimated }} CLP</flux:text>
                </div>
            @endif
            <div>
                <flux:text class="text-xs text-app-muted">{{ __('Precio Final') }}</flux:text>
                @if ($workOrder->final_price)
                    <div class="space-y-2">
                        <flux:text class="text-lg font-bold">${{ $workOrder->formatted_final_price }} CLP</flux:text>
                        <form wire:submit="updateFinalPrice" class="flex gap-2">
                            <flux:input
                                wire:model="price"
                                type="number"
                                placeholder="{{ $workOrder->final_price }}"
                                class="flex-1"
                                step="1"
                            />
                            <flux:button type="submit" size="sm">{{ __('Actualizar') }}</flux:button>
                        </form>
                    </div>
                @else
                    <form wire:submit="updateFinalPrice" class="mt-2">
                        <div class="flex gap-2">
                            <flux:input
                                wire:model="price"
                                type="number"
                                placeholder="0"
                                class="flex-1"
                                step="1"
                            />
                            <flux:button type="submit" size="sm">{{ __('Establecer') }}</flux:button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Fechas Importantes --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
        <flux:heading size="md">{{ __('Fechas Importantes') }}</flux:heading>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <flux:text class="text-xs text-app-muted">{{ __('Creada') }}</flux:text>
                <flux:text class="font-medium">{{ $workOrder->created_at->format('d/m/Y H:i') }}</flux:text>
            </div>
            @if ($workOrder->started_at)
                <div>
                    <flux:text class="text-xs text-app-muted">{{ __('Iniciada') }}</flux:text>
                    <flux:text class="font-medium">{{ $workOrder->started_at->format('d/m/Y H:i') }}</flux:text>
                </div>
            @endif
            @if ($workOrder->completed_at)
                <div>
                    <flux:text class="text-xs text-app-muted">{{ __('Completada') }}</flux:text>
                    <flux:text class="font-medium">{{ $workOrder->completed_at->format('d/m/Y H:i') }}</flux:text>
                </div>
            @endif
            @if ($workOrder->paid_at)
                <div>
                    <flux:text class="text-xs text-app-muted">{{ __('Pagada') }}</flux:text>
                    <flux:text class="font-medium">{{ $workOrder->paid_at->format('d/m/Y H:i') }}</flux:text>
                </div>
            @endif
        </div>
    </div>

    {{-- Acciones --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
        <flux:heading size="md">{{ __('Acciones') }}</flux:heading>
        <div class="flex flex-wrap gap-2">
            @if ($workOrder->status->value === 'open')
                <flux:button wire:click="markAsStarted" variant="primary" size="sm">
                    {{ __('Marcar como Iniciada') }}
                </flux:button>
            @endif
            @if ($workOrder->status->value === 'in_progress')
                <flux:button wire:click="markAsCompleted" variant="primary" size="sm">
                    {{ __('Marcar como Completada') }}
                </flux:button>
            @endif
        </div>
    </div>

    {{-- Imágenes --}}
    @if ($workOrder->serviceRequest->attachments->isNotEmpty())
        <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
            <flux:heading size="md">{{ __('Imágenes del Servicio') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($workOrder->serviceRequest->attachments as $attachment)
                    <a href="{{ route('attachments.show', $attachment) }}" target="_blank" class="block">
                        <img
                            src="{{ route('attachments.show', $attachment) }}"
                            alt="Imagen {{ $loop->iteration }}"
                            class="h-32 w-full rounded-lg object-cover border border-app-border"
                        >
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Valoraciones --}}
    @if ($workOrder->ratings->isNotEmpty())
        <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-4">
            <flux:heading size="md">{{ __('Valoraciones') }}</flux:heading>
            <div class="space-y-3">
                @foreach ($workOrder->ratings as $rating)
                    <div class="border-b border-app-border pb-3 last:border-0">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="flex">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="text-yellow-400">{{ $i <= $rating->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <flux:text class="text-sm text-app-muted">{{ $rating->user->name }}</flux:text>
                        </div>
                        @if ($rating->comment)
                            <flux:text class="text-sm">{{ $rating->comment }}</flux:text>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
