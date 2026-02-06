<section class="mx-auto w-full max-w-7xl space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Solicitudes de servicio') }}</flux:heading>
        <flux:text>{{ __('Crea una necesidad (draft) y luego publícala para recibir presupuestos.') }}</flux:text>
    </div>

    {{-- Formulario Nueva solicitud --}}
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

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit">{{ __('Crear (Draft)') }}</flux:button>
            </div>
        </form>
    </div>

    {{-- DataTable Mis Solicitudes --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-5">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <flux:heading size="md">{{ __('Mis solicitudes') }}</flux:heading>

            <form wire:submit="applySearch" class="flex w-full gap-2 sm:max-w-md">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="{{ __('Buscar por título, categoría...') }}"
                    class="min-w-0 flex-1"
                />
                <flux:tooltip :content="__('Buscar')" position="bottom">
                    <flux:button type="submit" variant="primary" size="base" class="!p-2.5" aria-label="{{ __('Buscar') }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </flux:button>
                </flux:tooltip>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-app-border">
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('title')" class="flex items-center gap-2 font-medium text-app-text hover:text-app-muted">
                                {{ __('Título') }}
                                @if ($sortField === 'title')
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
                            <button wire:click="sortBy('category')" class="flex items-center gap-2 font-medium text-app-text hover:text-app-muted">
                                {{ __('Categoría') }}
                                @if ($sortField === 'category')
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
                            <button wire:click="sortBy('status')" class="flex items-center gap-2 font-medium text-app-text hover:text-app-muted">
                                {{ __('Estado') }}
                                @if ($sortField === 'status')
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
                            <button wire:click="sortBy('created_at')" class="flex items-center gap-2 font-medium text-app-text hover:text-app-muted">
                                {{ __('Fecha') }}
                                @if ($sortField === 'created_at')
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
                        <th class="px-4 py-3 text-center">{{ __('PDF') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->serviceRequestsPaginated as $request)
                        <tr class="border-b border-app-border/50 transition-colors hover:bg-app-surface-2/50">
                            <td class="px-4 py-3">
                                <span class="font-medium text-app-text">{{ $request->title }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-app-muted">
                                {{ $request->category?->parent?->name }} / {{ $request->category?->name }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($request->isExpired())
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                        {{ __('Vencida') }}
                                    </span>
                                @else
                                    @php
                                        $statusLabels = [
                                            'draft' => __('Borrador'),
                                            'published' => __('Publicada'),
                                            'closed' => __('Cerrada'),
                                            'awarded' => __('Adjudicada'),
                                            'cancelled' => __('Cancelada'),
                                        ];
                                        $statusColors = [
                                            'draft' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300',
                                            'published' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'closed' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-600 dark:text-zinc-200',
                                            'awarded' => 'bg-brand-100 text-brand-700 dark:bg-brand-900/30 dark:text-brand-300',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        ];
                                        $value = $request->status->value;
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$value] ?? 'bg-zinc-100 text-zinc-700' }}">
                                        {{ $statusLabels[$value] ?? $value }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-app-muted">
                                {{ $request->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <flux:tooltip :content="__('Ver PDF (abre en nueva pestaña)')" position="bottom">
                                    <a href="{{ route('client.requests.pdf', $request) }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700 transition hover:bg-amber-200 dark:bg-amber-900/40 dark:text-amber-400 dark:hover:bg-amber-900/60" aria-label="{{ __('Ver PDF') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                </flux:tooltip>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:tooltip :content="__('Ver')" position="bottom">
                                        <button type="button" wire:click="openViewModal({{ $request->id }})" class="inline-flex size-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 transition hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-400 dark:hover:bg-emerald-900/60" aria-label="{{ __('Ver') }}">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </flux:tooltip>
                                    @if ($request->status->value === 'draft')
                                        <flux:tooltip :content="__('Editar')" position="bottom">
                                            <button type="button" wire:click="openEditConfirmModal({{ $request->id }}, @js($request->title))" class="inline-flex size-8 items-center justify-center rounded-lg bg-violet-100 text-violet-700 transition hover:bg-violet-200 dark:bg-violet-900/40 dark:text-violet-400 dark:hover:bg-violet-900/60" aria-label="{{ __('Editar') }}">
                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        </flux:tooltip>
                                        <flux:tooltip :content="__('Publicar')" position="bottom">
                                            <button type="button" wire:click="openPublishConfirmModal({{ $request->id }}, @js($request->title))" class="inline-flex size-8 items-center justify-center rounded-lg bg-green-100 text-green-700 transition hover:bg-green-200 dark:bg-green-900/40 dark:text-green-400 dark:hover:bg-green-900/60" aria-label="{{ __('Publicar') }}">
                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </button>
                                        </flux:tooltip>
                                    @endif
                                    @if (!$request->awarded_bid_id)
                                        <flux:tooltip :content="__('Eliminar')" position="bottom">
                                            <button type="button" wire:click="confirmDelete({{ $request->id }})" class="inline-flex size-8 items-center justify-center rounded-lg bg-red-100 text-red-700 transition hover:bg-red-200 dark:bg-red-900/40 dark:text-red-400 dark:hover:bg-red-900/60" aria-label="{{ __('Eliminar') }}">
                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </flux:tooltip>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-app-muted">
                                {{ __('Aún no tienes solicitudes.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->serviceRequestsPaginated->links() }}
        </div>
    </div>

    {{-- Modal Ver solicitud --}}
    @if ($viewingId !== null && $this->viewRequest)
        @php($req = $this->viewRequest)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @keydown.escape.window="$wire.closeViewModal()">
            <div
                x-data
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.away="$wire.closeViewModal()"
                class="w-full max-w-lg rounded-xl border border-app-border bg-app-surface p-6 shadow-xl"
            >
                <flux:heading size="md">{{ __('Ver solicitud') }}</flux:heading>
                <div class="mt-4 space-y-2 text-sm">
                    <p><span class="font-medium text-app-muted">{{ __('Título') }}:</span> {{ $req->title }}</p>
                    <p><span class="font-medium text-app-muted">{{ __('Categoría') }}:</span> {{ $req->category?->parent?->name }} / {{ $req->category?->name }}</p>
                    <p><span class="font-medium text-app-muted">{{ __('Estado') }}:</span> {{ $req->status->value }}</p>
                    <p><span class="font-medium text-app-muted">{{ __('Fecha') }}:</span> {{ $req->created_at?->format('d/m/Y H:i') }}</p>
                    @if ($req->description || $req->notes)
                        <p class="mt-2"><span class="font-medium text-app-muted">{{ __('Descripción') }}:</span></p>
                        <p class="line-clamp-3 text-app-text">{{ Str::limit($req->notes ?? $req->description, 200) }}</p>
                    @endif
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button wire:click="closeViewModal" variant="ghost">{{ __('Cerrar') }}</flux:button>
                    <a href="{{ route('client.requests.show', $req) }}" wire:navigate>
                        <flux:button variant="primary">{{ __('Ver detalle completo') }}</flux:button>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal confirmar Editar --}}
    @if ($editConfirmId !== null)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @keydown.escape.window="$wire.closeEditConfirmModal()">
            <div
                x-data
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.away="$wire.closeEditConfirmModal()"
                class="w-full max-w-md rounded-xl border border-app-border bg-app-surface p-6 shadow-xl"
            >
                <div class="flex items-center gap-3 text-violet-600 dark:text-violet-400">
                    <svg class="h-10 w-10 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <flux:heading size="md">{{ __('Editar solicitud') }}</flux:heading>
                </div>
                <flux:text class="mt-3">
                    {{ __('¿Desea editar la solicitud') }} <strong>{{ $editConfirmTitle }}</strong>?
                </flux:text>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button wire:click="closeEditConfirmModal" variant="ghost">{{ __('Cancelar') }}</flux:button>
                    <flux:button wire:click="goToEdit" variant="primary">{{ __('Editar') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal confirmar Publicar --}}
    @if ($publishConfirmId !== null)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @keydown.escape.window="$wire.closePublishConfirmModal()">
            <div
                x-data
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.away="$wire.closePublishConfirmModal()"
                class="w-full max-w-md rounded-xl border border-app-border bg-app-surface p-6 shadow-xl"
            >
                <div class="flex items-center gap-3 text-green-600 dark:text-green-400">
                    <svg class="h-10 w-10 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <flux:heading size="md">{{ __('Publicar solicitud') }}</flux:heading>
                </div>
                <flux:text class="mt-3">
                    {{ __('¿Desea publicar la solicitud') }} <strong>{{ $publishConfirmTitle }}</strong>? {{ __('Estará visible para proveedores y podrán enviar presupuestos.') }}
                </flux:text>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button wire:click="closePublishConfirmModal" variant="ghost">{{ __('Cancelar') }}</flux:button>
                    <flux:button wire:click="confirmPublish" variant="primary">{{ __('Publicar') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal solicitud creada --}}
    @if ($showCreatedModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @keydown.escape.window="$wire.closeCreatedModal()">
            <div
                x-data
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.away="$wire.closeCreatedModal()"
                class="w-full max-w-md rounded-xl border border-app-border bg-app-surface p-6 shadow-xl"
            >
                <div class="flex items-center gap-3 text-green-600 dark:text-green-400">
                    <svg class="h-10 w-10 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <flux:heading size="md">{{ __('Solicitud creada') }}</flux:heading>
                </div>
                <flux:text class="mt-3">
                    {{ __('La solicitud ha sido creada correctamente y está en estado borrador. Puedes editarla, publicarla o ver el PDF desde la tabla.') }}
                </flux:text>
                <div class="mt-6 flex justify-end">
                    <flux:button wire:click="closeCreatedModal" variant="primary">{{ __('Cerrar') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal confirmación eliminar --}}
    @if ($deletingId !== null)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @keydown.escape.window="$wire.closeDeleteModal()">
        <div
            x-data
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            @click.away="$wire.closeDeleteModal()"
            class="w-full max-w-md rounded-xl border border-app-border bg-app-surface p-6 shadow-xl"
        >
            <flux:heading size="md" class="text-danger">{{ __('Confirmar eliminación') }}</flux:heading>
            <flux:text class="mt-2">
                {{ __('¿Desea eliminar la solicitud') }} <strong>{{ $deletingTitle }}</strong>?
            </flux:text>
            <flux:text class="mt-2 block text-sm text-app-muted">{{ __('Esta acción no se puede deshacer.') }}</flux:text>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button wire:click="closeDeleteModal" variant="ghost">{{ __('Cancelar') }}</flux:button>
                <flux:button wire:click="delete" variant="danger">{{ __('Eliminar') }}</flux:button>
            </div>
        </div>
    </div>
    @endif
</section>
