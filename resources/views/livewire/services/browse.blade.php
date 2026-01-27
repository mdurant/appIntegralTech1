<section class="mx-auto w-full max-w-6xl space-y-4 px-4 py-6 sm:space-y-6 sm:px-6">
    <div class="flex flex-col gap-4 sm:gap-3 md:flex-row md:items-end md:justify-between">
        <div class="space-y-2">
            <flux:heading size="lg" class="text-2xl sm:text-3xl">{{ __('Servicios publicados') }}</flux:heading>
            <flux:text class="text-sm sm:text-base">{{ __('Explora necesidades publicadas y oferta si puedes resolverlas.') }}</flux:text>
        </div>

        <div class="w-full sm:min-h-[44px] md:max-w-sm">
            <flux:select wire:model.live="categoryId" :label="__('Categoría')" placeholder="{{ __('Todas') }}">
                @foreach ($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div class="space-y-3">
        @forelse ($this->serviceRequests as $request)
            <a
                href="{{ route('services.show', $request) }}"
                class="block min-h-[120px] rounded-lg border border-zinc-200 bg-white p-4 shadow-sm transition hover:border-zinc-300 hover:shadow dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600 sm:min-h-[140px] sm:rounded-xl sm:p-5"
                wire:navigate
            >
                <div class="flex flex-col gap-2 sm:gap-3 md:flex-row md:items-start md:justify-between">
                    <div class="min-w-0 flex-1 space-y-1">
                        <flux:heading size="md" class="text-base sm:text-lg">{{ $request->title }}</flux:heading>
                        <flux:text class="text-xs sm:text-sm">
                            {{ $request->category?->name }}
                            · {{ $request->tenant?->name }}
                        </flux:text>
                    </div>
                    <flux:text class="shrink-0 text-xs font-medium text-brand-700 sm:text-sm">{{ __('Ver detalle') }} →</flux:text>
                </div>

                <flux:text class="mt-3 text-xs sm:text-sm">
                    {{ \Illuminate\Support\Str::limit($request->description, 200) }}
                </flux:text>
            </a>
        @empty
            <div class="rounded-lg border border-zinc-200 bg-white p-8 text-center dark:border-zinc-700 dark:bg-zinc-900 sm:rounded-xl">
                <flux:text class="text-sm sm:text-base">{{ __('No hay servicios publicados aún.') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>

