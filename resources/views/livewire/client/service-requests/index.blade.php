<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Solicitudes de servicio') }}</flux:heading>
        <flux:text>{{ __('Crea una necesidad (draft) y luego publícala para recibir presupuestos.') }}</flux:text>
    </div>

    <div class="rounded-xl border border-app-border bg-app-surface p-5">
        <flux:heading size="md">{{ __('Nueva solicitud') }}</flux:heading>

        <form wire:submit="create" class="mt-4 space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.live="topCategoryId" class="select2" :label="__('Categoría')" placeholder="{{ __('Selecciona...') }}">
                    @foreach ($this->topCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="subcategoryId" class="select2" :label="__('Subcategoría')" placeholder="{{ __('Selecciona...') }}">
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
                <flux:text class="text-sm font-medium">
                    {{ __('Imágenes (máx 4)') }}
                    @if (count($photos))
                        <span class="font-normal text-app-muted">({{ count($photos) }}/4)</span>
                    @endif
                </flux:text>
                <input
                    type="file"
                    wire:model="photos"
                    multiple
                    accept="image/*"
                    @disabled(count($photos) >= 4)
                    class="block w-full text-sm text-app-text file:mr-4 file:rounded-md file:border-0 file:bg-app-surface-2 file:px-3 file:py-2 file:text-sm file:font-medium file:text-app-text hover:file:bg-brand-50 disabled:opacity-60"
                />
                @error('photos')
                    <flux:text class="text-sm !text-danger">{{ $message }}</flux:text>
                @enderror
                @error('photos.*')
                    <flux:text class="text-sm !text-danger">{{ $message }}</flux:text>
                @enderror

                <div wire:loading wire:target="photos">
                    <flux:text class="text-sm text-app-muted">{{ __('Procesando imágenes…') }}</flux:text>
                </div>

                @if (count($photos))
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                        @foreach ($photos as $idx => $photo)
                            <div
                                wire:key="photo-preview-{{ $idx }}"
                                class="relative overflow-hidden rounded-lg border border-app-border bg-app-surface"
                            >
                                <img
                                    src="{{ $photo->temporaryUrl() }}"
                                    alt=""
                                    class="aspect-square w-full object-cover"
                                />

                                <button
                                    type="button"
                                    wire:click="removePhoto({{ $idx }})"
                                    class="absolute right-2 top-2 rounded-md bg-white/90 px-2 py-1 text-xs font-medium text-app-text shadow-sm ring-1 ring-app-border hover:bg-white"
                                >
                                    {{ __('Quitar') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <flux:text class="text-xs text-app-muted">
                        {{ __('Tip: se mostrarán como miniaturas al publicar la solicitud.') }}
                    </flux:text>
                @endif
            </div>

            @if ($this->formFields->count())
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->formFields as $field)
                        @php($name = 'answers.'.$field->id)

                        <div class="md:col-span-{{ $field->type->value === 'textarea' ? 2 : 1 }}">
                            @if ($field->type->value === 'select')
                                <flux:select wire:model="{{ $name }}" class="select2" :label="$field->label" placeholder="{{ __('Selecciona...') }}">
                                    @foreach ($field->options as $opt)
                                        <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                                    @endforeach
                                </flux:select>
                            @elseif ($field->type->value === 'number')
                                <flux:input wire:model="{{ $name }}" :label="$field->label" type="number" inputmode="numeric" />
                            @elseif ($field->type->value === 'date')
                                <flux:input wire:model="{{ $name }}" class="date-picker" :label="$field->label" type="text" data-date-picker="true" />
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

    <div class="rounded-xl border border-app-border bg-app-surface p-5">
        <flux:heading size="md">{{ __('Mis solicitudes') }}</flux:heading>

        <div class="mt-4 space-y-3">
            @forelse ($this->serviceRequests as $request)
                <div class="flex flex-col gap-3 rounded-lg border border-app-border p-4 md:flex-row md:items-center md:justify-between">
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

