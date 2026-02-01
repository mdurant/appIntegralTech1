<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    public const StatusPending = 'pending';

    public const StatusApproved = 'approved';

    public const StatusRejected = 'rejected';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'payment_simulation_id',
        'status',
        'resolved_by_user_id',
        'user_comment',
        'admin_comment',
        'resolved_at',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentSimulation(): BelongsTo
    {
        return $this->belongsTo(PaymentSimulation::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::StatusPending;
    }

    public function isApproved(): bool
    {
        return $this->status === self::StatusApproved;
    }

    public function isRejected(): bool
    {
        return $this->status === self::StatusRejected;
    }
}
