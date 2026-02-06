<section class="mx-auto w-full max-w-6xl space-y-6 px-6 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Explorar solicitudes de cotización publicadas') }}</flux:heading>
            <flux:text class="text-app-muted">{{ __('Encuentra oportunidades reales y decide si quieres cotizar (requiere cuenta).') }}</flux:text>
        </div>

        <div class="w-full md:max-w-xl">
            <form class="flex flex-col gap-3 sm:flex-row sm:items-center" method="GET" action="{{ route('public.services.browse') }}">
                <div class="flex-1">
                    <div class="flex items-center gap-3 rounded-xl border border-app-border bg-app-surface px-4 py-3 shadow-sm">
                        <flux:icon.magnifying-glass class="size-5 text-app-muted" />
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Busca por servicio, palabra clave o necesidad…"
                            class="w-full bg-transparent text-sm outline-hidden placeholder:text-app-muted"
                        />
                    </div>
                </div>

                <div class="sm:w-[260px]">
                    <select
                        name="categoryId"
                        class="select2 w-full rounded-xl border border-app-border bg-app-surface px-4 py-3 text-sm shadow-sm outline-hidden"
                    >
                        <option value="">{{ __('Todas las categorías') }}</option>
                        @foreach ($this->categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('categoryId') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <flux:button variant="primary" type="submit" class="sm:w-auto">
                    {{ __('Buscar') }}
                </flux:button>
            </form>
        </div>
    </div>

    <div class="grid gap-3">
        @forelse ($this->serviceRequests as $request)
            <div class="rounded-2xl border border-app-border bg-app-surface p-5 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div class="space-y-1">
                        <flux:heading size="md">{{ $request->title }}</flux:heading>
                        <flux:text class="text-sm text-app-muted">
                            {{ $request->category?->name }} · {{ $request->tenant?->name }}
                            @if ($request->location_text)
                                · {{ $request->location_text }}
                            @endif
                        </flux:text>
                    </div>

                    <div class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('services.show', $request) }}">
                                <flux:button variant="primary">{{ __('Ver detalle') }}</flux:button>
                            </a>
                        @else
                            <a href="{{ route('login') }}">
                                <flux:button variant="primary">{{ __('Iniciar sesión para ver detalle') }}</flux:button>
                            </a>
                        @endauth
                    </div>
                </div>

                <flux:text class="mt-3 text-sm">
                    {{ \Illuminate\Support\Str::limit($request->notes ?? $request->description, 220) }}
                </flux:text>
            </div>
        @empty
            <div class="rounded-2xl border border-app-border bg-app-surface p-8 text-center">
                <flux:heading size="md">{{ __('Sin resultados') }}</flux:heading>
                <flux:text class="mt-2 text-app-muted">{{ __('Prueba con otra palabra clave o categoría.') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>

