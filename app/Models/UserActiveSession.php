<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActiveSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_name',
        'device_type',
        'browser_name',
        'browser_version',
        'operating_system',
        'os_version',
        'location',
        'is_current',
        'is_terminated',
        'last_activity',
        'terminated_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_terminated' => 'boolean',
        'last_activity' => 'datetime',
        'terminated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener solo sesiones activas (no terminadas)
     */
    public function scopeActive($query)
    {
        return $query->where('is_terminated', false);
    }

    /**
     * Scope para obtener solo sesiones terminadas
     */
    public function scopeTerminated($query)
    {
        return $query->where('is_terminated', true);
    }

    /**
     * Marcar sesión como terminada (soft delete visual)
     */
    public function terminate(): void
    {
        $this->update([
            'is_terminated' => true,
            'terminated_at' => now(),
        ]);
    }
}
