<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ! $user->isGuest();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        if ($user->isClient() && $user->belongsToTenant($workOrder->tenant)) {
            return true;
        }

        return $workOrder->awarded_to_user_id === $user->id;
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
    public function update(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }
}
