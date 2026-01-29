<section class="mx-auto w-full max-w-7xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Categorías / Subcategorías') }}</flux:heading>
            <flux:text>{{ __('Mantenedor básico de taxonomía (tipo Habitissimo).') }}</flux:text>
        </div>

        <a href="{{ route('admin.dashboard') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    {{-- Formulario de creación --}}
    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Crear categoría') }}</flux:heading>

        <form wire:submit="create" class="mt-4 grid gap-4 md:grid-cols-2">
            <div class="flex flex-col gap-4">
                <flux:input wire:model="name" :label="__('Nombre')" type="text" />
                <flux:select wire:model="parentId" class="select2" :label="__('Parent (opcional)')" placeholder="{{ __('(Top level)') }}">
                    <option value="">{{ __('(Top level)') }}</option>
                    @foreach ($this->allCategoriesForSelect as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="flex flex-col gap-4">
                <flux:input wire:model="key" :label="__('Key')" type="text" />
                <flux:input wire:model="sortOrder" :label="__('Orden')" type="number" inputmode="numeric" />
            </div>

            <div class="md:col-span-2 flex items-center justify-end">
                <flux:button variant="primary" type="submit">{{ __('Crear') }}</flux:button>
            </div>
        </form>
    </div>

    {{-- Tabla de categorías con búsqueda y paginación --}}
    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <flux:heading size="md">{{ __('Listado de Categorías') }}</flux:heading>

            {{-- Buscador con botón de lupa --}}
            <form wire:submit="applySearch" class="flex w-full gap-2 sm:max-w-md">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="{{ __('Buscar por nombre, key o slug...') }}"
                    class="min-w-0 flex-1"
                />
                <flux:button type="submit" variant="primary" size="base">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    {{ __('') }}
                </flux:button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="px-4 py-3 text-left">
                            <button
                                wire:click="sortBy('reference_code')"
                                class="flex items-center gap-2 font-medium text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100"
                            >
                                {{ __('Código') }}
                                @if ($sortField === 'reference_code')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button
                                wire:click="sortBy('name')"
                                class="flex items-center gap-2 font-medium text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100"
                            >
                                {{ __('Nombre') }}
                                @if ($sortField === 'name')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button
                                wire:click="sortBy('key')"
                                class="flex items-center gap-2 font-medium text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100"
                            >
                                {{ __('Key') }}
                                @if ($sortField === 'key')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button
                                wire:click="sortBy('slug')"
                                class="flex items-center gap-2 font-medium text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100"
                            >
                                {{ __('Slug') }}
                                @if ($sortField === 'slug')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-right">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->categories as $category)
                        <tr class="border-b border-zinc-100 transition-colors hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                #{{ $category->reference_code }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                <code class="rounded bg-zinc-100 px-2 py-1 text-xs dark:bg-zinc-800">
                                    {{ $category->key }}
                                </code>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $category->slug }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button
                                        wire:click="edit({{ $category->id }})"
                                        wire:loading.attr="disabled"
                                        variant="ghost"
                                        size="sm"
                                    >
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        {{ __('') }}
                                    </flux:button>
                                    <flux:button
                                        wire:click="confirmDelete({{ $category->id }})"
                                        wire:loading.attr="disabled"
                                        variant="danger"
                                        size="sm"
                                    >
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No se encontraron categorías.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $this->categories->links() }}
        </div>
    </div>

    {{-- Modal de Edición --}}
    @if ($editingId !== null)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @keydown.escape.window="$wire.closeEditModal()"
    >
        <div
            x-data
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="$wire.closeEditModal()"
            class="w-full max-w-2xl rounded-xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
        >
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="md">{{ __('Modificar Categoría') }}</flux:heading>
                <button
                    wire:click="closeEditModal"
                    class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-300"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="update" class="space-y-4">
                <flux:input wire:model="editName" :label="__('Nombre')" type="text" />
                <flux:input wire:model="editKey" :label="__('Key')" type="text" />
                <flux:select wire:model="editParentId" class="select2" :label="__('Parent (opcional)')" placeholder="{{ __('(Top level)') }}">
                    <option value="">{{ __('(Top level)') }}</option>
                    @foreach ($this->allCategoriesForSelect as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="editSortOrder" :label="__('Orden')" type="number" inputmode="numeric" />

                <div class="flex items-center justify-end gap-3 pt-4">
                    <flux:button wire:click="closeEditModal" variant="ghost" type="button">
                        {{ __('Cancelar') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        {{ __('Guardar Cambios') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal de Confirmación de Eliminación --}}
    @if ($deletingId !== null)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @keydown.escape.window="$wire.closeDeleteModal()"
    >
        <div
            x-data
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="$wire.closeDeleteModal()"
            class="w-full max-w-md rounded-xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
        >
            <div class="mb-4">
                <flux:heading size="md" class="text-danger">{{ __('Confirmar Eliminación') }}</flux:heading>
            </div>

            <div class="mb-6">
                <flux:text>
                    {{ __('¿Desea eliminar el registro') }} <strong class="font-semibold">{{ $deletingName }}</strong>?
                </flux:text>
                <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Esta acción no se puede deshacer.') }}
                </flux:text>
            </div>

            <div class="flex items-center justify-end gap-3">
                <flux:button wire:click="closeDeleteModal" variant="ghost" type="button">
                    {{ __('Cancelar') }}
                </flux:button>
                <flux:button wire:click="delete" variant="danger" type="button">
                    {{ __('Eliminar') }}
                </flux:button>
            </div>
        </div>
    </div>
    @endif
</section>

@script
<script>
    Livewire.hook('morph.updated', ({ el }) => {
        setTimeout(() => {
            if (typeof initializeSelect2 !== 'undefined') {
                initializeSelect2(el);
            }
        }, 100);
    });
</script>
@endscript
