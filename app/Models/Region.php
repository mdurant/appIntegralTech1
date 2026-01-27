<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class)->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function allCommunes(): HasMany
    {
        return $this->hasMany(Commune::class)->orderBy('sort_order')->orderBy('name');
    }
}
