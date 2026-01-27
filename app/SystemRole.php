<?php

namespace App;

enum SystemRole: string
{
    case Admin = 'admin';
    case User = 'user';
    case Guest = 'guest';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Administrador'),
            self::User => __('Usuario'),
            self::Guest => __('Invitado'),
            self::Client => __('Cliente'),
        };
    }
}
