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
<div class="border-b border-app-border bg-app-surface">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-6 py-5">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="flex size-10 items-center justify-center rounded-xl bg-brand-700 text-white shadow-sm">
                <flux:icon.sparkles class="size-5" />
            </div>
            <div class="leading-tight">
                <div class="text-sm font-semibold">Integral Service Tech</div>
                <div class="text-xs text-app-muted">{{ __('Servicios técnicos · Cotizaciones · Ordenes de trabajo') }}</div>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('public.services.browse') }}">
                <flux:button>{{ __('Explorar servicios') }}</flux:button>
            </a>

            @auth
                <a href="{{ route('dashboard') }}">
                    <flux:button variant="primary">{{ __('Ir al dashboard') }}</flux:button>
                </a>
            @else
                <a href="{{ route('login') }}">
                    <flux:button>{{ __('Ingresar') }}</flux:button>
                </a>
                <a href="{{ route('register') }}">
                    <flux:button variant="primary">{{ __('Crear cuenta') }}</flux:button>
                </a>
            @endauth
        </div>
    </div>
</div>

<section class="mx-auto w-full max-w-6xl space-y-10 px-6 py-12">
    <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
        <div class="space-y-5">
            <flux:heading size="xl">
                {{ __('Tu próxima oportunidad técnica, a un clic.') }}
            </flux:heading>
            <flux:text class="text-app-muted">
                {{ __('Conecta con clientes que ya describieron su necesidad, cotiza rápido y gestiona tu flujo con claridad.') }}
            </flux:text>

            <div class="rounded-2xl border border-app-border bg-app-surface p-5">
                <flux:heading size="sm">{{ __('Busca solicitudes por categoría o palabra clave') }}</flux:heading>
                <div class="mt-4">
                    <livewire:marketing.landing-search />
                </div>
                <flux:text class="mt-3 text-xs text-app-muted">
                    {{ __('Ejemplos: “instalación eléctrica”, “aire acondicionado”, “reparación de fuga”.') }}
                </flux:text>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-app-border bg-app-surface p-4">
                    <div class="flex items-center gap-2">
                        <div class="grid size-9 place-items-center rounded-lg bg-brand-50 text-brand-700">
                            <flux:icon.check class="size-5" />
                        </div>
                        <div class="text-sm font-semibold">{{ __('Solicitudes claras') }}</div>
                    </div>
                    <div class="mt-2 text-sm text-app-muted">{{ __('Formularios dinámicos y fotos (hasta 4).') }}</div>
                </div>
                <div class="rounded-xl border border-app-border bg-app-surface p-4">
                    <div class="flex items-center gap-2">
                        <div class="grid size-9 place-items-center rounded-lg bg-brand-50 text-brand-700">
                            <flux:icon.check class="size-5" />
                        </div>
                        <div class="text-sm font-semibold">{{ __('Vigencia de 15 días') }}</div>
                    </div>
                    <div class="mt-2 text-sm text-app-muted">{{ __('Cotizaciones con fecha y estado. Reapertura incluida.') }}</div>
                </div>
                <div class="rounded-xl border border-app-border bg-app-surface p-4">
                    <div class="flex items-center gap-2">
                        <div class="grid size-9 place-items-center rounded-lg bg-brand-50 text-brand-700">
                            <flux:icon.check class="size-5" />
                        </div>
                        <div class="text-sm font-semibold">{{ __('Orden de Trabajo') }}</div>
                    </div>
                    <div class="mt-2 text-sm text-app-muted">{{ __('Al adjudicar, se crea OT y queda trazabilidad.') }}</div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-app-border bg-app-surface p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <div class="text-sm font-semibold">{{ __('Categorías populares') }}</div>
                    <div class="text-xs text-app-muted">{{ __('Inspiradas en rubros técnicos de alto movimiento.') }}</div>
                </div>
                <a href="{{ route('public.services.browse') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">
                    {{ __('Ver todo') }}
                </a>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach ($this->popularSubcategories as $cat)
                    @php($icon = $iconByKey[$cat->key] ?? 'sparkles')
                    <a
                        href="{{ route('public.services.browse', ['categoryId' => $cat->id]) }}"
                        class="flex items-center justify-between gap-3 rounded-2xl border border-app-border bg-app-surface-2 p-4 hover:bg-brand-50"
                    >
                        <div class="flex items-center gap-3">
                            <div class="grid size-10 place-items-center rounded-xl bg-white ring-1 ring-app-border text-brand-700">
                                <x-dynamic-component :component="'flux::icon.'.$icon" class="size-5" />
                            </div>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold">{{ $cat->name }}</div>
                                <div class="text-xs text-app-muted">
                                    {{ __('Publicadas') }}: {{ (int) $cat->published_requests_count }}
                                </div>
                            </div>
                        </div>
                        <flux:icon.arrow-right class="size-4 text-app-muted" />
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="border-y border-app-border bg-app-surface">
    <div class="mx-auto w-full max-w-6xl px-6 py-12">
        <div class="text-center">
            <flux:heading size="lg">{{ __('Profesionales valorados por clientes reales') }}</flux:heading>
            <flux:text class="mt-2 text-app-muted">{{ __('La confianza se construye con buen servicio y claridad.') }}</flux:text>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-4">
            @foreach ([
                ['name' => 'Grisel Escudero', 'role' => 'Construcción Casa', 'text' => 'Eficiente y confiable. Excelente comunicación.'],
                ['name' => 'María Soledad Ilabaca', 'role' => 'Remodelación', 'text' => 'Trabajo prolijo, resolvieron problemas y cuidaron el presupuesto.'],
                ['name' => 'Nathaly Brito', 'role' => 'Electricidad', 'text' => 'Respuesta rápida, muy educado y trabajo de calidad.'],
                ['name' => 'Mallas Ruiz Pardo', 'role' => 'Seguridad', 'text' => 'Ordenados y claros con plazos. Recomendado.'],
            ] as $card)
                <div class="rounded-2xl border border-app-border bg-app-surface p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold">{{ $card['name'] }}</div>
                            <div class="text-xs text-app-muted">{{ $card['role'] }}</div>
                        </div>
                        <div class="flex items-center gap-1 text-xs font-semibold text-warning">
                            <flux:icon.star class="size-4" />
                            5/5
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-app-muted">{{ $card['text'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            <a href="{{ route('public.services.browse') }}">
                <flux:button>{{ __('Ver servicios publicados') }}</flux:button>
            </a>
        </div>
    </div>
