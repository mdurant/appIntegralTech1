<x-layouts::app :title="__('Dashboard')">
    @php($user = auth()->user())

    <div class="mx-auto w-full max-w-7xl space-y-6 px-4 py-6 sm:px-6">
        {{-- Header con saludo y perfil --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1 space-y-2">
                <flux:heading size="lg">{{ __('Hola, :name', ['name' => $user->name]) }}</flux:heading>
                <flux:text class="max-w-2xl text-sm text-app-muted">
                    {{ __('Bienvenido a IntegralTech. Ajusta tu perfil, fortalece tu seguridad y comienza a operar en la plataforma.') }}
                </flux:text>
                <flux:text class="text-sm">
                    {{ __('Rol') }}: <span class="font-medium">{{ $user->system_role?->label() ?? __('Usuario') }}</span>
                    @if ($user->currentTenant)
                        · {{ __('Organización') }}: <span class="font-medium">{{ $user->currentTenant->name }}</span>
                    @endif
                </flux:text>
            </div>

            {{-- Perfil de usuario en barra superior derecha --}}
            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <flux:text class="text-sm font-medium">{{ $user->name }}</flux:text>
                    <flux:text class="text-xs text-app-muted">{{ $user->email }}</flux:text>
                </div>
                <flux:avatar
                    :name="$user->name"
                    :initials="$user->initials()"
                    class="size-10"
                />
            </div>
        </div>

        {{-- Gráficos principales --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Gráfico: Usuarios Registrados --}}
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="md">{{ __('Usuarios Registrados') }}</flux:heading>
                    <div class="grid size-10 place-items-center rounded-lg bg-brand-50 text-brand-700">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="h-48 flex items-center justify-center rounded-lg border border-app-border bg-app-surface-2">
                    <flux:text class="text-sm text-app-muted">{{ __('Gráfico de usuarios - Se implementará con Chart.js') }}</flux:text>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <flux:text class="text-xs text-app-muted">{{ __('Total registrados') }}</flux:text>
                    <flux:text class="text-lg font-bold">1,247</flux:text>
                </div>
            </div>

            {{-- Gráfico: Servicios Solicitados (Cotizaciones) --}}
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="md">{{ __('Servicios Solicitados') }}</flux:heading>
                    <div class="grid size-10 place-items-center rounded-lg bg-brand-50 text-brand-700">
                        <flux:icon.layout-grid class="size-5" />
                    </div>
                </div>
                <div class="h-48 flex items-center justify-center rounded-lg border border-app-border bg-app-surface-2">
                    <flux:text class="text-sm text-app-muted">{{ __('Gráfico de cotizaciones - Se implementará con Chart.js') }}</flux:text>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <flux:text class="text-xs text-app-muted">{{ __('Total solicitudes') }}</flux:text>
                    <flux:text class="text-lg font-bold">892</flux:text>
                </div>
            </div>

            {{-- Gráfico: Órdenes de Trabajo Generadas --}}
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="md">{{ __('Órdenes de Trabajo') }}</flux:heading>
                    <div class="grid size-10 place-items-center rounded-lg bg-brand-50 text-brand-700">
                        <flux:icon.wrench class="size-5" />
                    </div>
                </div>
                <div class="h-48 flex items-center justify-center rounded-lg border border-app-border bg-app-surface-2">
                    <flux:text class="text-sm text-app-muted">{{ __('Gráfico de OTs - Se implementará con Chart.js') }}</flux:text>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <flux:text class="text-xs text-app-muted">{{ __('Total OTs') }}</flux:text>
                    <flux:text class="text-lg font-bold">456</flux:text>
                </div>
            </div>
        </div>

        {{-- Tarjetas de estadísticas --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Precios de Cotizaciones --}}
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <flux:text class="text-sm text-app-muted">{{ __('Precios de Cotizaciones') }}</flux:text>
                        <flux:text class="mt-1 text-2xl font-bold">$50.400</flux:text>
                        <flux:text class="mt-1 text-xs text-green-600">
                            <span class="inline-flex items-center gap-1">
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                {{ __('Última semana') }} +12%
                            </span>
                        </flux:text>
                    </div>
                    <div class="grid size-12 place-items-center rounded-lg bg-brand-50 text-brand-700">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Venta Estimada --}}
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <flux:text class="text-sm text-app-muted">{{ __('Venta Estimada') }}</flux:text>
                        <flux:text class="mt-1 text-2xl font-bold">$18.400.000</flux:text>
                        <flux:text class="mt-1 text-xs text-green-600">
                            <span class="inline-flex items-center gap-1">
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                {{ __('Última semana') }} +8%
                            </span>
                        </flux:text>
                    </div>
                    <div class="grid size-12 place-items-center rounded-lg bg-blue-50 text-blue-700">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tarjeta dummy 1 --}}
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <flux:text class="text-sm text-app-muted">{{ __('Cotizaciones Aceptadas') }}</flux:text>
                        <flux:text class="mt-1 text-2xl font-bold">234</flux:text>
                        <flux:text class="mt-1 text-xs text-red-600">
                            <span class="inline-flex items-center gap-1">
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                {{ __('Última semana') }} -5%
                            </span>
                        </flux:text>
                    </div>
                    <div class="grid size-12 place-items-center rounded-lg bg-green-50 text-green-700">
                        <flux:icon.check class="size-6" />
                    </div>
                </div>
            </div>

            {{-- Tarjeta dummy 2 --}}
            <div class="rounded-xl border border-app-border bg-app-surface p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <flux:text class="text-sm text-app-muted">{{ __('Tasa de Conversión') }}</flux:text>
                        <flux:text class="mt-1 text-2xl font-bold">26.2%</flux:text>
                        <flux:text class="mt-1 text-xs text-green-600">
                            <span class="inline-flex items-center gap-1">
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                {{ __('Última semana') }} +3%
                            </span>
                        </flux:text>
                    </div>
                    <div class="grid size-12 place-items-center rounded-lg bg-purple-50 text-purple-700">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cápsulas de configuración con íconos --}}
        <div class="grid gap-4 md:grid-cols-3">
            <a
                href="{{ route('profile.edit') }}"
                class="group flex items-start gap-4 rounded-xl border border-app-border bg-app-surface p-5 shadow-sm transition hover:border-app-border-hover hover:shadow-md"
            >
                <div class="grid shrink-0 size-12 place-items-center rounded-lg bg-brand-50 text-brand-700 group-hover:bg-brand-100">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <flux:heading size="md">{{ __('Perfil') }}</flux:heading>
                    <flux:text class="mt-1 text-sm text-app-muted">{{ __('Actualiza tu nombre, email y avatar.') }}</flux:text>
                    <flux:text class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-brand-700 group-hover:text-brand-800">
                        {{ __('Abrir') }}
                        <flux:icon.arrow-right class="size-4" />
                    </flux:text>
                </div>
            </a>

            <a
                href="{{ route('user-password.edit') }}"
                class="group flex items-start gap-4 rounded-xl border border-app-border bg-app-surface p-5 shadow-sm transition hover:border-app-border-hover hover:shadow-md"
            >
                <div class="grid shrink-0 size-12 place-items-center rounded-lg bg-blue-50 text-blue-700 group-hover:bg-blue-100">
                    <flux:icon.shield class="size-6" />
                </div>
                <div class="flex-1">
                    <flux:heading size="md">{{ __('Seguridad') }}</flux:heading>
                    <flux:text class="mt-1 text-sm text-app-muted">{{ __('Cambia tu contraseña y protege tu cuenta.') }}</flux:text>
                    <flux:text class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-brand-700 group-hover:text-brand-800">
                        {{ __('Abrir') }}
                        <flux:icon.arrow-right class="size-4" />
                    </flux:text>
                </div>
            </a>

            <a
                href="{{ route('two-factor.show') }}"
                class="group flex items-start gap-4 rounded-xl border border-app-border bg-app-surface p-5 shadow-sm transition hover:border-app-border-hover hover:shadow-md"
            >
                <div class="grid shrink-0 size-12 place-items-center rounded-lg bg-green-50 text-green-700 group-hover:bg-green-100">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <flux:heading size="md">{{ __('2FA') }}</flux:heading>
                    <flux:text class="mt-1 text-sm text-app-muted">{{ __('Recomendado: activa 2FA con QR y guarda tus códigos.') }}</flux:text>
                    <flux:text class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-brand-700 group-hover:text-brand-800">
                        {{ __('Abrir') }}
                        <flux:icon.arrow-right class="size-4" />
                    </flux:text>
                </div>
            </a>
        </div>

        {{-- Sección siguiente paso --}}
        <div class="rounded-xl border border-app-border bg-app-surface-2 p-5">
            <flux:heading size="md">{{ __('Siguiente paso') }}</flux:heading>
            <flux:text class="mt-1 text-sm text-app-muted">
                {{ __('Si eres Cliente, podrás crear solicitudes de servicio. Si eres Usuario, podrás ofertar en solicitudes publicadas.') }}
            </flux:text>
        </div>
    </div>
</x-layouts::app>
