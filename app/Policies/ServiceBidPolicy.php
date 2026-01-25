<?php

namespace App\Policies;

use App\Models\ServiceBid;
use App\Models\User;
use App\ServiceBidStatus;
use App\ServiceRequestStatus;

class ServiceBidPolicy
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
    public function view(User $user, ServiceBid $serviceBid): bool
    {
        if ($serviceBid->user_id === $user->id) {
            return true;
        }

        $request = $serviceBid->serviceRequest;

        if ($user->isClient() && $user->belongsToTenant($request->tenant)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ! $user->isGuest() && ! $user->isClient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceBid $serviceBid): bool
    {
        if ($serviceBid->user_id !== $user->id) {
            return false;
        }

        return $serviceBid->status === ServiceBidStatus::Submitted;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceBid $serviceBid): bool
    {
        return $this->update($user, $serviceBid);
    }

    public function accept(User $user, ServiceBid $serviceBid): bool
    {
        $request = $serviceBid->serviceRequest;

        if ($request->status !== ServiceRequestStatus::Published) {
            return false;
        }

        if ($serviceBid->valid_until && $serviceBid->valid_until->isPast()) {
            return false;
        }

        return $user->isClient() && $user->belongsToTenant($request->tenant);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceBid $serviceBid): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceBid $serviceBid): bool
    {
        return false;
    }
}
