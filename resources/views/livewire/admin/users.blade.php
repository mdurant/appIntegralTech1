<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Usuarios') }}</flux:heading>
            <flux:text>{{ __('Roles globales y organización actual.') }}</flux:text>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="space-y-3">
            @foreach ($this->users as $user)
                <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-12 md:items-center">
                    <div class="md:col-span-4">
                        <flux:heading size="sm">{{ $user->name }}</flux:heading>
                        <flux:text class="text-sm">{{ $user->email }}</flux:text>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Rol') }}</label>
                        <select
                            class="mt-1 w-full rounded-md border-zinc-200 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-900"
                            wire:change="updateRole({{ $user->id }}, $event.target.value)"
                        >
                            @foreach ($roles as $role)
                                <option value="{{ $role->value }}" @selected($user->system_role?->value === $role->value)>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Organización actual') }}</label>
                        <select
                            class="mt-1 w-full rounded-md border-zinc-200 bg-white text-sm dark:border-zinc-700 dark:bg-zinc-900"
                            wire:change="updateTenant({{ $user->id }}, $event.target.value ? parseInt($event.target.value) : null)"
                        >
                            <option value="">{{ __('(Ninguno)') }}</option>
                            @foreach ($this->tenants as $tenant)
                                <option value="{{ $tenant->id }}" @selected($user->current_tenant_id === $tenant->id)>
                                    {{ $tenant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-1 md:text-right">
                        <flux:button variant="danger" wire:click="delete({{ $user->id }})">
                            {{ __('X') }}
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

