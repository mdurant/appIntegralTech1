<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Categorías / Subcategorías') }}</flux:heading>
            <flux:text>{{ __('Mantenedor básico de taxonomía (tipo Habitissimo).') }}</flux:text>
        </div>

        <a href="{{ route('admin.dashboard') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Crear categoría') }}</flux:heading>

        <form wire:submit="create" class="mt-4 grid gap-4 md:grid-cols-4">
            <flux:input wire:model="name" :label="__('Nombre')" type="text" />
            <flux:input wire:model="key" :label="__('Key')" type="text" />
            <flux:select wire:model="parentId" :label="__('Parent (opcional)')" placeholder="{{ __('(Top level)') }}">
                @foreach ($this->allCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->parent_id ? '— '.$cat->name : $cat->name }}</option>
                @endforeach
            </flux:select>
            <flux:input wire:model="sortOrder" :label="__('Orden')" type="number" inputmode="numeric" />

            <div class="md:col-span-4 flex items-center justify-end">
                <flux:button variant="primary" type="submit">{{ __('Crear') }}</flux:button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Listado') }}</flux:heading>

        <div class="mt-4 space-y-3">
            @forelse ($this->topCategories as $top)
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <flux:heading size="sm">{{ $top->name }}</flux:heading>
                            <flux:text class="text-sm">{{ $top->key }}</flux:text>
                        </div>
                        <flux:button variant="danger" wire:click="delete({{ $top->id }})">
                            {{ __('Eliminar') }}
                        </flux:button>
                    </div>

                    @if ($top->children->count())
                        <div class="mt-4 space-y-2">
                            @foreach ($top->children as $child)
                                <div class="flex items-center justify-between gap-4 rounded-md border border-zinc-200 p-3 dark:border-zinc-700">
                                    <div>
                                        <flux:text class="text-sm font-medium">{{ $child->name }}</flux:text>
                                        <flux:text class="text-sm">{{ $child->key }}</flux:text>
                                    </div>
                                    <flux:button variant="danger" wire:click="delete({{ $child->id }})">
                                        {{ __('Eliminar') }}
                                    </flux:button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <flux:text>{{ __('No hay categorías.') }}</flux:text>
            @endforelse
        </div>
    </div>
</section>

