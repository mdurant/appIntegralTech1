<?php

namespace App\Models;

use App\Helpers\ChileanDataHelper;
use App\WorkOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'budget_estimated',
        'final_price',
        'started_at',
        'completed_at',
        'paid_at',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => WorkOrderStatus::class,
            'budget_estimated' => 'decimal:2',
            'final_price' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'paid_at' => 'datetime',
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

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Formatea el budget_estimated en formato chileno
     */
    public function getFormattedBudgetEstimatedAttribute(): string
    {
        if (! $this->budget_estimated) {
            return '—';
        }

        return ChileanDataHelper::formatChileanCurrency($this->budget_estimated);
    }

    /**
     * Formatea el final_price en formato chileno
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        if (! $this->final_price) {
            return '—';
        }

        return ChileanDataHelper::formatChileanCurrency($this->final_price);
    }

    /**
     * Calcula la diferencia entre presupuesto estimado y precio final
     */
    public function getPriceDifferenceAttribute(): ?float
    {
        if (! $this->budget_estimated || ! $this->final_price) {
            return null;
        }

        return $this->final_price - $this->budget_estimated;
    }
}
