<section class="w-full">
    <flux:heading class="sr-only">{{ __('Pagos y Planes') }}</flux:heading>

    <x-settings.layout :heading="__('Pagos y Planes')" :subheading="__('Gestiona tu plan, métodos de pago e historial de suscripciones')">
        <div class="mt-6 space-y-8">
            {{-- Plan actual --}}
            <div class="rounded-xl border border-app-border bg-app-card p-6">
                <flux:heading size="lg">{{ __('Plan actual') }}</flux:heading>
                <p class="mt-2 text-app-muted">{{ $subscriptionTier->label() }}</p>
                @if($subscriptionEndsAt)
                    <p class="mt-1 text-sm text-app-muted">{{ __('Válido hasta') }}: {{ $subscriptionEndsAt->translatedFormat('d \d\e F \d\e Y') }}</p>
                @endif
                <div class="mt-4 flex flex-wrap gap-3">
                    @if($subscriptionTier === \App\SubscriptionTier::Independent)
                        <flux:button variant="primary" wire:click="$set('selectedPlanTier', 'pyme')">{{ __('Upgrade a Pro') }}</flux:button>
                        <flux:button variant="ghost" wire:click="$set('selectedPlanTier', 'enterprise')">{{ __('Upgrade a Enterprise') }}</flux:button>
                    @else
                        @if($subscriptionTier === \App\SubscriptionTier::Pyme)
                            <flux:button variant="primary" wire:click="$set('selectedPlanTier', 'enterprise')">{{ __('Upgrade a Enterprise') }}</flux:button>
                        @endif
                        <flux:button variant="ghost" wire:click="cancelSubscription">{{ __('Cancelar suscripción') }}</flux:button>
                    @endif
                </div>
                @if($selectedPlanTier)
                    <div class="mt-4 rounded-lg border border-app-border bg-app-subtle p-4">
                        <p class="text-sm">{{ __('Confirmar cambio a') }}: {{ \App\SubscriptionTier::from($selectedPlanTier)->label() }}</p>
                        <div class="mt-3 flex gap-2">
                            <flux:button variant="primary" size="sm" wire:click="changePlan">{{ __('Confirmar') }}</flux:button>
                            <flux:button variant="ghost" size="sm" wire:click="$set('selectedPlanTier', null)">{{ __('Cancelar') }}</flux:button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Métodos de pago (placeholder) --}}
            <div class="rounded-xl border border-app-border bg-app-card p-6">
                <flux:heading size="lg">{{ __('Métodos de pago') }}</flux:heading>
                <p class="mt-2 text-app-muted">{{ __('Próximamente: tarjeta, PayPal y más.') }}</p>
            </div>

            {{-- Historial de suscripciones --}}
            <div class="rounded-xl border border-app-border bg-app-card p-6">
                <flux:heading size="lg">{{ __('Historial de suscripciones') }}</flux:heading>
                @if($subscriptions->isEmpty())
                    <p class="mt-2 text-app-muted">{{ __('Aún no tienes registros de suscripción.') }}</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full min-w-[400px] text-left text-sm">
                            <thead>
                                <tr class="border-b border-app-border text-app-muted">
                                    <th class="pb-2 font-medium">{{ __('Plan') }}</th>
                                    <th class="pb-2 font-medium">{{ __('Inicio') }}</th>
                                    <th class="pb-2 font-medium">{{ __('Fin') }}</th>
                                    <th class="pb-2 font-medium">{{ __('Monto') }}</th>
                                    <th class="pb-2 font-medium">{{ __('Método') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $sub)
                                    <tr class="border-b border-app-border/60">
                                        <td class="py-2">{{ $sub->subscription_tier->label() }}</td>
                                        <td class="py-2">{{ $sub->starts_at?->translatedFormat('d/m/Y') }}</td>
                                        <td class="py-2">{{ $sub->ends_at?->translatedFormat('d/m/Y') }}</td>
                                        <td class="py-2">{{ $sub->amount ? number_format($sub->amount, 0, ',', '.') : '—' }} {{ $sub->currency ?? '' }}</td>
                                        <td class="py-2">{{ $sub->payment_method ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- API Keys --}}
            <div class="rounded-xl border border-app-border bg-app-card p-6">
                <flux:heading size="lg">{{ __('API Keys') }}</flux:heading>
                @if($hasApiAccess)
                    <p class="mt-2 text-app-muted">{{ __('Crea claves para acceder a la API. El valor solo se muestra una vez al crear.') }}</p>

                    @if($newlyCreatedToken)
                        <div class="mt-4 rounded-lg border border-amber-500/50 bg-amber-500/10 p-4 dark:border-amber-400/30 dark:bg-amber-400/10" x-data="{ token: @js($newlyCreatedToken) }">
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">{{ __('La clave se ha creado. Cópiala ahora; no se volverá a mostrar.') }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="text" readonly class="flux-input w-full rounded-lg border border-app-border bg-app-input px-3 py-2 font-mono text-sm" value="{{ $newlyCreatedToken }}" />
                                <flux:button variant="ghost" size="sm" type="button" x-on:click="navigator.clipboard.writeText(token)">{{ __('Copiar') }}</flux:button>
                            </div>
                            <flux:button variant="ghost" size="sm" class="mt-2" wire:click="dismissNewToken">{{ __('Cerrar') }}</flux:button>
                        </div>
                    @else
                        <form wire:submit="createApiKey" class="mt-4 flex flex-wrap items-end gap-4">
                            <flux:select wire:model="apiKeyType" label="{{ __('Tipo de clave') }}">
                                <option value="full_access">{{ __('Full Access') }}</option>
                                <option value="read_only">{{ __('Solo lectura') }}</option>
                            </flux:select>
                            <flux:input wire:model="apiKeyName" label="{{ __('Nombre') }}" placeholder="{{ __('ej. Server Key 1') }}" class="min-w-[180px]" />
                            <flux:button variant="primary" type="submit">{{ __('Crear clave') }}</flux:button>
                        </form>
                    @endif

                    <div class="mt-6">
                        <flux:heading size="md">{{ __('Tus claves') }}</flux:heading>
                        @if(count($apiKeys) === 0)
                            <p class="mt-2 text-app-muted">{{ __('No tienes claves API.') }}</p>
                        @else
                            <ul class="mt-2 space-y-2">
                                @foreach($apiKeys as $key)
                                    <li class="flex items-center justify-between rounded-lg border border-app-border bg-app-subtle px-4 py-3">
                                        <div>
                                            <span class="font-medium">{{ $key['name'] }}</span>
                                            <span class="ml-2 text-app-muted">({{ $key['type'] === 'full_access' ? __('Full Access') : __('Solo lectura') }})</span>
                                            <span class="ml-2 text-xs text-app-muted">•••• •••• ••••</span>
                                            <span class="ml-2 text-xs text-app-muted">{{ $key['created_at']->translatedFormat('d/m/Y H:i') }}</span>
                                        </div>
                                        <flux:button variant="ghost" size="sm" wire:click="revokeApiKey('{{ $key['id'] }}')" wire:confirm="{{ __('¿Revocar esta clave? No podrás deshacerlo.') }}">{{ __('Revocar') }}</flux:button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @else
                    <p class="mt-2 text-app-muted">{{ __('Actualiza a Pro o Enterprise para usar la API y crear claves.') }}</p>
                @endif
            </div>
        </div>
    </x-settings.layout>
</section>
