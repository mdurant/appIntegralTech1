<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\SystemRole;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Users extends Component
{
    #[Computed]
    public function users(): Collection
    {
        return User::query()->with('currentTenant')->latest('id')->get();
    }

    #[Computed]
    public function tenants(): Collection
    {
        return Tenant::query()->orderBy('name')->get();
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    public function updateRole(int $userId, string $role): void
    {
        $roleEnum = SystemRole::tryFrom($role);
        abort_unless($roleEnum !== null, 422);

        User::query()->findOrFail($userId)->update([
            'system_role' => $roleEnum,
        ]);
    }

    public function updateTenant(int $userId, ?int $tenantId): void
    {
        $user = User::query()->findOrFail($userId);

        if ($tenantId === null) {
            $user->update(['current_tenant_id' => null]);

            return;
        }

        Tenant::query()->findOrFail($tenantId);

        $user->update(['current_tenant_id' => $tenantId]);
    }

    public function delete(int $userId): void
    {
        User::query()->findOrFail($userId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.users', [
            'roles' => SystemRole::cases(),
        ])->layout('layouts.app', ['title' => __('Admin · Usuarios')]);
    }
}

