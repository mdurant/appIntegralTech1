<section class="mx-auto w-full max-w-4xl space-y-4 px-4 py-6 sm:space-y-6 sm:px-6">
    <livewire:services.detail-modal />

    <div class="space-y-2">
        <flux:heading size="lg" class="text-2xl sm:text-3xl">{{ $serviceRequest->title }}</flux:heading>
        <flux:text class="text-xs sm:text-sm">
            @if ($serviceRequest->reference_id)
                <span class="font-mono text-app-muted">{{ $serviceRequest->reference_id }}</span>
                ·
            @endif
            {{ $serviceRequest->category?->parent?->name }} / {{ $serviceRequest->category?->name }}
            · {{ $serviceRequest->tenant?->name }}
            · <span class="font-medium">{{ $serviceRequest->status->value }}</span>
        </flux:text>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 sm:rounded-xl sm:p-5">
        <flux:heading size="md">{{ __('Detalle') }}</flux:heading>

        @if ($this->detailRows->count())
            <div class="mt-4 divide-y divide-zinc-200 rounded-lg border border-zinc-200 bg-white dark:divide-zinc-700 dark:border-zinc-700 dark:bg-zinc-900">
                @foreach ($this->detailRows as $row)
                    <div class="grid gap-2 p-4 md:grid-cols-2">
                        <flux:text class="text-sm font-medium">{{ $row['label'] }}</flux:text>
                        <flux:text class="text-sm">{{ $row['value'] }}</flux:text>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($serviceRequest->notes)
            <flux:heading size="sm" class="mt-6">{{ __('Nota del particular') }}</flux:heading>
            <flux:text class="mt-2 whitespace-pre-line">{{ $serviceRequest->notes }}</flux:text>
        @else
            <flux:text class="mt-3 whitespace-pre-line">{{ $serviceRequest->description }}</flux:text>
        @endif
    </div>

    {{-- Contacto: Mostrar datos completos u ofuscados según pago --}}
    @if (!auth()->user()->isClient() && !auth()->user()->isGuest() && $serviceRequest->status->value === 'published')
        @if ($this->hasPayment)
            {{-- Usuario con pago: Mostrar datos completos --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="md">{{ __('Contacto y localización') }}</flux:heading>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                    <flux:text class="text-xs sm:text-sm"><span class="font-medium">{{ __('Nombre') }}:</span> {{ $serviceRequest->contact_name ?? '—' }}</flux:text>
                    <flux:text class="text-xs sm:text-sm"><span class="font-medium">{{ __('Email') }}:</span> {{ $serviceRequest->contact_email ?? '—' }}</flux:text>
                    <flux:text class="text-xs sm:text-sm"><span class="font-medium">{{ __('Teléfono') }}:</span> {{ $serviceRequest->contact_phone ?? '—' }}</flux:text>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <flux:text class="text-sm"><span class="font-medium">{{ __('Localización') }}:</span> {{ $serviceRequest->location_display }}</flux:text>
                    <flux:text class="text-sm"><span class="font-medium">{{ __('Dirección') }}:</span> {{ $serviceRequest->address ?? '—' }}</flux:text>
                </div>
                <div class="mt-4">
                    <a href="{{ route('services.contact', $serviceRequest) }}" wire:navigate>
                        <flux:button variant="primary">{{ __('Ver datos completos') }}</flux:button>
                    </a>
                </div>
            </div>
        @else
            {{-- Usuario sin pago: Mostrar datos ofuscados --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="md">{{ __('Contacto y localización') }}</flux:heading>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                    <flux:text class="text-xs sm:text-sm">
                        <span class="font-medium">{{ __('Nombre') }}:</span>
                        {{ $serviceRequest->obfuscated_contact_name }}
                    </flux:text>
                    <flux:text class="text-xs sm:text-sm">
                        <span class="font-medium">{{ __('Email') }}:</span>
                        <span class="font-mono">{{ $serviceRequest->obfuscated_email }}</span>
                    </flux:text>
                    <flux:text class="text-xs sm:text-sm">
                        <span class="font-medium">{{ __('Teléfono') }}:</span>
                        <span class="font-mono">{{ $serviceRequest->obfuscated_phone }}</span>
                    </flux:text>
                </div>
                <div class="mt-3">
                    <flux:text class="text-sm">
                        <span class="font-medium">{{ __('Localización') }}:</span>
                        {{ $serviceRequest->location_display }}
                    </flux:text>
                </div>
                <div class="mt-4 flex flex-wrap gap-3">
                    <button
                        wire:click="$dispatch('open-detail-modal', { serviceRequestId: {{ $serviceRequest->id }} })"
                        class="rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                    >
                        {{ __('Ver detalle') }}
                    </button>
                    <a href="{{ route('services.payment', $serviceRequest) }}" wire:navigate>
                        <flux:button variant="primary">{{ __('Ver contacto') }}</flux:button>
                    </a>
                </div>
            </div>
        @endif
    @else
        {{-- Cliente o invitado: Mostrar datos completos si es el dueño --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="md">{{ __('Contacto y localización') }}</flux:heading>
            <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                <flux:text class="text-xs sm:text-sm"><span class="font-medium">{{ __('Nombre') }}:</span> {{ $serviceRequest->contact_name ?? '—' }}</flux:text>
                <flux:text class="text-xs sm:text-sm"><span class="font-medium">{{ __('Email') }}:</span> {{ $serviceRequest->contact_email ?? '—' }}</flux:text>
                <flux:text class="text-xs sm:text-sm"><span class="font-medium">{{ __('Teléfono') }}:</span> {{ $serviceRequest->contact_phone ?? '—' }}</flux:text>
            </div>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <flux:text class="text-sm"><span class="font-medium">{{ __('Localización') }}:</span> {{ $serviceRequest->location_text ?? '—' }}</flux:text>
                <flux:text class="text-sm"><span class="font-medium">{{ __('Dirección') }}:</span> {{ $serviceRequest->address ?? '—' }}</flux:text>
            </div>
        </div>
    @endif

    @if (count($this->attachmentUrls))
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="md">{{ __('Imágenes') }}</flux:heading>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                @foreach ($this->attachmentUrls as $url)
                    <a href="{{ $url }}" target="_blank" class="block overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <img src="{{ $url }}" alt="" class="aspect-square w-full object-cover" />
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if (!auth()->user()->isGuest() && !auth()->user()->isClient() && $serviceRequest->status->value === 'published')
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="md">{{ __('Tu presupuesto') }}</flux:heading>

            @if ($this->myBid)
                <flux:text class="mt-2 text-sm">
                    {{ __('Estado') }}: <span class="font-medium">{{ $this->myBid->status->value }}</span>
                    · {{ __('Monto') }}: <span class="font-medium">${{ $this->myBid->formatted_amount }} {{ $this->myBid->currency }}</span>
                    @if ($this->myBid->valid_until)
                        · {{ __('Vigencia') }}: <span class="font-medium">{{ $this->myBid->valid_until->format('Y-m-d') }}</span>
                    @endif
                </flux:text>
            @endif

            <form wire:submit="submit" class="mt-4 space-y-4">
                <flux:input wire:model="amount" :label="__('Monto')" type="number" step="1" inputmode="numeric" class="min-h-[44px]" />
                <flux:textarea wire:model="message" :label="__('Mensaje (opcional)')" rows="4" class="min-h-[120px]" />
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="min-h-[44px] px-6 py-2.5">{{ __('Enviar presupuesto') }}</flux:button>
                </div>
                <flux:text class="text-sm">
                    {{ __('La cotización tiene una vigencia de 15 días.') }}
                </flux:text>
            </form>
        </div>
    @endif

    @if (auth()->user()->isClient() && $this->bidsForClient->count())
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="md">{{ __('Presupuestos recibidos') }}</flux:heading>
            <div class="mt-4 space-y-3">
                @foreach ($this->bidsForClient as $bid)
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <flux:text class="text-sm font-medium">{{ $bid->user?->name }} · ${{ $bid->formatted_amount }} {{ $bid->currency }}</flux:text>
                        <flux:text class="mt-1 text-sm">{{ __('Estado') }}: {{ $bid->status->value }}</flux:text>
                        @if ($bid->message)
                            <flux:text class="mt-2 text-sm whitespace-pre-line">{{ $bid->message }}</flux:text>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

