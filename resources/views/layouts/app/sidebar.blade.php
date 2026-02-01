<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-app-bg">
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
                        <flux:sidebar.item icon="layout-grid" :href="route('services.browse')" :current="request()->routeIs('services.browse')" wire:navigate>
                            {{ __('Servicios') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="user-group" :href="route('services.paid-contacts')" :current="request()->routeIs('services.paid-contacts')" wire:navigate>
                            {{ __('Contactos comprados') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()->isClient())
                        <flux:sidebar.item icon="folder-git-2" :href="route('client.requests.index')" :current="request()->routeIs('client.requests.*')" wire:navigate>
                            {{ __('Mis solicitudes') }}
                        </flux:sidebar.item>
                    @endif

                    @if (!auth()->user()->isClient() && !auth()->user()->isGuest())
                        <flux:sidebar.item icon="wrench" :href="route('provider.work-orders.index')" :current="request()->routeIs('provider.work-orders.*')" wire:navigate>
                            {{ __('Mis Órdenes de Trabajo') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="sparkles" :href="route('provider.work-orders.chart')" :current="request()->routeIs('provider.work-orders.chart')" wire:navigate>
                            {{ __('Gráficos de OTs') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="folder-git-2" :href="route('provider.bids.index')" :current="request()->routeIs('provider.bids.*')" wire:navigate>
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

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
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
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
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

        @fluxScripts
    </body>
</html>
