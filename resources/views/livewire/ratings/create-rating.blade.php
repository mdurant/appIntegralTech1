<section class="mx-auto w-full max-w-2xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Valorar Orden de Trabajo') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Comparte tu experiencia con este profesional') }}
        </flux:text>
    </div>

    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <flux:heading size="md" class="mb-4">{{ $workOrder->serviceRequest->title }}</flux:heading>

        <form wire:submit="submit" class="space-y-6">
            <div>
                <flux:text class="mb-2 text-sm font-medium">{{ __('Valoración (1-5 estrellas)') }}</flux:text>
                <div class="flex gap-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <button
                            type="button"
                            wire:click="$set('rating', {{ $i }})"
                            class="text-3xl {{ $rating >= $i ? 'text-yellow-400' : 'text-app-muted' }} hover:text-yellow-400 transition-colors"
                        >
                            ★
                        </button>
                    @endfor
                </div>
                @error('rating')
                    <flux:text class="mt-1 text-sm text-red-600">{{ $message }}</flux:text>
                @enderror
            </div>

            <flux:textarea
                wire:model="comment"
                label="{{ __('Comentario (opcional)') }}"
                rows="4"
                placeholder="{{ __('Comparte tu experiencia con este servicio...') }}"
            />

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('client.requests.show', $workOrder->serviceRequest) }}" wire:navigate>
                    <flux:button variant="ghost">{{ __('Cancelar') }}</flux:button>
                </a>
                <flux:button variant="primary" type="submit" class="min-h-[44px] px-6 py-2.5">
                    {{ __('Enviar Valoración') }}
                </flux:button>
            </div>
        </form>
    </div>
</section>
