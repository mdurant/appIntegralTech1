<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Solicitudes') }}</flux:heading>
            <flux:text>{{ __('Listado global y eliminación básica.') }}</flux:text>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="space-y-3">
            @forelse ($this->serviceRequests as $request)
                <div class="flex flex-col gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <flux:heading size="sm">{{ $request->title }}</flux:heading>
                        <flux:text class="text-sm">
                            {{ $request->tenant?->name }} · {{ $request->category?->name }} · {{ $request->status->value }}
                        </flux:text>
                        <flux:text class="text-sm">{{ __('Creador') }}: {{ $request->creator?->email }}</flux:text>
                    </div>
                    <flux:button variant="danger" wire:click="delete({{ $request->id }})">
                        {{ __('Eliminar') }}
                    </flux:button>
                </div>
            @empty
                <flux:text>{{ __('No hay solicitudes.') }}</flux:text>
            @endforelse
        </div>
    </div>
</section>

