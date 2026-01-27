<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ChileanRut implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->isValidRut($value)) {
            $fail(__('El RUT ingresado no es válido.'));
        }
    }

    /**
     * Valida un RUT chileno
     * Formato aceptado: 12345678-9 o 123456789
     */
    private function isValidRut(?string $rut): bool
    {
        if (empty($rut)) {
            return true; // Nullable
        }

        // Limpiar el RUT (eliminar puntos y guiones)
        $rut = str_replace(['.', '-'], '', $rut);
        $rut = strtoupper($rut);

        // Debe tener entre 8 y 9 caracteres (sin contar el dígito verificador)
        if (strlen($rut) < 8 || strlen($rut) > 9) {
            return false;
        }

        // Separar número y dígito verificador
        $numero = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        // El número debe ser solo dígitos
        if (! ctype_digit($numero)) {
            return false;
        }

        // El dígito verificador puede ser un dígito o 'K'
        if (! ctype_digit($dv) && $dv !== 'K') {
            return false;
        }

        // Calcular dígito verificador
        $suma = 0;
        $multiplier = 2;

        // Recorrer el número de derecha a izquierda
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += (int) $numero[$i] * $multiplier;
            $multiplier = $multiplier === 7 ? 2 : $multiplier + 1;
        }

        $resto = $suma % 11;
        $dvCalculado = 11 - $resto;

        if ($dvCalculado === 11) {
            $dvCalculado = '0';
        } elseif ($dvCalculado === 10) {
            $dvCalculado = 'K';
        } else {
            $dvCalculado = (string) $dvCalculado;
        }

        return $dvCalculado === $dv;
    }
}
