<div class="space-y-6">
    <div class="space-y-1">
        <flux:heading size="lg">{{ __('Verifica tu cuenta') }}</flux:heading>
        <flux:text>{{ __('Ingresa el código de 6 dígitos enviado a tu correo.') }}</flux:text>
    </div>

    <form wire:submit="verify" class="space-y-6">
        <flux:otp
            wire:model="code"
            length="6"
            label="{{ __('Código') }}"
            label:sr-only
            :error:icon="false"
        />

        <div class="space-y-3">
            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Verificar') }}
            </flux:button>

            <flux:button type="button" wire:click="resend" class="w-full">
                {{ __('Reenviar código') }}
            </flux:button>
        </div>
    </form>
</div>

