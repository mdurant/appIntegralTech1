<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFormFieldOption extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFormFieldOptionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'service_form_field_id',
        'value',
        'label',
        'sort_order',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServiceFormField::class, 'service_form_field_id');
    }
}
