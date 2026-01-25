<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Solicitudes de servicio') }}</flux:heading>
        <flux:text>{{ __('Crea una necesidad (draft) y luego publícala para recibir presupuestos.') }}</flux:text>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Nueva solicitud') }}</flux:heading>

        <form wire:submit="create" class="mt-4 space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.live="topCategoryId" :label="__('Categoría')" placeholder="{{ __('Selecciona...') }}">
                    @foreach ($this->topCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="subcategoryId" :label="__('Subcategoría')" placeholder="{{ __('Selecciona...') }}">
                    @foreach ($this->subcategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <flux:input wire:model="title" :label="__('Trabajo a realizar')" type="text" />
            <flux:textarea wire:model="description" :label="__('Nota del particular')" rows="4" />

            <div class="grid gap-4 md:grid-cols-3">
                <flux:input wire:model="contact_name" :label="__('Nombre de contacto')" type="text" />
                <flux:input wire:model="contact_email" :label="__('Email de contacto')" type="email" />
                <flux:input wire:model="contact_phone" :label="__('Teléfono de contacto')" type="text" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="location_text" :label="__('Localización del trabajo')" type="text" />
                <flux:input wire:model="address" :label="__('Dirección')" type="text" />
            </div>

            <div class="space-y-2">
                <flux:text class="text-sm font-medium">{{ __('Imágenes (máx 4)') }}</flux:text>
                <input
                    type="file"
                    wire:model="photos"
                    multiple
                    accept="image/*"
                    class="block w-full text-sm text-zinc-700 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:text-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-200 dark:hover:file:bg-zinc-700"
                />
                @error('photos')
                    <flux:text class="text-sm !text-red-600 !dark:text-red-400">{{ $message }}</flux:text>
                @enderror
                @error('photos.*')
                    <flux:text class="text-sm !text-red-600 !dark:text-red-400">{{ $message }}</flux:text>
                @enderror
            </div>

            @if ($this->formFields->count())
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->formFields as $field)
                        @php($name = 'answers.'.$field->id)

                        <div class="md:col-span-{{ $field->type->value === 'textarea' ? 2 : 1 }}">
                            @if ($field->type->value === 'select')
                                <flux:select wire:model="{{ $name }}" :label="$field->label" placeholder="{{ __('Selecciona...') }}">
                                    @foreach ($field->options as $opt)
                                        <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                                    @endforeach
                                </flux:select>
                            @elseif ($field->type->value === 'number')
                                <flux:input wire:model="{{ $name }}" :label="$field->label" type="number" inputmode="numeric" />
                            @elseif ($field->type->value === 'date')
                                <flux:input wire:model="{{ $name }}" :label="$field->label" type="date" />
                            @elseif ($field->type->value === 'textarea')
                                <flux:textarea wire:model="{{ $name }}" :label="$field->label" rows="3" />
                            @else
                                <flux:input wire:model="{{ $name }}" :label="$field->label" type="text" />
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit">{{ __('Crear (Draft)') }}</flux:button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="md">{{ __('Mis solicitudes') }}</flux:heading>

        <div class="mt-4 space-y-3">
            @forelse ($this->serviceRequests as $request)
                <div class="flex flex-col gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <flux:heading size="sm">{{ $request->title }}</flux:heading>
                        <flux:text class="text-sm">
                            {{ $request->category?->parent?->name }} / {{ $request->category?->name }}
                            · <span class="font-medium">{{ $request->status->value }}</span>
                        </flux:text>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('client.requests.show', $request) }}" wire:navigate>
                            <flux:button>{{ __('Ver') }}</flux:button>
                        </a>

                        @if ($request->status->value === 'draft')
                            <flux:button wire:click="publish({{ $request->id }})" variant="primary">
                                {{ __('Publicar') }}
                            </flux:button>
                        @endif

                        <flux:button wire:click="delete({{ $request->id }})" variant="danger">
                            {{ __('Eliminar') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <flux:text>{{ __('Aún no tienes solicitudes.') }}</flux:text>
            @endforelse
        </div>
    </div>
</section>

