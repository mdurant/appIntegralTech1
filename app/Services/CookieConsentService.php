<?php

namespace App\Services;

class CookieConsentService
{
    /**
     * Nombre de la cookie que indica que el consentimiento fue dado
     */
    public const CONSENT_COOKIE_NAME = 'cookie_consent_given';

    /**
     * Nombre de la cookie que almacena las preferencias de cookies
     */
    public const PREFERENCES_COOKIE_NAME = 'cookie_preferences';

    /**
     * Duración de las cookies de consentimiento (en días)
     */
    public const CONSENT_COOKIE_DURATION = 365;

    /**
     * Tipos de cookies disponibles
     */
    public const COOKIE_TYPE_MARKETING = 'marketing';
    public const COOKIE_TYPE_USER_EXPERIENCE = 'user_experience';
    public const COOKIE_TYPE_ESSENTIAL = 'essential'; // Siempre activas

    /**
     * Verifica si el usuario ya ha dado su consentimiento
     */
    public function hasConsent(): bool
    {
        return isset($_COOKIE[self::CONSENT_COOKIE_NAME]);
    }

    /**
     * Obtiene las preferencias de cookies del usuario
     *
     * @return array{marketing: bool, user_experience: bool}
     */
    public function getPreferences(): array
    {
        if (!isset($_COOKIE[self::PREFERENCES_COOKIE_NAME])) {
            return [
                'marketing' => false,
                'user_experience' => false,
            ];
        }

        $preferences = json_decode($_COOKIE[self::PREFERENCES_COOKIE_NAME], true);

        return [
            'marketing' => $preferences['marketing'] ?? false,
            'user_experience' => $preferences['user_experience'] ?? false,
        ];
    }

    /**
     * Verifica si un tipo específico de cookie está permitido
     */
    public function isAllowed(string $cookieType): bool
    {
        if ($cookieType === self::COOKIE_TYPE_ESSENTIAL) {
            return true; // Las cookies esenciales siempre están permitidas
        }

        $preferences = $this->getPreferences();

        return $preferences[$cookieType] ?? false;
    }

    /**
     * Crea cookies de marketing cuando están permitidas
     */
    public function setMarketingCookies(): void
    {
        if (!$this->isAllowed(self::COOKIE_TYPE_MARKETING)) {
            return;
        }

        $visitorId = $this->getOrCreateVisitorId();
        $sessionId = session()->getId();
        $lastVisit = now()->toIso8601String();

        // Cookie: marketing_visitor_id
        setcookie(
            'marketing_visitor_id',
            $visitorId,
            time() + (self::CONSENT_COOKIE_DURATION * 24 * 60 * 60),
            '/',
            null,
            true, // secure
            true  // httponly
        );

        // Cookie: marketing_session_id
        setcookie(
            'marketing_session_id',
            $sessionId,
            time() + (24 * 60 * 60), // 1 día
            '/',
            null,
            true,
            true
        );

        // Cookie: marketing_last_visit
        setcookie(
            'marketing_last_visit',
            $lastVisit,
            time() + (self::CONSENT_COOKIE_DURATION * 24 * 60 * 60),
            '/',
            null,
            true,
            true
        );

        // Cookie: marketing_preferences (genérica)
        $marketingPreferences = [
            'source' => request()->header('Referer') ?? 'direct',
            'user_agent' => substr(request()->userAgent() ?? '', 0, 100), // Limitado por seguridad
            'accepted_at' => now()->toIso8601String(),
        ];

        setcookie(
            'marketing_preferences',
            json_encode($marketingPreferences),
            time() + (self::CONSENT_COOKIE_DURATION * 24 * 60 * 60),
            '/',
            null,
            true,
            true
        );
    }

    /**
     * Crea cookies de experiencia de usuario cuando están permitidas
     */
    public function setUserExperienceCookies(): void
    {
        if (!$this->isAllowed(self::COOKIE_TYPE_USER_EXPERIENCE)) {
            return;
        }

        $language = app()->getLocale();
        $theme = 'light'; // Por defecto, puede expandirse

        // Cookie: ux_language
        setcookie(
            'ux_language',
            $language,
            time() + (self::CONSENT_COOKIE_DURATION * 24 * 60 * 60),
            '/',
            null,
            true,
            true
        );

        // Cookie: ux_theme
        setcookie(
            'ux_theme',
            $theme,
            time() + (self::CONSENT_COOKIE_DURATION * 24 * 60 * 60),
            '/',
            null,
            true,
            true
        );

        // Cookie: ux_preferences
        $uxPreferences = [
            'language' => $language,
            'theme' => $theme,
            'last_activity' => now()->toIso8601String(),
        ];

        setcookie(
            'ux_preferences',
            json_encode($uxPreferences),
            time() + (self::CONSENT_COOKIE_DURATION * 24 * 60 * 60),
            '/',
            null,
            true,
            true
        );

        // Cookie: ux_last_activity
        setcookie(
            'ux_last_activity',
            now()->toIso8601String(),
            time() + (30 * 24 * 60 * 60), // 30 días
            '/',
            null,
            true,
            true
        );
    }

    /**
     * Elimina todas las cookies de marketing
     */
    public function removeMarketingCookies(): void
    {
        $cookies = [
            'marketing_visitor_id',
            'marketing_session_id',
            'marketing_last_visit',
            'marketing_preferences',
        ];

        foreach ($cookies as $cookieName) {
            setcookie($cookieName, '', time() - 3600, '/');
            unset($_COOKIE[$cookieName]);
        }
    }

    /**
     * Elimina todas las cookies de experiencia de usuario
     */
    public function removeUserExperienceCookies(): void
    {
        $cookies = [
            'ux_language',
            'ux_theme',
            'ux_preferences',
            'ux_last_activity',
        ];

        foreach ($cookies as $cookieName) {
            setcookie($cookieName, '', time() - 3600, '/');
            unset($_COOKIE[$cookieName]);
        }
    }

    /**
     * Obtiene o crea un ID único para el visitante
     */
    private function getOrCreateVisitorId(): string
    {
        if (isset($_COOKIE['marketing_visitor_id'])) {
            return $_COOKIE['marketing_visitor_id'];
        }

        // Generar un ID único
        return uniqid('visitor_', true) . '_' . time();
    }
}
