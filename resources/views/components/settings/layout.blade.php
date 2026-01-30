<div class="w-full">
    {{-- Header --}}
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Configuración') }}</flux:heading>
        <flux:subheading size="lg" class="mt-2">{{ __('Administra tu perfil y la configuración de tu cuenta') }}</flux:subheading>
    </div>

    {{-- Tabs Navigation --}}
    <div class="mb-8 border-b border-app-border">
        <nav class="flex gap-1 -mb-px" aria-label="{{ __('Configuración') }}">
            <a
                href="{{ route('profile.edit') }}"
                wire:navigate
                class="settings-tab group relative flex items-center gap-2 px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'settings-tab-active text-brand-700' : 'text-app-muted hover:text-app-text' }}"
            >
                <svg class="size-4 shrink-0 {{ request()->routeIs('profile.edit') ? 'text-brand-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ __('Ficha Personal') }}</span>
            </a>
            <a
                href="{{ route('user-password.edit') }}"
                wire:navigate
                class="settings-tab group relative flex items-center gap-2 px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('user-password.edit') ? 'settings-tab-active text-brand-700' : 'text-app-muted hover:text-app-text' }}"
            >
                <svg class="size-4 shrink-0 {{ request()->routeIs('user-password.edit') ? 'text-brand-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>{{ __('Password') }}</span>
            </a>
            <a
                href="{{ route('billing.show') }}"
                wire:navigate
                class="settings-tab group relative flex items-center gap-2 px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('billing.show') ? 'settings-tab-active text-brand-700' : 'text-app-muted hover:text-app-text' }}"
            >
                <svg class="size-4 shrink-0 {{ request()->routeIs('billing.show') ? 'text-brand-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>{{ __('Pagos y Planes') }}</span>
            </a>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <a
                    href="{{ route('two-factor.show') }}"
                    wire:navigate
                    class="settings-tab group relative flex items-center gap-2 px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('two-factor.show') ? 'settings-tab-active text-brand-700' : 'text-app-muted hover:text-app-text' }}"
                >
                    <flux:icon.shield variant="micro" class="{{ request()->routeIs('two-factor.show') ? 'text-brand-700' : '' }}" />
                    <span>{{ __('Two-Factor (2FA)') }}</span>
                </a>
            @endif
            <a
                href="{{ route('active-sessions.show') }}"
                wire:navigate
                class="settings-tab group relative flex items-center gap-2 px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('active-sessions.show') ? 'settings-tab-active text-brand-700' : 'text-app-muted hover:text-app-text' }}"
            >
                <svg class="size-4 shrink-0 {{ request()->routeIs('active-sessions.show') ? 'text-brand-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>{{ __('Sesiones Activas') }}</span>
            </a>
        </nav>
    </div>

    {{-- Content --}}
    <div class="w-full">
        <div class="mb-6">
            <flux:heading size="lg">{{ $heading ?? '' }}</flux:heading>
            <flux:subheading class="mt-2">{{ $subheading ?? '' }}</flux:subheading>
        </div>

        <div class="w-full max-w-4xl">
            {{ $slot }}
        </div>
    </div>
</div>
