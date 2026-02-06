<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-app-bg">
        {{-- Header superior con perfil de usuario (solo desktop) --}}
        <flux:header class="hidden lg:flex border-b border-app-border bg-app-surface">
            <div class="flex w-full items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    @php($user = auth()->user())
                    @if ($user->shouldShowFantasyName())
                        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
                            @if ($user->avatarUrl())
                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->fantasy_name }}" class="size-10 rounded-lg object-cover" />
                            @else
                                <div class="flex size-10 items-center justify-center rounded-lg bg-brand-700 text-white">
                                    <x-app-logo-icon class="size-6 text-white" />
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold">{{ $user->fantasy_name }}</div>
                            </div>
                        </a>
                    @elseif (!$user->isGuest() && !$user->isClient())
                        <x-app-logo href="{{ route('dashboard') }}" wire:navigate />
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <flux:dropdown position="bottom" align="end">
                        <button class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-app-hover">
                            <flux:avatar
                                :name="auth()->user()->display_name"
                                :initials="auth()->user()->initials()"
                                class="size-8"
                            />
                            <div class="hidden text-left text-sm xl:block">
                                <flux:heading class="truncate text-sm">Hola: {{ auth()->user()->display_name }}</flux:heading>
                                
                            </div>
                            <svg class="size-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <flux:menu>
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->display_name"
                                    :initials="auth()->user()->initials()"
                                />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->display_name }}</flux:heading>
                                    
                                </div>
                            </div>
                            <flux:menu.separator />
                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                    {{ __('Configuración') }}
                                </flux:menu.item>
                            </flux:menu.radio.group>
                            <flux:menu.separator />
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-app-text hover:bg-app-hover"
                                    data-test="logout-button"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Cerrar sesión') }}
                                </button>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        </flux:header>

        <flux:sidebar sticky collapsible="mobile" class="border-e border-app-border bg-app-sidebar">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="text-app-text">
                <flux:sidebar.group :heading="__('Plataforma')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if (!auth()->user()->isClient())
                        <flux:sidebar.item icon="user-group" :href="route('services.paid-contacts')" :current="request()->routeIs('services.paid-contacts')" wire:navigate>
                            {{ __('Contactos comprados') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="rectangle-stack" :href="route('services.browse')" :current="request()->routeIs('services.browse')" wire:navigate>
                            {{ __('Solicitudes de cotización') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()->isClient())
                        @php($bidNotificationsCount = auth()->user()->unreadNotifications()->where('type', \App\Notifications\BidReceivedNotification::class)->count())
                        @if ($bidNotificationsCount > 0)
                            <flux:sidebar.item icon="bell-alert" :href="route('client.dashboard')" :current="request()->routeIs('client.dashboard')" :badge="$bidNotificationsCount" badge-color="red" wire:navigate>
                                {{ __('Notificaciones') }}
                            </flux:sidebar.item>
                        @endif
                        <flux:sidebar.item icon="folder" :href="route('client.requests.index')" :current="request()->routeIs('client.requests.*')" wire:navigate>
                            {{ __('Mis solicitudes') }}
                        </flux:sidebar.item>
                    @endif

                    @if (!auth()->user()->isClient() && !auth()->user()->isGuest())
                        @php($bidAcceptedCount = auth()->user()->unreadNotifications()->where('type', \App\Notifications\BidAcceptedNotification::class)->count())
                        @if ($bidAcceptedCount > 0)
                            <flux:sidebar.item icon="bell-alert" :href="route('provider.work-orders.index')" :current="request()->routeIs('provider.work-orders.*')" :badge="$bidAcceptedCount" badge-color="green" wire:navigate>
                                {{ __('Notificaciones (OT creada)') }}
                            </flux:sidebar.item>
                        @endif
                        <flux:sidebar.item icon="wrench" :href="route('provider.work-orders.index')" :current="request()->routeIs('provider.work-orders.*')" wire:navigate>
                            {{ __('Mis Órdenes de Trabajo') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="sparkles" :href="route('provider.work-orders.chart')" :current="request()->routeIs('provider.work-orders.chart')" wire:navigate>
                            {{ __('Gráficos de OTs') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="document-text" :href="route('provider.bids.index')" :current="request()->routeIs('provider.bids.*')" wire:navigate>
                            {{ __('Mis Cotizaciones') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()->isAdministrator())
                        <flux:sidebar.item icon="cog" :href="route('admin.dashboard')" :current="request()->routeIs('admin.*')" wire:navigate>
                            {{ __('Admin') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- Ocultar sección de usuario en sidebar (solo mostrar en header superior) --}}
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->display_name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->display_name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Configuración') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-app-text hover:bg-app-hover"
                            data-test="logout-button"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Cerrar sesión') }}
                        </button>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <x-toaster />

        @fluxScripts

        <script>
            // Inicializar Flatpickr y Select2 después de que el DOM esté listo
            document.addEventListener('DOMContentLoaded', function() {
                initializeDatePickers();
                initializeSelect2();
            });

            // Reinicializar después de actualizaciones de Livewire
            if (typeof Livewire !== 'undefined') {
                document.addEventListener('livewire:init', function() {
                    Livewire.hook('morph.updated', ({ el }) => {
                        setTimeout(() => {
                            initializeDatePickers(el);
                            initializeSelect2(el);
                        }, 100);
                    });
                });
            }

            function initializeDatePickers(container = document) {
                // Buscar todos los [data-flux-field] que contengan un elemento con data-date-picker="true"
                const allFluxFields = container.querySelectorAll('[data-flux-field]');
                
                allFluxFields.forEach(function(fluxField) {
                    // Verificar si este campo contiene un elemento con data-date-picker="true"
                    const hasDatePicker = fluxField.querySelector('[data-date-picker="true"]') !== null;
                    
                    if (!hasDatePicker) {
                        return; // No es un campo de fecha
                    }
                    
                    // Buscar el input real dentro del contenedor Flux
                    // Flux UI estructura: [data-flux-field] > [data-flux-control] > input
                    let input = fluxField.querySelector('input[type="text"]') || 
                               fluxField.querySelector('input') ||
                               fluxField.querySelector('[data-flux-control]');
                    
                    // Si no encontramos un input válido, saltar
                    if (!input || (input.tagName && input.tagName.toLowerCase() !== 'input')) {
                        return;
                    }
                    
                    // Si ya está inicializado, destruir y reinicializar para Livewire
                    if (input._flatpickr) {
                        input._flatpickr.destroy();
                    }
                    
                    if (typeof flatpickr !== 'undefined') {
                        // Obtener el wire:model del input o del contenedor
                        const wireModel = input.getAttribute('wire:model') || 
                                        fluxField.getAttribute('wire:model') ||
                                        fluxField.closest('[wire\\:model]')?.getAttribute('wire:model');
                        
                        flatpickr(input, {
                            locale: flatpickr.l10ns.es,
                            dateFormat: 'd-m-Y',
                            allowInput: true,
                            clickOpens: true,
                            animate: true,
                            defaultDate: input.value || null,
                            onChange: function(selectedDates, dateStr, instance) {
                                // Sincronizar con Livewire si está disponible
                                if (typeof Livewire !== 'undefined' && wireModel) {
                                    const livewireId = input.closest('[wire\\:id]')?.getAttribute('wire:id');
                                    if (livewireId) {
                                        const component = Livewire.find(livewireId);
                                        if (component) {
                                            component.set(wireModel, dateStr);
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
                
                // También buscar inputs directos con clase date-picker (fallback)
                const directInputs = container.querySelectorAll('input.date-picker');
                directInputs.forEach(function(input) {
                    if (input._flatpickr) {
                        input._flatpickr.destroy();
                    }
                    
                    if (typeof flatpickr !== 'undefined') {
                        const wireModel = input.getAttribute('wire:model');
                        flatpickr(input, {
                            locale: flatpickr.l10ns.es,
                            dateFormat: 'd-m-Y',
                            allowInput: true,
                            clickOpens: true,
                            animate: true,
                            defaultDate: input.value || null,
                            onChange: function(selectedDates, dateStr, instance) {
                                if (typeof Livewire !== 'undefined' && wireModel) {
                                    const livewireId = input.closest('[wire\\:id]')?.getAttribute('wire:id');
                                    if (livewireId) {
                                        const component = Livewire.find(livewireId);
                                        if (component) {
                                            component.set(wireModel, dateStr);
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            }

            function initializeSelect2(container = document) {
                const selects = container.querySelectorAll('select.select2');
                selects.forEach(function(select) {
                    if (typeof $ !== 'undefined' && $.fn.select2) {
                        if ($(select).hasClass('select2-hidden-accessible')) {
                            $(select).select2('destroy');
                        }
                        $(select).select2({
                            theme: 'default',
                            width: '100%',
                            language: {
                                noResults: function() {
                                    return 'No se encontraron resultados';
                                },
                                searching: function() {
                                    return 'Buscando...';
                                },
                            },
                        });
                    }
                });
            }
        </script>

        {{-- Cookie Consent Modal --}}
        <livewire:cookie-consent-modal />
    </body>
</html>
