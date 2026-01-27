<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Configuración de Contraseña') }}</flux:heading>

    <x-settings.layout :heading="__('Seguridad')" :subheading="__('Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <div class="form-control-with-icon">
                <flux:input
                    wire:model="current_password"
                    :label="__('Contraseña actual')"
                    type="password"
                    required
                    autocomplete="current-password"
                />
                <div class="form-control-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <div class="form-control-with-icon">
                <flux:input
                    wire:model="password"
                    :label="__('Nueva contraseña')"
                    type="password"
                    required
                    autocomplete="new-password"
                />
                <div class="form-control-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>
            <div class="form-control-with-icon">
                <flux:input
                    wire:model="password_confirmation"
                    :label="__('Confirmar contraseña')"
                    type="password"
                    required
                    autocomplete="new-password"
                />
                <div class="form-control-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Guardar') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Guardado.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
