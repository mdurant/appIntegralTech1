<?php

namespace App\Models;

use App\SubscriptionTier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_tier',
        'starts_at',
        'ends_at',
        'amount',
        'currency',
        'payment_method',
        'external_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'amount' => 'decimal:2',
            'subscription_tier' => SubscriptionTier::class,
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
