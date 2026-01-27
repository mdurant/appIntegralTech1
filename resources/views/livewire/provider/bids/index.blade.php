<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Mis Cotizaciones') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Gestiona todas tus cotizaciones enviadas') }}
        </flux:text>
    </div>

    {{-- Estadísticas --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-app-border bg-app-surface p-4">
            <flux:text class="text-xs text-app-muted">{{ __('Total') }}</flux:text>
            <flux:text class="text-xl font-bold">{{ $this->stats['total'] }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-4">
            <flux:text class="text-xs text-app-muted">{{ __('Enviadas') }}</flux:text>
            <flux:text class="text-xl font-bold">{{ $this->stats['submitted'] }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-4">
            <flux:text class="text-xs text-app-muted">{{ __('Aceptadas') }}</flux:text>
            <flux:text class="text-xl font-bold text-green-600">{{ $this->stats['accepted'] }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-4">
            <flux:text class="text-xs text-app-muted">{{ __('Vencidas') }}</flux:text>
            <flux:text class="text-xl font-bold text-orange-600">{{ $this->stats['expired'] }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-4">
            <flux:text class="text-xs text-app-muted">{{ __('Rechazadas') }}</flux:text>
            <flux:text class="text-xl font-bold text-red-600">{{ $this->stats['rejected'] }}</flux:text>
        </div>
    </div>

    {{-- Filtro --}}
    <div class="flex items-center gap-4">
        <flux:text class="text-sm font-medium">{{ __('Filtrar por estado') }}:</flux:text>
        <div class="flex gap-2">
            <flux:button
                wire:click="$set('statusFilter', 'all')"
                variant="{{ $statusFilter === 'all' ? 'primary' : 'ghost' }}"
                size="sm"
            >
                {{ __('Todas') }}
            </flux:button>
            <flux:button
                wire:click="$set('statusFilter', 'submitted')"
                variant="{{ $statusFilter === 'submitted' ? 'primary' : 'ghost' }}"
                size="sm"
            >
                {{ __('Enviadas') }}
            </flux:button>
            <flux:button
                wire:click="$set('statusFilter', 'accepted')"
                variant="{{ $statusFilter === 'accepted' ? 'primary' : 'ghost' }}"
                size="sm"
            >
                {{ __('Aceptadas') }}
            </flux:button>
            <flux:button
                wire:click="$set('statusFilter', 'expired')"
                variant="{{ $statusFilter === 'expired' ? 'primary' : 'ghost' }}"
                size="sm"
            >
                {{ __('Vencidas') }}
            </flux:button>
        </div>
    </div>

    {{-- Listado de Cotizaciones --}}
    <div class="space-y-4">
        @forelse ($this->bids as $bid)
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1 space-y-3">
                        <div>
                            <flux:heading size="md">{{ $bid->serviceRequest->title }}</flux:heading>
                            <flux:text class="text-sm text-app-muted">
                                {{ $bid->serviceRequest->category?->name }}
                                · {{ __('Cliente') }}: {{ $bid->serviceRequest->tenant->name }}
                            </flux:text>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <flux:text class="text-xs text-app-muted">{{ __('Monto') }}</flux:text>
                                <flux:text class="font-medium">${{ $bid->formatted_amount }} CLP</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-xs text-app-muted">{{ __('Estado') }}</flux:text>
                                <flux:text class="font-medium">{{ ucfirst($bid->status->value) }}</flux:text>
                            </div>
                            @if ($bid->valid_until)
                                <div>
                                    <flux:text class="text-xs text-app-muted">{{ __('Válida hasta') }}</flux:text>
                                    <flux:text class="font-medium {{ $bid->valid_until->isPast() ? 'text-red-600' : '' }}">
                                        {{ $bid->valid_until->format('d/m/Y') }}
                                    </flux:text>
                                </div>
                            @endif
                            <div>
                                <flux:text class="text-xs text-app-muted">{{ __('Enviada') }}</flux:text>
                                <flux:text class="font-medium">{{ $bid->created_at->format('d/m/Y') }}</flux:text>
                            </div>
                        </div>

                        @if ($bid->message)
                            <div>
                                <flux:text class="text-xs text-app-muted mb-1">{{ __('Mensaje') }}</flux:text>
                                <flux:text class="text-sm">{{ $bid->message }}</flux:text>
                            </div>
                        @endif
                    </div>

                    <div class="ml-4">
                        <a href="{{ route('services.show', $bid->serviceRequest) }}" wire:navigate>
                            <flux:button variant="ghost" size="sm">{{ __('Ver Solicitud') }}</flux:button>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-app-border bg-app-surface p-12 text-center">
                <flux:text class="text-app-muted">{{ __('No tienes cotizaciones aún.') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
