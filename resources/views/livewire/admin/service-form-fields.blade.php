<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Formulario dinámico') }}</flux:heading>
            <flux:text>{{ __('Mantenedor básico de preguntas por subcategoría.') }}</flux:text>
        </div>

        <a href="{{ route('admin.dashboard') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid gap-4 md:grid-cols-3 md:items-end">
            <flux:select wire:model.live="subcategoryId" :label="__('Subcategoría')" placeholder="{{ __('Selecciona...') }}">
                @foreach ($this->subcategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->parent?->name }} / {{ $cat->name }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Crear campo') }}</flux:heading>

        <form wire:submit="createField" class="mt-4 grid gap-4 md:grid-cols-6">
            <flux:input wire:model="key" :label="__('Key')" type="text" class="md:col-span-2" />
            <flux:input wire:model="label" :label="__('Label')" type="text" class="md:col-span-2" />
            <flux:select wire:model="type" :label="__('Tipo')" class="md:col-span-1">
                @foreach ($types as $t)
                    <option value="{{ $t->value }}">{{ $t->value }}</option>
                @endforeach
            </flux:select>
            <flux:input wire:model="sortOrder" :label="__('Orden')" type="number" inputmode="numeric" class="md:col-span-1" />

            <div class="md:col-span-6 flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                    <input type="checkbox" wire:model="required" class="rounded border-zinc-300 dark:border-zinc-700" />
                    <span>{{ __('Requerido') }}</span>
                </label>

                <flux:button variant="primary" type="submit" :disabled="!$subcategoryId">
                    {{ __('Crear') }}
                </flux:button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Campos') }}</flux:heading>

        <div class="mt-4 space-y-3">
            @forelse ($this->fields as $field)
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <flux:heading size="sm">{{ $field->label }}</flux:heading>
                            <flux:text class="text-sm">
                                {{ $field->key }} · {{ $field->type->value }} · {{ $field->required ? __('requerido') : __('opcional') }}
                            </flux:text>
                        </div>
                        <flux:button variant="danger" wire:click="deleteField({{ $field->id }})">
                            {{ __('Eliminar') }}
                        </flux:button>
                    </div>

                    @if ($field->type->value === 'select')
                        <div class="mt-4 space-y-3">
                            <flux:text class="text-sm font-medium">{{ __('Opciones') }}</flux:text>

                            <div class="grid gap-3 md:grid-cols-3 md:items-end">
                                <flux:input wire:model="optionValue" :label="__('Value')" type="text" />
                                <flux:input wire:model="optionLabel" :label="__('Label')" type="text" />
                                <flux:button wire:click="addOption({{ $field->id }})" variant="primary">
                                    {{ __('Agregar') }}
                                </flux:button>
                            </div>

                            <div class="space-y-2">
                                @foreach ($field->options as $opt)
                                    <div class="flex items-center justify-between gap-4 rounded-md border border-zinc-200 p-3 dark:border-zinc-700">
                                        <flux:text class="text-sm">{{ $opt->label }} ({{ $opt->value }})</flux:text>
                                        <flux:button variant="danger" wire:click="deleteOption({{ $opt->id }})">
                                            {{ __('Eliminar') }}
                                        </flux:button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <flux:text>{{ __('Selecciona una subcategoría para ver/crear campos.') }}</flux:text>
            @endforelse
        </div>
    </div>
</section>

