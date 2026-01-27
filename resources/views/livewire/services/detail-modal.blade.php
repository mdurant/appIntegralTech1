<div>
    @if ($show && $this->serviceRequest)
        <flux:modal wire:model="show" name="detail-modal" class="max-w-6xl">
            <div class="grid gap-6 lg:grid-cols-2">
            {{-- Panel Izquierdo: Detalles del Servicio --}}
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Trabajo a realizar') }}</flux:heading>
                    <flux:heading size="md" class="mt-2">{{ $this->serviceRequest->title }}</flux:heading>
                </div>

                <div>
                    <flux:text class="text-sm font-medium text-app-muted">{{ __('Categoría') }}</flux:text>
                    <a href="#" class="text-sm text-brand-700 hover:text-brand-800">
                        {{ $this->serviceRequest->category?->name }}
                    </a>
                </div>

                @if ($this->detailRows->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($this->detailRows as $row)
                            <div>
                                <flux:text class="text-xs font-medium text-app-muted">{{ $row['label'] }}</flux:text>
                                <flux:text class="text-sm">{{ $row['value'] }}</flux:text>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <flux:text class="text-sm font-medium text-app-muted">{{ __('Localización del trabajo') }}</flux:text>
                    <div class="mt-1 flex items-center gap-2">
                        <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <flux:text class="text-sm">{{ $this->serviceRequest->location_display }}</flux:text>
                    </div>
                    {{-- Nota: No mostrar dirección completa, solo región + comuna --}}
                </div>

                @if ($this->serviceRequest->published_at)
                    <div>
                        <flux:text class="text-xs text-app-muted">
                            {{ __('Publicado hace') }} {{ $this->serviceRequest->published_at->diffForHumans() }}
                        </flux:text>
                    </div>
                @endif
            </div>

            {{-- Panel Derecho: Contacto Ofuscado --}}
            <div class="space-y-4 rounded-xl border border-app-border bg-app-surface-2 p-6">
                <div>
                    <flux:heading size="md">{{ __('Contacta con tu potencial cliente') }}</flux:heading>
                    <flux:text class="mt-2 text-sm text-app-muted">
                        {{ __('No te pierdas la oportunidad de conseguir este trabajo por solo:') }}
                    </flux:text>
                </div>

                {{-- Card con datos ofuscados --}}
                <div class="rounded-lg border border-app-border bg-app-surface p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <flux:text class="text-xs text-app-muted">{{ __('Precio') }}</flux:text>
                        <flux:text class="text-lg font-bold text-brand-700">$6.105 CLP</flux:text>
                    </div>

                    <div class="space-y-2 border-t border-app-border pt-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <flux:text class="text-sm">{{ $this->serviceRequest->obfuscated_contact_name }}</flux:text>
                        </div>

                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <flux:text class="font-mono text-sm">{{ $this->serviceRequest->obfuscated_email }}</flux:text>
                        </div>

                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <flux:text class="font-mono text-sm">{{ $this->serviceRequest->obfuscated_phone }}</flux:text>
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <flux:text class="text-xs text-green-600">{{ __('Teléfono validado') }}</flux:text>
                        </div>
                    </div>
                </div>

                {{-- Botón Ver Contacto --}}
                <flux:button
                    wire:click="goToPayment"
                    variant="primary"
                    class="w-full min-h-[48px] bg-red-600 hover:bg-red-700"
                >
                    {{ __('Ver contacto') }}
                </flux:button>

                {{-- Mensaje de urgencia --}}
                <div class="flex items-center gap-2 text-sm text-app-muted">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    <flux:text class="text-sm">{{ __('↑ Date prisa, ¡sé el primero!') }}</flux:text>
                </div>
            </div>
        </div>
        </flux:modal>
    @endif
</div>
