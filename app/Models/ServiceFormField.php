<?php

namespace App\Models;

use App\ServiceFormFieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceFormField extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFormFieldFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'service_category_id',
        'key',
        'label',
        'type',
        'required',
        'sort_order',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'type' => ServiceFormFieldType::class,
            'required' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ServiceFormFieldOption::class)->orderBy('sort_order')->orderBy('label');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ServiceRequestFieldAnswer::class);
    }
}
