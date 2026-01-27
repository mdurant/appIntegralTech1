<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use App\Models\WorkOrder;
use App\WorkOrderStatus;

class RatingPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, WorkOrder $workOrder): bool
    {
        // Solo clientes del tenant pueden valorar
        if (! $user->isClient()) {
            return false;
        }

        if (! $user->belongsToTenant($workOrder->tenant)) {
            return false;
        }

        // Solo OTs completadas pueden ser valoradas
        if ($workOrder->status !== WorkOrderStatus::Completed) {
            return false;
        }

        // No puede valorar si ya valoró
        return ! Rating::where('work_order_id', $workOrder->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Rating $rating): bool
    {
        // El usuario que creó la valoración puede verla
        if ($rating->user_id === $user->id) {
            return true;
        }

        // El profesional de la OT puede ver su valoración
        if ($rating->workOrder->awarded_to_user_id === $user->id) {
            return true;
        }

        return false;
    }
}
