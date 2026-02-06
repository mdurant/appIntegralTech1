<x-layouts::auth.login-split>
    <div class="flex flex-col gap-6">
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                label="Correo electrónico"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                class="min-h-[44px]"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    label="Contraseña"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Contraseña"
                    viewable
                    class="min-h-[44px]"
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0 mt-1" :href="route('password.request')" wire:navigate>
                        {{ __('¿Problemas con tu clave? Recupérala') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" label="Recordarme" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full min-h-[44px] px-6 py-3 text-base" data-test="login-button">
                    {{ __('INGRESAR') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('¿No tienes cuenta?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Crear cuenta') }}</flux:link>
            </div>
        @endif

        {{-- Credenciales demo para auto-guiarse --}}
        <div class="rounded-xl border border-app-border bg-app-surface-2 p-4 text-sm">
            <div class="mb-2 font-semibold text-app-text">{{ __('Credenciales demo') }}</div>
            <ul class="space-y-1.5 text-app-muted">
                <li><span class="font-mono text-zinc-700 dark:text-zinc-300">admin@integraltech.cl</span> / password — {{ __('Perfil Admin') }}</li>
                <li><span class="font-mono text-zinc-700 dark:text-zinc-300">cliente@integraltech.cl</span> / password — {{ __('Perfil Cliente') }}</li>
                <li><span class="font-mono text-zinc-700 dark:text-zinc-300">usuario@integraltech.cl</span> / password — {{ __('Perfil Usuario') }}</li>
            </ul>
        </div>
    </div>
</x-layouts::auth.login-split>
