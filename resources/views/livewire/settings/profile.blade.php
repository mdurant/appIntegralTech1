<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Configuración de Perfil') }}</flux:heading>

    <x-settings.layout :heading="__('Perfil')" :subheading="__('Actualiza tu información personal y profesional')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            {{-- Avatar/Foto de perfil --}}
            <div class="flex items-center gap-4">
                <div class="size-16 overflow-hidden rounded-full bg-zinc-100 ring-1 ring-zinc-200 dark:bg-zinc-800 dark:ring-zinc-700">
                    @if (auth()->user()->avatarUrl())
                        <img
                            src="{{ auth()->user()->avatarUrl() }}"
                            alt="{{ auth()->user()->name }}"
                            class="size-full object-cover"
                        />
                    @else
                        <div class="grid size-full place-items-center text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            {{ auth()->user()->initials() }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-1">
                    <flux:text class="text-sm font-medium">{{ __('Foto de perfil o Logo') }}</flux:text>
                    <input
                        type="file"
                        wire:model="avatar"
                        accept="image/*"
                        class="block w-full text-sm text-zinc-700 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:text-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-200 dark:hover:file:bg-zinc-700"
                    />
                    @error('avatar')
                        <flux:text class="text-sm !text-danger">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>

            {{-- Información básica --}}
            <div class="space-y-4 rounded-xl border border-app-border bg-app-surface p-6">
                <flux:heading size="sm">{{ __('Información Personal') }}</flux:heading>

                <div class="form-control-with-icon">
                    <flux:input wire:model="name" :label="__('Nombre completo')" type="text" required autofocus autocomplete="name" />
                    <div class="form-control-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="form-control-with-icon">
                        <flux:input wire:model="first_name" :label="__('Nombres')" type="text" autocomplete="given-name" />
                        <div class="form-control-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="form-control-with-icon">
                        <flux:input wire:model="last_name" :label="__('Apellidos')" type="text" autocomplete="family-name" />
                        <div class="form-control-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <flux:select wire:model="gender" class="select2" :label="__('Sexo')">
                            <option value="">{{ __('Seleccionar') }}</option>
                            <option value="hombre">{{ __('Hombre') }}</option>
                            <option value="mujer">{{ __('Mujer') }}</option>
                        </flux:select>
                        @error('gender')
                            <flux:text class="mt-1 text-sm !text-danger">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <div class="form-control-with-icon">
                        <flux:input wire:model="birth_date" class="date-picker" :label="__('Fecha de Nacimiento')" type="text" placeholder="DD-MM-YYYY" hint="{{ __('Formato: DD-MM-YYYY') }}" />
                        <div class="date-picker-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="form-control-with-icon">
                    <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required autocomplete="email" />
                    <div class="form-control-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Tu dirección de correo no está verificada.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Haz clic aquí para reenviar el código de verificación.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-code-sent')
                            <flux:text class="mt-2 font-medium !text-success">
                                {{ __('Se ha enviado un nuevo código de verificación a tu correo.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Información profesional (solo para proveedores) --}}
            @if (auth()->user()->isProvider())
                <div class="space-y-4 rounded-xl border border-app-border bg-app-surface p-6">
                    <flux:heading size="sm">{{ __('Información Profesional') }}</flux:heading>

                    <div class="form-control-with-icon">
                        <flux:input wire:model="rut" :label="__('RUT')" type="text" placeholder="12345678-9" hint="{{ __('Ingresa tu RUT chileno') }}" />
                        <div class="form-control-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                    </div>
                    @error('rut')
                        <flux:text class="text-sm !text-danger">{{ $message }}</flux:text>
                    @enderror

                    <div class="form-control-with-icon">
                        <flux:input wire:model="fantasy_name" :label="__('Nombre de Fantasía para Banner')" type="text" hint="{{ __('Este nombre aparecerá en el dashboard en lugar de "Integral Service Tech"') }}" />
                        <div class="form-control-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                    </div>

                    <div class="form-control-with-icon">
                        <flux:input wire:model="economic_activity" :label="__('Actividad económica')" type="text" />
                        <div class="form-control-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <flux:select wire:model.live="region_id" class="select2" :label="__('Región')">
                                <option value="">{{ __('Seleccionar región') }}</option>
                                @foreach ($this->regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('region_id')
                                <flux:text class="mt-1 text-sm !text-danger">{{ $message }}</flux:text>
                            @enderror
                        </div>

                        <div>
                            <flux:select wire:model="commune_id" class="select2" :label="__('Comuna')" :disabled="!$region_id">
                                <option value="">{{ __('Seleccionar comuna') }}</option>
                                @if ($region_id)
                                    @foreach ($this->communes as $commune)
                                        <option value="{{ $commune->id }}">{{ $commune->name }}</option>
                                    @endforeach
                                @endif
                            </flux:select>
                            @error('commune_id')
                                <flux:text class="mt-1 text-sm !text-danger">{{ $message }}</flux:text>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <flux:text class="mb-2 text-sm font-medium">{{ __('Tipo de Servicio entregado') }}</flux:text>
                        <flux:text class="mb-3 text-xs text-app-muted">{{ __('Selecciona hasta 10 categorías de servicios que ofreces') }}</flux:text>
                        <div class="space-y-2 max-h-64 overflow-y-auto rounded-lg border border-app-border bg-app-surface-2 p-4">
                            @foreach ($this->serviceCategories as $category)
                                <div class="space-y-1">
                                    <label class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            wire:model="service_category_ids"
                                            value="{{ $category->id }}"
                                            class="rounded border-app-border text-brand-600 focus:ring-brand-500"
                                        />
                                        <flux:text class="text-sm font-medium">{{ $category->name }}</flux:text>
                                    </label>
                                    @if ($category->children->isNotEmpty())
                                        <div class="ml-6 space-y-1">
                                            @foreach ($category->children as $child)
                                                <label class="flex items-center gap-2">
                                                    <input
                                                        type="checkbox"
                                                        wire:model="service_category_ids"
                                                        value="{{ $child->id }}"
                                                        class="rounded border-app-border text-brand-600 focus:ring-brand-500"
                                                    />
                                                    <flux:text class="text-xs text-app-muted">{{ $child->name }}</flux:text>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @error('service_category_ids')
                            <flux:text class="mt-1 text-sm !text-danger">{{ $message }}</flux:text>
                        @enderror
                        @if (count($service_category_ids) > 10)
                            <flux:text class="mt-2 text-sm !text-danger">{{ __('Máximo 10 categorías permitidas') }}</flux:text>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Guardar') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Guardado.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