</section>

<section class="mx-auto w-full max-w-6xl px-6 py-12">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-app-border bg-app-surface p-6">
            <div class="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-700">
                <flux:icon.magnifying-glass class="size-5" />
            </div>
            <div class="mt-4 text-sm font-semibold">{{ __('Encuentra trabajos por rubro') }}</div>
            <div class="mt-2 text-sm text-app-muted">{{ __('Filtra por categoría y palabras clave. Mira el contexto antes de cotizar.') }}</div>
        </div>
        <div class="rounded-2xl border border-app-border bg-app-surface p-6">
            <div class="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-700">
                <flux:icon.check class="size-5" />
            </div>
            <div class="mt-4 text-sm font-semibold">{{ __('Cotiza con control') }}</div>
            <div class="mt-2 text-sm text-app-muted">{{ __('Gestiona vigencia, estados y mensajes. Mantén tu pipeline ordenado.') }}</div>
        </div>
        <div class="rounded-2xl border border-app-border bg-app-surface p-6">
            <div class="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-700">
                <flux:icon.check class="size-5" />
            </div>
            <div class="mt-4 text-sm font-semibold">{{ __('Convierte en OT') }}</div>
            <div class="mt-2 text-sm text-app-muted">{{ __('Cuando el cliente adjudica, se genera una Orden de Trabajo y seguimiento.') }}</div>
        </div>
    </div>

    <div class="mt-10 rounded-3xl border border-app-border bg-brand-50 p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <div class="text-lg font-semibold">{{ __('¿Eres un profesional técnico?') }}</div>
                <div class="text-sm text-app-muted">
                    {{ __('Regístrate y comienza a cotizar hoy. Clientes, fotos y formularios listos para tu decisión.') }}
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('register') }}">
                    <flux:button variant="primary">{{ __('Registrarme gratis') }}</flux:button>
                </a>
                <a href="{{ route('login') }}">
                    <flux:button>{{ __('Ya tengo cuenta') }}</flux:button>
                </a>
            </div>
        </div>
    </div>
</section>

<footer class="border-t border-app-border bg-app-surface">
    <div class="mx-auto w-full max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
            <div class="space-y-2">
                <div class="text-sm font-semibold">Integral Service Tech</div>
                <div class="text-sm text-app-muted">{{ __('Marketplace técnico para cotizaciones y OT.') }}</div>
                <div class="text-xs text-app-muted">
                    {{ __('IntegralTech - Soluciones Tech. Chile') }}
                </div>
            </div>

            <div class="grid gap-2 text-sm text-app-muted">
                <a class="hover:text-brand-700" href="{{ route('public.services.browse') }}">{{ __('Explorar servicios') }}</a>
                <a class="hover:text-brand-700" href="{{ route('login') }}">{{ __('Ingresar') }}</a>
                <a class="hover:text-brand-700" href="{{ route('register') }}">{{ __('Crear cuenta') }}</a>
            </div>
        </div>

        <div class="mt-8 text-xs text-app-muted">
            © {{ date('Y') }} Integral Service Tech. {{ __('Todos los derechos reservados.') }}
        </div>
    </div>
</footer>

</div>
