<?php

namespace App\Models;

use App\ServiceBidStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBid extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceBidFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'service_request_id',
        'user_id',
        'amount',
        'currency',
        'message',
        'status',
        'valid_until',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => ServiceBidStatus::class,
            'valid_until' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        if (! $this->valid_until) {
            return false;
        }

        return $this->valid_until->isPast();
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
