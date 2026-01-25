<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequestFieldAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceRequestFieldAnswerFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'service_request_id',
        'service_form_field_id',
        'value',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServiceFormField::class, 'service_form_field_id');
    }
}
