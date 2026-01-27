<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\User;
use App\Models\WorkOrder;

class RatingService
{
    public function create(
        WorkOrder $workOrder,
        User $user,
        int $rating,
        ?string $comment = null,
    ): Rating {
        return Rating::create([
            'work_order_id' => $workOrder->id,
            'user_id' => $user->id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }
}
