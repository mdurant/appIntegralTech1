<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Servicios publicados') }}</flux:heading>
            <flux:text>{{ __('Explora necesidades publicadas y oferta si puedes resolverlas.') }}</flux:text>
        </div>

        <div class="w-full md:max-w-sm">
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
                class="block rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-zinc-300 hover:shadow dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600"
                wire:navigate
            >
                <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                    <div class="space-y-1">
                        <flux:heading size="md">{{ $request->title }}</flux:heading>
                        <flux:text class="text-sm">
                            {{ $request->category?->name }}
                            · {{ $request->tenant?->name }}
                        </flux:text>
                    </div>
                    <flux:text class="text-sm font-medium">{{ __('Ver detalle') }}</flux:text>
                </div>

                <flux:text class="mt-3 text-sm">
                    {{ \Illuminate\Support\Str::limit($request->description, 200) }}
                </flux:text>
            </a>
        @empty
            <flux:text>{{ __('No hay servicios publicados aún.') }}</flux:text>
        @endforelse
    </div>
</section>

