<?php

namespace App;

enum SubscriptionTier: string
{
    case Independent = 'independent';
    case Pyme = 'pyme';
    case Enterprise = 'enterprise';

    public function label(): string
    {
        return match ($this) {
            self::Independent => __('Freemium (solo web)'),
            self::Pyme => __('Pro'),
            self::Enterprise => __('Enterprise'),
        };
    }

    public function apiRateLimit(): int
    {
        return match ($this) {
            self::Independent => 0,
            self::Pyme => 60,
            self::Enterprise => 500,
        };
    }

    public function hasApiAccess(): bool
    {
        return $this !== self::Independent;
    }
}
