<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Organizaciones') }}</flux:heading>
            <flux:text>{{ __('Listado y eliminación básica.') }}</flux:text>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="space-y-3">
            @forelse ($this->tenants as $tenant)
                <div class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <div>
                        <flux:heading size="sm">{{ $tenant->name }}</flux:heading>
                        <flux:text class="text-sm">{{ $tenant->slug }}</flux:text>
                    </div>
                    <flux:button variant="danger" wire:click="delete({{ $tenant->id }})">
                        {{ __('Eliminar') }}
                    </flux:button>
                </div>
            @empty
                <flux:text>{{ __('No hay organizaciones.') }}</flux:text>
            @endforelse
        </div>
    </div>
</section>

