<section class="mx-auto w-full max-w-4xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Solicitud') }}</flux:heading>
            <flux:text class="text-sm">
                {{ $serviceRequest->category?->parent?->name }} / {{ $serviceRequest->category?->name }}
                · <span class="font-medium">{{ $serviceRequest->status->value }}</span>
                @if ($serviceRequest->expires_at)
                    · {{ __('Vence') }}: <span class="font-medium">{{ $serviceRequest->expires_at->format('Y-m-d') }}</span>
                @endif
            </flux:text>
        </div>

        <a href="{{ route('client.requests.index') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ $serviceRequest->title }}</flux:heading>
        <flux:text class="mt-3 whitespace-pre-line">{{ $serviceRequest->notes ?? $serviceRequest->description }}</flux:text>
    </div>

    @if ($serviceRequest->status->value === 'published' && $serviceRequest->expires_at && $serviceRequest->expires_at->isPast())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/30">
            <flux:heading size="md">{{ __('Solicitud vencida') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Puedes reabrirla por 15 días adicionales.') }}</flux:text>
            <div class="mt-4">
                <flux:button variant="primary" wire:click="reopen">{{ __('Reabrir por 15 días') }}</flux:button>
            </div>
        </div>
    @endif

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Presupuestos recibidos') }}</flux:heading>
        <div class="mt-4 space-y-3">
            @forelse ($this->bids as $bid)
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text class="text-sm font-medium">
                        {{ $bid->user?->name }} · ${{ $bid->formatted_amount }} {{ $bid->currency }}
                        @if ($bid->valid_until)
                            · {{ __('Vigencia') }}: {{ $bid->valid_until->format('Y-m-d') }}
                        @endif
                    </flux:text>
                    <flux:text class="mt-1 text-sm">{{ __('Estado') }}: {{ $bid->status->value }}</flux:text>
                    @if ($bid->message)
                        <flux:text class="mt-2 text-sm whitespace-pre-line">{{ $bid->message }}</flux:text>
                    @endif

                    @if ($serviceRequest->status->value === 'published' && $bid->status->value === 'submitted')
                        <div class="mt-4">
                            <flux:button variant="primary" wire:click="award({{ $bid->id }})">
                                {{ __('Adjudicar y crear OT') }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            @empty
                <flux:text>{{ __('Aún no tienes presupuestos.') }}</flux:text>
            @endforelse
        </div>
    </div>
</section>

