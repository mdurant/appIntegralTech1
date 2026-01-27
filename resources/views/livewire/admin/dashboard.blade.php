<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Admin') }}</flux:heading>
        <flux:text>{{ __('Monitoreo y control básico de la plataforma.') }}</flux:text>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <a href="{{ route('admin.tenants') }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Organizaciones') }}</flux:heading>
            <flux:text class="mt-2 text-2xl font-semibold">{{ $tenantsCount }}</flux:text>
        </a>

        <a href="{{ route('admin.users') }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Usuarios') }}</flux:heading>
            <flux:text class="mt-2 text-2xl font-semibold">{{ $usersCount }}</flux:text>
        </a>

        <a href="{{ route('admin.service-requests') }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Solicitudes') }}</flux:heading>
            <flux:text class="mt-2 text-2xl font-semibold">{{ $requestsCount }}</flux:text>
        </a>

        <a href="{{ route('admin.service-bids') }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Presupuestos') }}</flux:heading>
            <flux:text class="mt-2 text-2xl font-semibold">{{ $bidsCount }}</flux:text>
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <a href="{{ route('admin.service-categories') }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Categorías') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Taxonomía (categoría/subcategoría).') }}</flux:text>
        </a>

        <a href="{{ route('admin.service-form') }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Formulario') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Campos dinámicos por subcategoría.') }}</flux:text>
        </a>
    </div>
</section>

