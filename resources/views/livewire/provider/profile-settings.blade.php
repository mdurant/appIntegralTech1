<section class="mx-auto w-full max-w-2xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Configuración de Perfil Profesional') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Completa tu información profesional para mejorar tu visibilidad') }}
        </flux:text>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-app-border bg-app-surface p-6 space-y-6">
            <flux:input
                wire:model="rut"
                label="{{ __('RUT') }}"
                placeholder="12345678-9"
                hint="{{ __('Ingresa tu RUT chileno') }}"
            />

            <flux:input
                wire:model="giro_sii"
                label="{{ __('Giro SII') }}"
                placeholder="494110"
                hint="{{ __('Código de actividad económica según SII') }}"
            />

            <div>
                <flux:text class="mb-2 text-sm font-medium">{{ __('Logo/Imagen de Perfil') }}</flux:text>
                @if ($logoPreview)
                    <div class="mb-2">
                        <img src="{{ $logoPreview }}" alt="Preview" class="h-24 w-24 rounded-lg object-cover">
                    </div>
                @endif
                <input
                    type="file"
                    wire:model="logo"
                    accept="image/*"
                    class="block w-full text-sm text-app-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-app-primary file:text-white hover:file:bg-app-primary/90"
                >
                @error('logo')
                    <flux:text class="mt-1 text-sm text-red-600">{{ $message }}</flux:text>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('provider.dashboard') }}" wire:navigate>
                    <flux:button variant="ghost">{{ __('Cancelar') }}</flux:button>
                </a>
                <flux:button variant="primary" type="submit" class="min-h-[44px] px-6 py-2.5">
                    {{ __('Guardar Cambios') }}
                </flux:button>
            </div>
        </div>
    </form>
</section>
