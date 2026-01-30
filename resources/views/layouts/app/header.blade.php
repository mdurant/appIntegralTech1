<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-app-bg text-app-text">
        <flux:header container class="border-b border-app-border bg-app-surface">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>

                @if (!auth()->user()->isClient())
                    <flux:navbar.item icon="layout-grid" :href="route('services.browse')" :current="request()->routeIs('services.*')" wire:navigate>
                        {{ __('Servicios') }}
                    </flux:navbar.item>
                @endif
            </flux:navbar>

            <flux:spacer />

            @if (!auth()->user()->isClient())
                <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                    <flux:tooltip :content="__('Explorar servicios')" position="bottom">
                        <flux:navbar.item
                            class="h-10 max-lg:hidden [&>div>svg]:size-5"
                            icon="magnifying-glass"
                            :href="route('services.browse')"
                            label="{{ __('Explorar') }}"
                            wire:navigate
                        />
                    </flux:tooltip>
                </flux:navbar>
            @endif

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-app-border bg-app-surface">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Plataforma')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard')  }}
                    </flux:sidebar.item>

                    @if (!auth()->user()->isClient())
                        <flux:sidebar.item icon="layout-grid" :href="route('services.browse')" :current="request()->routeIs('services.*')" wire:navigate>
                            {{ __('Servicios')  }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
