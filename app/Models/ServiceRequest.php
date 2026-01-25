<?php

namespace App\Models;

use App\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceRequestFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'category_id',
        'created_by_user_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'title',
        'description',
        'location_text',
        'address',
        'notes',
        'status',
        'published_at',
        'expires_at',
        'awarded_bid_id',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => ServiceRequestStatus::class,
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(ServiceBid::class);
    }

    public function awardedBid(): BelongsTo
    {
        return $this->belongsTo(ServiceBid::class, 'awarded_bid_id');
    }

    public function fieldAnswers(): HasMany
    {
        return $this->hasMany(ServiceRequestFieldAnswer::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ServiceRequestAttachment::class)->orderBy('sort_order');
    }
}
