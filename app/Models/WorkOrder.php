<?php

namespace App\Models;

use App\WorkOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    /** @use HasFactory<\Database\Factories\WorkOrderFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'service_request_id',
        'service_bid_id',
        'tenant_id',
        'awarded_to_user_id',
        'status',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => WorkOrderStatus::class,
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function serviceBid(): BelongsTo
    {
        return $this->belongsTo(ServiceBid::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function awardedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_to_user_id');
    }
}
