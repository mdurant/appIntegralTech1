@php
    $iconByKey = [
        'construccion' => 'hammer',
        'construccion-casa' => 'home',
        'construccion-galpon' => 'warehouse',
        'electricidad' => 'zap',
        'gasfiteria' => 'droplets',
        'seguridad' => 'shield',
        'pintores' => 'paintbrush',
        'aire-acondicionado' => 'wind',
        'limpieza' => 'sparkles',
        'carpinteria' => 'wrench',
        'remodelacion' => 'building-2',
    ];
@endphp

<div class="min-h-screen">
    {{-- Header/Navbar --}}
    <div class="border-b border-app-border bg-app-surface">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-3 px-4 py-4 sm:gap-4 sm:px-6 sm:py-5">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2 sm:gap-3">
                <div class="flex shrink-0 size-10 items-center justify-center rounded-xl bg-brand-700 text-white shadow-sm">
                    <x-app-logo-icon class="size-6 text-white" />
                </div>
                <div class="min-w-0 leading-tight">
                    <div class="truncate text-sm font-semibold">Integral Service Tech</div>
                    <div class="hidden text-xs text-app-muted sm:block">{{ __('Servicios técnicos · Cotizaciones · Ordenes de trabajo') }}</div>
                </div>
            </a>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('public.services.browse') }}" class="hidden sm:block">
                    <flux:button class="min-h-[44px] px-4 py-2.5">{{ __('Explorar servicios') }}</flux:button>
                </a>

                @auth
                    <a href="{{ route('dashboard') }}">
                        <flux:button variant="primary" class="min-h-[44px] px-4 py-2.5 text-sm sm:px-6 sm:text-base">{{ __('Dashboard') }}</flux:button>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:block">
                        <flux:button class="min-h-[44px] px-4 py-2.5">{{ __('Ingresar') }}</flux:button>
                    </a>
                    <a href="{{ route('register') }}">
                        <flux:button variant="primary" class="min-h-[44px] px-4 py-2.5 text-sm sm:px-6 sm:text-base">{{ __('Crear cuenta') }}</flux:button>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Header/Hero Section --}}
    <section class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:py-24">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-center lg:gap-12">
            <div class="space-y-4 sm:space-y-6">
                <h1 class="text-3xl font-bold leading-tight text-app-text sm:text-4xl lg:text-5xl">
                    {{ __('Conecta con profesionales de calidad y experiencia') }}
                </h1>

                <flux:text class="text-base text-app-muted sm:text-lg">
                    {{ __('Simplifica la búsqueda de profesionales de confianza para tus proyectos, crea una cuenta y recibe propuestas de calidad en tiempo récord.') }}
                </flux:text>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto">
                        <flux:button variant="primary" class="w-full min-h-[44px] px-6 py-3 text-base sm:w-auto">
                            {{ __('Crear cuenta gratis') }}
                        </flux:button>
                    </a>
                    <a href="{{ route('public.services.browse') }}" class="w-full sm:w-auto">
                        <flux:button class="w-full min-h-[44px] px-6 py-3 text-base sm:w-auto">
                            {{ __('Ver servicios disponibles') }}
                        </flux:button>
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-6 pt-4 sm:gap-8">
                    <div>
                        <div class="text-xl font-bold text-brand-700 sm:text-2xl">{{ __('Gratis') }}</div>
                        <div class="text-xs text-app-muted sm:text-sm">{{ __('Sin costo para clientes') }}</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-brand-700 sm:text-2xl">{{ __('15 días') }}</div>
                        <div class="text-xs text-app-muted sm:text-sm">{{ __('Vigencia de ofertas') }}</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-brand-700 sm:text-2xl">{{ __('Múltiples') }}</div>
                        <div class="text-xs text-app-muted sm:text-sm">{{ __('Propuestas por solicitud') }}</div>
                    </div>
                </div>
            </div>

            <div class="relative order-first lg:order-last">
                <div class="rounded-2xl border border-app-border bg-app-surface-2 p-3 shadow-lg sm:rounded-3xl sm:p-4 lg:p-6 xl:p-8">
                    <div class="aspect-video w-full overflow-hidden rounded-xl bg-app-surface sm:rounded-2xl">
                        <img
                            src="{{ asset('images/dashboard-reference.png') }}"
                            alt="{{ __('Dashboard de Integral Service Tech - Vista previa de la plataforma') }}"
                            class="h-full w-full object-contain object-center"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: 3 Pasos del Ciclo del Proyecto --}}
    <section class="border-y border-app-border bg-app-surface">
        <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
            <div class="mb-8 text-center sm:mb-12">
                <flux:heading size="xl" class="text-app-text">
                    {{ __('Tu proyecto en 3 pasos simples') }}
                </flux:heading>
                <flux:text class="mt-3 text-base text-app-muted sm:text-lg">
                    {{ __('Desde la solicitud hasta la adjudicación, te acompañamos en cada etapa') }}
                </flux:text>
            </div>

            <div class="grid gap-6 sm:gap-8 md:grid-cols-3">
                <div class="rounded-xl border border-app-border bg-app-surface p-6 text-center sm:rounded-2xl sm:p-8">
                    <div class="mx-auto grid size-14 place-items-center rounded-xl bg-brand-50 text-brand-700 mb-4 sm:size-16 sm:rounded-2xl sm:mb-6">
                        <flux:icon.magnifying-glass class="size-7 sm:size-8" />
                    </div>
                    <flux:heading size="md" class="mb-3">
                        {{ __('1. Publica tu necesidad') }}
                    </flux:heading>
                    <flux:text class="text-sm text-app-muted sm:text-base">
                        {{ __('Describe tu proyecto con formularios dinámicos, adjunta hasta 4 imágenes y completa los detalles específicos de tu categoría. Tu solicitud queda visible para profesionales calificados.') }}
                    </flux:text>
                </div>

                <div class="rounded-xl border border-app-border bg-app-surface p-6 text-center sm:rounded-2xl sm:p-8">
                    <div class="mx-auto grid size-14 place-items-center rounded-xl bg-brand-50 text-brand-700 mb-4 sm:size-16 sm:rounded-2xl sm:mb-6">
                        <flux:icon.check class="size-7 sm:size-8" />
                    </div>
                    <flux:heading size="md" class="mb-3">
                        {{ __('2. Recibe propuestas') }}
                    </flux:heading>
                    <flux:text class="text-sm text-app-muted sm:text-base">
                        {{ __('Profesionales técnicos revisan tu solicitud y te envían cotizaciones detalladas con precios, plazos y mensajes personalizados. Cada oferta tiene 15 días de vigencia para que compares y elijas.') }}
                    </flux:text>
                </div>

                <div class="rounded-xl border border-app-border bg-app-surface p-6 text-center sm:rounded-2xl sm:p-8">
                    <div class="mx-auto grid size-14 place-items-center rounded-xl bg-brand-50 text-brand-700 mb-4 sm:size-16 sm:rounded-2xl sm:mb-6">
                        <flux:icon.star class="size-7 sm:size-8" />
                    </div>
                    <flux:heading size="md" class="mb-3">
                        {{ __('3. Adjudica y ejecuta') }}
                    </flux:heading>
                    <flux:text class="text-sm text-app-muted sm:text-base">
                        {{ __('Selecciona la propuesta que mejor se adapte a tus necesidades. Al adjudicar, se genera automáticamente una Orden de Trabajo (OT) con toda la trazabilidad del proyecto.') }}
                    </flux:text>
                </div>
            </div>
        </div>
    </section>

    {{-- Section2: Categorías y Video --}}
    <section class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        <div class="mb-8 text-center sm:mb-12">
            <flux:heading size="xl" class="text-app-text">
                {{ __('Explora las categorías de servicios disponibles') }}
            </flux:heading>
            <flux:text class="mt-3 text-base text-app-muted sm:text-lg">
                {{ __('Desde construcción hasta instalaciones especializadas, encuentra el profesional adecuado para cada necesidad') }}
            </flux:text>
        </div>

        <div class="grid gap-8 lg:grid-cols-2 lg:items-start lg:gap-12">
            <div class="space-y-6">
                <flux:heading size="md" class="text-app-text">
                    {{ __('Categorías principales') }}
                </flux:heading>
                <flux:text class="text-app-muted">
                    {{ __('Solicita cotizaciones en estas áreas de servicio:') }}
                </flux:text>

                <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
                    @foreach ($this->topCategories->take(6) as $category)
                        <a
                            href="{{ route('public.services.browse', ['categoryId' => $category->id]) }}"
                            class="group flex min-h-[60px] items-center gap-3 rounded-lg border border-app-border bg-app-surface p-3 transition hover:border-brand-300 hover:bg-brand-50 sm:min-h-[72px] sm:rounded-xl sm:p-4"
                        >
                            <div class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-50 text-brand-700 group-hover:bg-brand-100 sm:size-10">
                                @php($icon = $iconByKey[$category->key] ?? 'sparkles')
                                <x-dynamic-component :component="'flux::icon.'.$icon" class="size-4 sm:size-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-app-text sm:text-base">{{ $category->name }}</div>
                                <div class="text-xs text-app-muted">
                                    {{ $category->children->count() }} {{ __('subcategorías') }}
                                </div>
                            </div>
                            <flux:icon.arrow-right class="size-4 shrink-0 text-app-muted group-hover:text-brand-700" />
                        </a>
                    @endforeach
                </div>

                <div class="pt-4">
                    <a href="{{ route('public.services.browse') }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand-700 hover:text-brand-800">
                        {{ __('Ver todas las categorías') }}
                        <flux:icon.arrow-right class="size-4" />
                    </a>
                </div>
            </div>

            <div class="rounded-xl border border-app-border bg-app-surface p-4 shadow-sm sm:rounded-2xl sm:p-6">
                <flux:heading size="sm" class="mb-3 sm:mb-4">
                    {{ __('Cómo funciona Integral Service Tech') }}
                </flux:heading>
                <div class="aspect-video w-full overflow-hidden rounded-lg bg-app-surface-2 sm:rounded-xl">
                    <iframe
                        class="h-full w-full"
                        src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                        title="Video explicativo - Integral Service Tech"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
                <flux:text class="mt-2 text-xs text-app-muted sm:mt-3">
                    {{ __('Video de referencia - Se actualizará con contenido específico') }}
                </flux:text>
            </div>
        </div>
    </section>

    {{-- CTA Final --}}
    <section class="border-y border-app-border bg-brand-50">
        <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 sm:py-16">
            <div class="text-center">
                <flux:heading size="xl" class="text-app-text">
                    {{ __('¿Listo para encontrar el profesional perfecto?') }}
                </flux:heading>
                <flux:text class="mt-4 text-base text-app-muted sm:text-lg">
                    {{ __('Crea tu cuenta gratis y comienza a recibir propuestas de calidad hoy mismo') }}
                </flux:text>
                <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:mt-8 sm:flex-row sm:gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto">
                        <flux:button variant="primary" class="w-full min-h-[44px] px-6 py-3 text-base sm:w-auto">
                            {{ __('Crear cuenta gratis') }}
                        </flux:button>
                    </a>
                    <a href="{{ route('public.services.browse') }}" class="w-full sm:w-auto">
                        <flux:button class="w-full min-h-[44px] px-6 py-3 text-base sm:w-auto">
                            {{ __('Explorar servicios') }}
                        </flux:button>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-app-border bg-app-surface">
        <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-2">
                    <div class="text-sm font-semibold">Integral Service Tech</div>
                    <div class="text-xs text-app-muted sm:text-sm">{{ __('Marketplace de Servicios a Cotizaciones') }}</div>
                    <div class="text-xs text-app-muted">
                        {{ __('IntegralTech - Soluciones Tech. Chile') }}
                    </div>
                </div>

                <div class="grid gap-2 text-sm text-app-muted">
                    <a class="footer-link min-h-[44px] flex items-center transition-colors" href="{{ route('public.services.browse') }}">{{ __('Explorar servicios') }}</a>
                    <a class="footer-link min-h-[44px] flex items-center transition-colors" href="{{ route('login') }}">{{ __('Ingresar') }}</a>
                    <a class="footer-link min-h-[44px] flex items-center transition-colors" href="{{ route('register') }}">{{ __('Crear cuenta') }}</a>
                </div>
            </div>

            <div class="mt-6 text-xs text-app-muted sm:mt-8">
                © {{ date('Y') }} Integral Service Tech. {{ __('Todos los derechos reservados.') }}
            </div>
        </div>
    </footer>
</div>
