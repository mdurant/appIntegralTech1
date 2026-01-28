<div>
    @if ($show)
        <div 
            class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center sm:p-6"
            x-data="{ show: @entangle('show') }"
            x-show="show"
            x-cloak
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            {{-- Overlay --}}
            <div 
                class="fixed inset-0 bg-black/50 transition-opacity"
                @click="$wire.show = false"
            ></div>

            {{-- Modal --}}
            <div 
                class="relative w-full max-w-2xl rounded-xl bg-app-surface shadow-xl border border-app-border"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div class="p-6 sm:p-8">
                    {{-- Header --}}
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold text-app-text mb-2">
                            {{ __('Gestión de Cookies') }}
                        </h2>
                        <p class="text-sm text-app-muted leading-relaxed">
                            {{ __('Al hacer clic en «Aceptar todas las cookies», usted acepta el almacenamiento de cookies en su dispositivo para mejorar la navegación del sitio, analizar el uso del mismo y ayudarnos en nuestras iniciativas de marketing.') }}
                        </p>
                    </div>

                    @if (!$showSettings)
                        {{-- Botones principales --}}
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                            <flux:button 
                                variant="primary" 
                                wire:click="acceptAll"
                                class="flex-1 min-h-[44px]"
                            >
                                {{ __('Aceptar todas las cookies') }}
                            </flux:button>
                            
                            <flux:button 
                                variant="outline" 
                                wire:click="rejectAll"
                                class="flex-1 min-h-[44px]"
                            >
                                {{ __('Rechazar todas') }}
                            </flux:button>
                            
                            <flux:button 
                                variant="ghost" 
                                wire:click="openSettings"
                                class="flex-1 min-h-[44px] sm:flex-initial sm:px-4"
                            >
                                {{ __('Configuración de cookies') }}
                            </flux:button>
                        </div>
                    @else
                        {{-- Panel de configuración --}}
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-app-text mb-4">
                                    {{ __('Personalizar preferencias de cookies') }}
                                </h3>
                                <p class="text-sm text-app-muted mb-6">
                                    {{ __('Seleccione qué tipos de cookies desea permitir. Puede cambiar estas preferencias en cualquier momento.') }}
                                </p>
                            </div>

                            {{-- Marketing Cookies --}}
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-app-text mb-1">
                                            {{ __('Cookies de Marketing') }}
                                        </h4>
                                        <p class="text-sm text-app-muted">
                                            {{ __('Estas cookies nos permiten conocer al usuario visitante y entregar mejor experiencia. Se utilizan para analizar el uso del sitio y apoyar nuestras iniciativas de marketing.') }}
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            wire:model.live="marketingEnabled"
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-app-border peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-app-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                                    </label>
                                </div>
                            </div>

                            {{-- User Experience Cookies --}}
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-app-text mb-1">
                                            {{ __('Cookies de Experiencia de Usuario') }}
                                        </h4>
                                        <p class="text-sm text-app-muted">
                                            {{ __('Estas cookies permiten recordar sus preferencias (idioma, tema) y mejorar su experiencia de navegación en el sitio.') }}
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            wire:model.live="userExperienceEnabled"
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-app-border peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-app-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                                    </label>
                                </div>
                            </div>

                            {{-- Botones de configuración --}}
                            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-4 border-t border-app-border">
                                <flux:button 
                                    variant="primary" 
                                    wire:click="saveSettings"
                                    class="flex-1 min-h-[44px]"
                                >
                                    {{ __('Guardar preferencias') }}
                                </flux:button>
                                
                                <flux:button 
                                    variant="outline" 
                                    wire:click="closeSettings"
                                    class="flex-1 min-h-[44px] sm:flex-initial sm:px-6"
                                >
                                    {{ __('Cancelar') }}
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
