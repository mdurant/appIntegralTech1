<x-layouts::app.sidebar :title="__('Confirmar contraseña')">
    <flux:main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <p class="text-sm text-app-muted">{{ __('Confirme su contraseña en el cuadro de diálogo para continuar.') }}</p>
        </div>
    </flux:main>

    {{-- Modal de confirmación de contraseña (se abre al cargar la página) --}}
    <flux:modal name="confirm-password" focusable class="max-w-md" x-data x-init="$nextTick(() => $dispatch('modal-show', { name: 'confirm-password' }))">
        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <div>
                <flux:heading size="lg">{{ __('Confirmar contraseña') }}</flux:heading>
                <flux:subheading>
                    {{ __('Esta es una zona segura de la aplicación. Confirme su contraseña antes de continuar.') }}
                </flux:subheading>
            </div>

            <x-auth-session-status class="text-center" :status="session('status')" />

            @if ($errors->any())
                <flux:callout variant="danger" icon="x-circle">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </flux:callout>
            @endif

            <flux:input
                name="password"
                :label="__('Contraseña')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Contraseña')"
                viewable
            />

            <div class="flex flex-col gap-3 sm:flex-row-reverse sm:justify-end">
                <flux:button variant="primary" type="submit" class="w-full sm:w-auto" data-test="confirm-password-button">
                    {{ __('Confirmar') }}
                </flux:button>
                <a href="{{ route('dashboard') }}" wire:navigate class="inline-block">
                    <flux:button variant="ghost" type="button" class="w-full sm:w-auto">
                        {{ __('Cancelar') }}
                    </flux:button>
                </a>
            </div>
        </form>
    </flux:modal>
</x-layouts::app.sidebar>
