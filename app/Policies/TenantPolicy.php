<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use App\TenantRole;

class TenantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->isGuest()) {
            return false;
        }

        return $user->tenants()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tenant $tenant): bool
    {
        if ($user->isGuest()) {
            return false;
        }

        return $user->belongsToTenant($tenant);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tenant $tenant): bool
    {
        return $user->tenants()
            ->whereKey($tenant->getKey())
            ->wherePivotIn('role', [TenantRole::Owner->value, TenantRole::Admin->value])
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tenant $tenant): bool
    {
        return $this->update($user, $tenant);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tenant $tenant): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tenant $tenant): bool
    {
        return false;
    }
}
