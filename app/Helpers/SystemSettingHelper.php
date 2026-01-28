<?php

namespace App\Helpers;

use App\Models\SystemSetting;

class SystemSettingHelper
{
    /**
     * Obtiene un setting del sistema
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return SystemSetting::get($key, $default);
    }

    /**
     * Establece un setting del sistema
     */
    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): SystemSetting
    {
        return SystemSetting::set($key, $value, $type, $description);
    }

    /**
     * Obtiene los días de vigencia de cotizaciones
     */
    public static function quoteValidityDays(): int
    {
        return (int) self::get('quote_validity_days', 15);
    }

    /**
     * Obtiene los días de expiración de solicitudes
     */
    public static function serviceRequestExpiryDays(): int
    {
        return (int) self::get('service_request_expiry_days', 15);
    }

    /**
     * Verifica si el sistema de valoraciones está habilitado
     */
    public static function ratingsEnabled(): bool
    {
        return (bool) self::get('enable_ratings', true);
    }

    /**
     * Obtiene la valoración mínima permitida
     */
    public static function minRating(): int
    {
        return (int) self::get('min_rating', 1);
    }

    /**
     * Obtiene la valoración máxima permitida
     */
    public static function maxRating(): int
    {
        return (int) self::get('max_rating', 5);
    }
}
