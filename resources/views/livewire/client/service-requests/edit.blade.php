@use('Illuminate\Support\Facades\Storage')
<section class="mx-auto w-full max-w-7xl space-y-6">
    <div class="flex items-center justify-between gap-4">
        <flux:heading size="lg">{{ __('Editar solicitud') }}</flux:heading>
        <a href="{{ route('client.requests.show', $serviceRequest) }}" wire:navigate>
            <flux:button variant="ghost">{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-app-border bg-app-surface p-5">
        <form wire:submit="save" class="space-y-4">
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
                <div class="space-y-2">
                    <flux:text class="text-sm font-medium text-app-text">{{ __('Teléfono de contacto') }}</flux:text>
                    <div class="flex gap-2">
                        <flux:select wire:model.live="contact_phone_country" class="select2 w-28 shrink-0" placeholder="{{ __('País') }}" aria-label="{{ __('Código país') }}">
                            <option value="+56">+56 Chile</option>
                            <option value="+54">+54 Argentina</option>
                            <option value="+57">+57 Colombia</option>
                            <option value="+58">+58 Venezuela</option>
                            <option value="+51">+51 Perú</option>
                            <option value="+52">+52 México</option>
                            <option value="+593">+593 Ecuador</option>
                            <option value="+595">+595 Paraguay</option>
                            <option value="+598">+598 Uruguay</option>
                            <option value="+1">+1 USA/Can</option>
                        </flux:select>
                        <flux:input
                            wire:model="contact_phone_number"
                            type="tel"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="9"
                            placeholder="{{ __('9 dígitos') }}"
                            class="min-w-0 flex-1"
                            aria-label="{{ __('Número celular') }}"
                        />
                    </div>
                    @error('contact_phone_number')
                        <flux:text class="text-sm !text-danger">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <flux:select wire:model.live="regionId" class="select2" :label="__('Región')" placeholder="{{ __('Selecciona región...') }}">
                    @foreach ($this->regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="communeId" class="select2" :label="__('Comuna')" placeholder="{{ __('Selecciona comuna...') }}" :disabled="!$regionId">
                    @foreach ($this->communes as $commune)
                        <option value="{{ $commune->id }}">{{ $commune->name }}</option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="address" :label="__('Dirección')" type="text" class="lg:col-span-1" />
            </div>

            <div class="space-y-2">
                <flux:text class="text-sm font-medium">
                    {{ __('Imágenes (máx 4)') }}
                    @php
                        $existingCount = $serviceRequest->attachments->count();
                        $newCount = count($photos);
                        $total = $existingCount + $newCount;
                    @endphp
                    @if ($total > 0)
                        <span class="font-normal text-app-muted">({{ $total }}/4)</span>
                    @endif
                </flux:text>
                @if ($existingCount > 0)
                    <flux:text class="text-xs text-app-muted">{{ __('Imágenes actuales:') }}</flux:text>
                    <div class="flex gap-3 flex-wrap">
                        @foreach ($serviceRequest->attachments as $att)
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg border border-app-border bg-app-surface-2">
                                <img src="{{ Storage::url($att->path) }}" alt="" class="h-full w-full object-cover" />
                            </div>
                        @endforeach
                    </div>
                @endif
                @if ($total < 4)
                    <input
                        type="file"
                        wire:model="photos"
                        multiple
                        accept="image/*"
                        class="block w-full text-sm text-app-text file:mr-4 file:rounded-md file:border-0 file:bg-app-surface-2 file:px-3 file:py-2 file:text-sm file:font-medium file:text-app-text hover:file:bg-brand-50"
                    />
                @endif
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
                                    class="absolute right-2 top-2 rounded-md bg-white/90 px-2 py-1 text-xs font-medium text-app-text shadow-sm ring-1 ring-app-border hover:bg-white dark:bg-app-surface-2"
                                >
                                    {{ __('Quitar') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($this->formFields->count())
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->formFields as $field)
                        @php
                            $name = 'answers.'.$field->id;
                            $colSpan = $field->type->value === 'textarea' ? 2 : 1;
                        @endphp
                        <div class="md:col-span-{{ $colSpan }}">
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

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('client.requests.show', $serviceRequest) }}" wire:navigate>
                    <flux:button type="button" variant="ghost">{{ __('Cancelar') }}</flux:button>
                </a>
                <flux:button variant="primary" type="submit">{{ __('Guardar') }}</flux:button>
            </div>
        </form>
    </div>
</section>
