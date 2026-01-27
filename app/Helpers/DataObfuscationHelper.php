<?php

namespace App\Helpers;

class DataObfuscationHelper
{
    /**
     * Ofusca el nombre completo, retornando solo el primer nombre
     * Ejemplo: "Juan Pérez González" -> "Juan"
     */
    public static function obfuscateName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));

        return $parts[0] ?? '';
    }

    /**
     * Ofusca el teléfono, mostrando solo los 3 primeros dígitos
     * Ejemplo: "+56912345678" -> "569 XXX XXX"
     * Formato: 3 dígitos + " XXX XXX"
     */
    public static function obfuscatePhone(string $phone): string
    {
        // Limpiar el teléfono (eliminar espacios, guiones, paréntesis)
        $cleaned = preg_replace('/[\s\-\(\)]/', '', $phone);

        // Extraer los primeros 3 dígitos
        $firstThree = substr($cleaned, 0, 3);

        // Si no hay suficientes dígitos, retornar el formato ofuscado completo
        if (strlen($firstThree) < 3) {
            return 'XXX XXX XXX';
        }

        return $firstThree.' XXX XXX';
    }

    /**
     * Ofusca el email, mostrando solo los 3 primeros caracteres
     * Ejemplo: "juan.perez@gmail.com" -> "jua xxxxx@xxxxxx.xxx"
     * Formato: 3 caracteres + "xxxxx@xxxxxx.xxx"
     */
    public static function obfuscateEmail(string $email): string
    {
        // Separar local part y domain
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return 'xxx@xxxxxx.xxx';
        }

        [$localPart, $domain] = $parts;

        // Obtener los primeros 3 caracteres del local part
        $firstThree = substr($localPart, 0, 3);

        // Si no hay suficientes caracteres, usar lo que haya
        if (strlen($firstThree) < 3) {
            $firstThree = str_pad($firstThree, 3, 'x');
        }

        return $firstThree.'xxxxx@xxxxxx.xxx';
    }
}
