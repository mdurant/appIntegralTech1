<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\SystemRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'system_role',
        'current_tenant_id',
        'avatar_path',
        'rut',
        'giro_sii',
        'gender',
        'birth_date',
        'fantasy_name',
        'economic_activity',
        'region_id',
        'commune_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'system_role' => SystemRole::class,
            'birth_date' => 'date',
        ];
    }

    public function currentTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function serviceCategories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'user_service_categories')
            ->withTimestamps()
            ->orderBy('service_categories.sort_order')
            ->orderBy('service_categories.name');
    }

    public function isAdministrator(): bool
    {
        return $this->system_role === SystemRole::Admin;
    }

    public function isGuest(): bool
    {
        return $this->system_role === SystemRole::Guest;
    }

    public function isClient(): bool
    {
        return $this->system_role === SystemRole::Client;
    }

    public function belongsToTenant(Tenant $tenant): bool
    {
        return $this->tenants()->whereKey($tenant->getKey())->exists();
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        // Verificar que el archivo existe
        if (! Storage::disk('public')->exists($this->avatar_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    /**
     * Get the full name (first_name + last_name) or fallback to name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return trim("{$this->first_name} {$this->last_name}");
        }

        return $this->name;
    }

    /**
     * Get display name for banner (fantasy_name for providers, full name for others)
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->system_role === SystemRole::User && $this->fantasy_name) {
            return $this->fantasy_name;
        }

        return $this->full_name;
    }

    /**
     * Check if user is a provider (User role)
     */
    public function isProvider(): bool
    {
        return $this->system_role === SystemRole::User;
    }

    /**
     * Check if user should show fantasy name in banner
     */
    public function shouldShowFantasyName(): bool
    {
        return $this->isProvider() && ! empty($this->fantasy_name);
    }
}
