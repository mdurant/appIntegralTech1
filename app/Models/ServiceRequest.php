<?php

namespace App\Models;

use App\Helpers\DataObfuscationHelper;
use App\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'region_id',
        'commune_id',
        'notes',
        'status',
        'published_at',
        'expires_at',
        'awarded_bid_id',
        'pdf_path',
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

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Obtiene el nombre de contacto ofuscado (solo primer nombre)
     */
    protected function obfuscatedContactName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contact_name
                ? DataObfuscationHelper::obfuscateName($this->contact_name)
                : '—',
        );
    }

    /**
     * Obtiene el teléfono ofuscado (3 primeros dígitos + XXX XXX)
     */
    protected function obfuscatedPhone(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contact_phone
                ? DataObfuscationHelper::obfuscatePhone($this->contact_phone)
                : 'XXX XXX XXX',
        );
    }

    /**
     * Obtiene el email ofuscado (3 primeros caracteres + xxxxx@xxxxxx.xxx)
     */
    protected function obfuscatedEmail(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contact_email
                ? DataObfuscationHelper::obfuscateEmail($this->contact_email)
                : 'xxx@xxxxxx.xxx',
        );
    }

    /**
     * Indica si la solicitud publicada está vencida (expires_at ya pasó).
     */
    public function isExpired(): bool
    {
        return $this->status === ServiceRequestStatus::Published
            && $this->expires_at
            && $this->expires_at->isPast();
    }

    /**
     * Obtiene la localización para mostrar (Región + Comuna, sin dirección)
     */
    protected function locationDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = [];

                if ($this->commune) {
                    $parts[] = $this->commune->name;
                }

                if ($this->region) {
                    $parts[] = $this->region->name;
                }

                if (empty($parts) && $this->location_text) {
                    return $this->location_text;
                }

                return ! empty($parts) ? implode(' - ', $parts) : '—';
            },
        );
    }
}
