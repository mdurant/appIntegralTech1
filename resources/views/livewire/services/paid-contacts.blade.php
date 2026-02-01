<section class="mx-auto w-full max-w-6xl space-y-4 px-4 py-6 sm:space-y-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg" class="text-2xl sm:text-3xl">{{ __('Contactos comprados') }}</flux:heading>
        <flux:text class="text-sm sm:text-base">{{ __('Servicios cuyo contacto ya compraste. Aquí ves los datos completos para tu modelo de negocio.') }}</flux:text>
    </div>

    <div class="space-y-3">
        @forelse ($this->paidServiceRequests as $request)
            <div class="block min-h-[140px] rounded-lg border border-zinc-200 bg-white p-4 shadow-sm transition hover:border-zinc-300 hover:shadow dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600 sm:min-h-[160px] sm:rounded-xl sm:p-5" wire:key="paid-{{ $request->id }}">
                <div class="flex flex-col gap-3 sm:gap-4">
                    {{-- Título y Categoría --}}
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1 space-y-1">
                            <flux:heading size="md" class="text-base sm:text-lg">{{ $request->title }}</flux:heading>
                            <flux:text class="text-xs sm:text-sm text-app-muted">
                                {{ $request->category?->name }}
                            </flux:text>
                        </div>
                        <a href="{{ route('services.contact', $request) }}" wire:navigate class="shrink-0 text-xs font-medium text-brand-700 hover:text-brand-800 sm:text-sm">
                            {{ __('Ver datos completos') }} »
                        </a>
                    </div>

                    {{-- Localización --}}
                    <div class="flex items-center gap-2 text-xs sm:text-sm">
                        <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <flux:text class="text-app-muted">{{ $request->location_display }}</flux:text>
                    </div>

                    {{-- Datos de contacto visibles (ya pagados) --}}
                    <div class="grid gap-2 border-t border-zinc-200 pt-3 dark:border-zinc-700 sm:grid-cols-3">
                        <div class="flex items-center gap-2 text-xs sm:text-sm">
                            <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <flux:text>{{ $request->contact_name ?? '—' }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2 text-xs sm:text-sm">
                            <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <flux:text class="font-mono">{{ $request->contact_phone ?? '—' }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2 text-xs sm:text-sm">
                            <svg class="h-4 w-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <flux:text class="font-mono">{{ $request->contact_email ?? '—' }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-lg border border-zinc-200 bg-white p-8 text-center dark:border-zinc-700 dark:bg-zinc-900 sm:rounded-xl">
                <flux:text class="text-sm sm:text-base">{{ __('Aún no has comprado el contacto de ningún servicio.') }}</flux:text>
                <p class="mt-2 text-sm text-app-muted">{{ __('En') }} <a href="{{ route('services.browse') }}" wire:navigate class="font-medium text-brand-600 hover:underline">{{ __('Servicios') }}</a> {{ __('puedes explorar y pagar para ver datos de contacto.') }}</p>
            </div>
        @endforelse
    </div>
</section>
