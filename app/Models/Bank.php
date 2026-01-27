<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'cuenta',
        'tipo',
        'producto',
        'activo',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }
}
