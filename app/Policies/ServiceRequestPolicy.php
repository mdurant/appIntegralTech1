<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;
use App\ServiceRequestStatus;

class ServiceRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Si Spatie Permissions está disponible, usar permisos
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo('view-published-requests') || $user->hasPermissionTo('view-own-requests');
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($serviceRequest->status === ServiceRequestStatus::Published) {
            return true;
        }

        if ($user->isClient() && $user->belongsToTenant($serviceRequest->tenant)) {
            return true;
        }

        return $serviceRequest->created_by_user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Si Spatie Permissions está disponible, usar permisos
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo('create-requests');
        }

        return $user->isClient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        // Si Spatie Permissions está disponible, usar permisos
        if (method_exists($user, 'hasPermissionTo')) {
            if (! $user->hasPermissionTo('manage-own-requests')) {
                return false;
            }
        } else {
            if (! $user->isClient()) {
                return false;
            }
        }

        if (! $user->belongsToTenant($serviceRequest->tenant)) {
            return false;
        }

        return $serviceRequest->status === ServiceRequestStatus::Draft;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $this->update($user, $serviceRequest);
    }

    public function publish(User $user, ServiceRequest $serviceRequest): bool
    {
        return $this->update($user, $serviceRequest);
    }

    public function reopen(User $user, ServiceRequest $serviceRequest): bool
    {
        if (! $user->isClient()) {
            return false;
        }

        if (! $user->belongsToTenant($serviceRequest->tenant)) {
            return false;
        }

        if ($serviceRequest->status->value !== 'published') {
            return false;
        }

        return $serviceRequest->expires_at !== null && $serviceRequest->expires_at->isPast();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        return false;
    }
}
