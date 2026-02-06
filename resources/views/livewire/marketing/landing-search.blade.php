<div class="w-full">
    <form wire:submit="search" class="w-full">
        <div class="flex flex-col gap-3 rounded-2xl border border-app-border bg-app-surface p-3 shadow-sm sm:flex-row sm:items-center">
            <div class="flex flex-1 items-center gap-3 rounded-xl bg-app-surface-2 px-4 py-3 ring-1 ring-transparent focus-within:ring-brand-200">
                <flux:icon.magnifying-glass class="size-5 text-app-muted" />
                <input
                    type="text"
                    wire:model.live.debounce.250ms="q"
                    placeholder="¿Qué servicio técnico necesitas hoy?"
                    class="w-full bg-transparent text-sm outline-hidden placeholder:text-app-muted"
                    autocomplete="off"
                />
            </div>

            <flux:button variant="primary" type="submit" class="w-full sm:w-auto">
                {{ __('Buscar solicitudes de cotización') }}
            </flux:button>
        </div>
    </form>

    @if (trim($q) !== '')
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            <div class="rounded-xl border border-app-border bg-app-surface p-4">
                <flux:heading size="sm">{{ __('Categorías sugeridas') }}</flux:heading>
                <div class="mt-3 grid gap-2">
                    @forelse ($this->categories as $cat)
                        <a
                            href="{{ route('public.services.browse', ['categoryId' => $cat->id, 'q' => trim($q)]) }}"
                            class="flex items-center justify-between rounded-lg border border-app-border bg-app-surface-2 px-3 py-2 hover:bg-brand-50"
                        >
                            <span class="text-sm font-medium">{{ $cat->name }}</span>
                            <flux:icon.arrow-right class="size-4 text-app-muted" />
                        </a>
                    @empty
                        <flux:text class="mt-2 text-sm text-app-muted">{{ __('No encontramos categorías con ese término.') }}</flux:text>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-app-border bg-app-surface p-4">
                <flux:heading size="sm">{{ __('Solicitudes publicadas') }}</flux:heading>
                <div class="mt-3 grid gap-2">
                    @forelse ($this->requests as $req)
                        <div class="rounded-lg border border-app-border bg-app-surface-2 px-3 py-2">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-medium">{{ $req->title }}</div>
                                    <div class="mt-0.5 truncate text-xs text-app-muted">
                                        {{ $req->category?->name ?? __('Servicio') }}
                                    </div>
                                </div>
                                <a href="{{ route('login') }}" class="shrink-0 text-xs font-medium text-brand-700 hover:text-brand-800">
                                    {{ __('Ver') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <flux:text class="mt-2 text-sm text-app-muted">
                            {{ __('Aún no hay solicitudes publicadas que coincidan.') }}
                        </flux:text>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>

