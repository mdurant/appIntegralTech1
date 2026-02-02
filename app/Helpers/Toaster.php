<?php

namespace App\Helpers;

class Toaster
{
    public const TypeSuccess = 'success';

    public const TypeError = 'error';

    public const TypeWarning = 'warning';

    public const TypeInfo = 'info';

    /**
     * Flash a toast notification to show on next request (e.g. after redirect).
     */
    public static function flash(string $message, string $type = self::TypeSuccess): void
    {
        session()->flash('toast', [
            'message' => $message,
            'type' => $type,
        ]);
    }

    public static function success(string $message): void
    {
        self::flash($message, self::TypeSuccess);
    }

    public static function error(string $message): void
    {
        self::flash($message, self::TypeError);
    }

    public static function warning(string $message): void
    {
        self::flash($message, self::TypeWarning);
    }

    public static function info(string $message): void
    {
        self::flash($message, self::TypeInfo);
    }
}
