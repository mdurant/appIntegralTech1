<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Admin') }}</flux:heading>
        <flux:text>{{ __('Monitoreo y control básico de la plataforma.') }}</flux:text>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <a href="{{ route('admin.tenants') }}" wire:navigate class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-brand-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <flux:heading size="sm">{{ __('Organizaciones') }}</flux:heading>
                <div class="grid size-10 place-items-center rounded-lg bg-purple-50 text-purple-700 group-hover:bg-purple-100 dark:bg-purple-900/20 dark:text-purple-400">
                    <flux:icon.building-2 class="size-5" />
                </div>
            </div>
            <flux:text class="text-2xl font-semibold">{{ $tenantsCount }}</flux:text>
        </a>

        <a href="{{ route('admin.users') }}" wire:navigate class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-brand-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <flux:heading size="sm">{{ __('Usuarios') }}</flux:heading>
                <div class="grid size-10 place-items-center rounded-lg bg-blue-50 text-blue-700 group-hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <flux:text class="text-2xl font-semibold">{{ $usersCount }}</flux:text>
        </a>

        <a href="{{ route('admin.service-requests') }}" wire:navigate class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-brand-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <flux:heading size="sm">{{ __('Solicitudes') }}</flux:heading>
                <div class="grid size-10 place-items-center rounded-lg bg-green-50 text-green-700 group-hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400">
                    <flux:icon.book-open-text class="size-5" />
                </div>
            </div>
            <flux:text class="text-2xl font-semibold">{{ $requestsCount }}</flux:text>
        </a>

        <a href="{{ route('admin.service-bids') }}" wire:navigate class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-brand-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <flux:heading size="sm">{{ __('Presupuestos') }}</flux:heading>
                <div class="grid size-10 place-items-center rounded-lg bg-amber-50 text-amber-700 group-hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <flux:text class="text-2xl font-semibold">{{ $bidsCount }}</flux:text>
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <a href="{{ route('admin.service-categories') }}" wire:navigate class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-brand-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <flux:heading size="sm">{{ __('Categorías') }}</flux:heading>
                <div class="grid size-10 place-items-center rounded-lg bg-indigo-50 text-indigo-700 group-hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400">
                    <flux:icon.layout-grid class="size-5" />
                </div>
            </div>
            <flux:text class="mt-1">{{ __('Taxonomía (categoría/subcategoría).') }}</flux:text>
        </a>

        <a href="{{ route('admin.service-form') }}" wire:navigate class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-brand-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <flux:heading size="sm">{{ __('Formulario') }}</flux:heading>
                <div class="grid size-10 place-items-center rounded-lg bg-rose-50 text-rose-700 group-hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <flux:text class="mt-1">{{ __('Campos dinámicos por subcategoría.') }}</flux:text>
        </a>
    </div>
</section>

