<x-layouts::app :title="__('Dashboard')">
    @php($user = auth()->user())

    <div class="mx-auto w-full max-w-6xl space-y-8">
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Hola, :name', ['name' => $user->name]) }}</flux:heading>
            <flux:text class="max-w-2xl">
                {{ __('Bienvenido a IntegralTech. Ajusta tu perfil, fortalece tu seguridad y comienza a operar en la plataforma.') }}
            </flux:text>

            <flux:text class="text-sm">
                {{ __('Rol') }}: <span class="font-medium">{{ $user->system_role?->value ?? 'user' }}</span>
                @if ($user->currentTenant)
                    · {{ __('Tenant') }}: <span class="font-medium">{{ $user->currentTenant->name }}</span>
                @endif
            </flux:text>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a
                href="{{ route('profile.edit') }}"
                class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-zinc-300 hover:shadow dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600"
            >
                <flux:heading size="md">{{ __('Perfil') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Actualiza tu nombre, email y avatar.') }}</flux:text>
                <flux:text class="mt-3 text-sm font-medium">{{ __('Abrir') }}</flux:text>
            </a>

            <a
                href="{{ route('user-password.edit') }}"
                class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-zinc-300 hover:shadow dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600"
            >
                <flux:heading size="md">{{ __('Seguridad') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Cambia tu contraseña y protege tu cuenta.') }}</flux:text>
                <flux:text class="mt-3 text-sm font-medium">{{ __('Abrir') }}</flux:text>
            </a>

            <a
                href="{{ route('two-factor.show') }}"
                class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-zinc-300 hover:shadow dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600"
            >
                <flux:heading size="md">{{ __('2FA') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Recomendado: activa 2FA con QR y guarda tus códigos.') }}</flux:text>
                <flux:text class="mt-3 text-sm font-medium">{{ __('Abrir') }}</flux:text>
            </a>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="md">{{ __('Siguiente paso') }}</flux:heading>
            <flux:text class="mt-1">
                {{ __('Si eres Cliente, podrás crear solicitudes de servicio. Si eres Usuario, podrás ofertar en solicitudes publicadas.') }}
            </flux:text>
        </div>
    </div>
</x-layouts::app>
