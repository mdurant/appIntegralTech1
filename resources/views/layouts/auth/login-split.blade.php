<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-app-bg antialiased">
        <div class="grid min-h-screen lg:grid-cols-2">
            {{-- Left Section: Login Form and Support --}}
            <div class="flex flex-col bg-white px-4 py-8 sm:px-6 sm:py-12 lg:px-12 lg:py-16">
                {{-- Navigation Link --}}
                <a href="{{ route('home') }}" class="mb-8 text-sm text-app-muted hover:text-brand-700 transition-colors" wire:navigate>
                    ← {{ __('Ir a home') }}
                </a>

                {{-- Branding --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="flex size-10 items-center justify-center rounded-xl bg-brand-700 text-white shadow-sm">
                            <x-app-logo-icon class="size-6 text-white" />
                        </div>
                        <div class="text-xl font-semibold text-app-text">Integral Service Tech</div>
                    </div>
                    <div class="text-sm text-app-muted">{{ __('Marketplace de Servicios Técnicos') }}</div>
                </div>

                {{-- Welcome Message --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-app-text mb-2 sm:text-3xl">
                        {{ __('Bienvenido a tu plataforma') }}
                    </h1>
                    <p class="text-sm text-app-muted">
                        {{ __('Ingresa tus credenciales para acceder a tu cuenta') }}
                    </p>
                </div>

                {{-- Login Form Content --}}
                <div class="flex-1">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        {{ $content ?? '' }}
                    @endisset
                </div>

                {{-- Support Footer --}}
                <div class="mt-8 rounded-xl border border-app-border bg-app-surface-2 p-6">
                    <h3 class="text-sm font-semibold text-app-text mb-4">
                        {{ __('¿Tienes problema para acceder a tu cuenta?') }}
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-2 text-app-muted">
                            <flux:icon.phone class="size-4" />
                            <span>{{ __('Soporte IntegralTech') }}: <a href="tel:+56912345678" class="text-brand-700 hover:text-brand-800 font-medium">+569 1234 5678</a></span>
                        </div>
                        <div class="flex items-center gap-2 text-app-muted">
                            <flux:icon.phone class="size-4" />
                            <span>{{ __('Soporte Técnico') }}: <a href="tel:+56987654321" class="text-brand-700 hover:text-brand-800 font-medium">+569 8765 4321</a></span>
                        </div>
                        <div class="pt-2">
                            <a href="#" class="text-sm text-brand-700 hover:text-brand-800 font-medium">
                                {{ __('IR AL CENTRO DE AYUDA') }} →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Section: Business Image --}}
            <div class="hidden lg:flex relative bg-gradient-to-br from-brand-600 to-brand-800">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="relative z-10 flex flex-col justify-between p-12 text-white">
                    {{-- Top Logo/Branding --}}
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex size-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                                <x-app-logo-icon class="size-7 text-white" />
                            </div>
                            <div>
                                <div class="text-xl font-semibold">Integral Service Tech</div>
                                <div class="text-sm text-white/80">{{ __('Servicios Técnicos Profesionales') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Image Placeholder --}}
                    <div class="flex-1 flex items-center justify-center my-8">
                        <div class="rounded-2xl overflow-hidden shadow-2xl max-w-md w-full">
                            <img
                                src="{{ asset('images/dashboard-reference.png') }}"
                                alt="{{ __('Ciclo de negocio Integral Service Tech') }}"
                                class="w-full h-auto object-cover"
                                loading="lazy"
                            />
                        </div>
                    </div>

                    {{-- Bottom Message --}}
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                        <p class="text-sm text-white/90">
                            {{ __('Conecta con profesionales técnicos calificados para tus proyectos de hogar y empresa. Publica tu solicitud y recibe múltiples propuestas de calidad.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
