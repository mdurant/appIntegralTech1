<?php

namespace App\Models;

use App\Helpers\ChileanDataHelper;
use App\ServiceBidStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBid extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceBidFactory> */
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('nonExpired', function (Builder $builder) {
            $builder->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now())
                    ->orWhere('status', '!=', ServiceBidStatus::Expired->value);
            });
        });
    }

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

    /**
     * Formatea el amount en formato chileno (sin decimales, separador de miles con punto)
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => ChileanDataHelper::formatChileanCurrency($this->amount),
        );
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

    /**
     * Incluir cotizaciones vencidas en la query
     */
    public function scopeWithExpired(Builder $query): Builder
    {
        return $query->withoutGlobalScope('nonExpired');
    }
}
