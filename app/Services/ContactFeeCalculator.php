<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\SystemSetting;

/**
 * Motor de precio-oferta: calcula el fee por desbloqueo de contacto (valor + IVA)
 * según categoría, demanda (bids), ubicación y antigüedad.
 *
 * Fórmula: Fee_bruto = Base * (1 + Coef_categoria) * (1 + Coef_demanda)
 *                      * (1 + Coef_ubicacion) * (1 - Coef_antiguedad)
 * Fee_final = redondear(Fee_bruto); IVA = Fee_final * iva_rate; Total = Fee_final + IVA
 */
class ContactFeeCalculator
{
    /**
     * Calcula el fee para una solicitud de servicio.
     *
     * @return array{ fee_net: int, iva: int, total: int, breakdown: array<string, mixed> }
     */
    public static function calculate(ServiceRequest $serviceRequest): array
    {
        $serviceRequest->load(['category', 'region']);
        $serviceRequest->loadCount('bids');

        $baseFee = self::baseFee();
        $ivaRate = self::ivaRate();
        $coefCategory = self::coefficientCategory($serviceRequest);
        $coefDemand = self::coefficientDemand($serviceRequest);
        $coefLocation = self::coefficientLocation($serviceRequest);
        $coefAge = self::coefficientAge($serviceRequest);

        $feeBruto = $baseFee
            * (1 + $coefCategory)
            * (1 + $coefDemand)
            * (1 + $coefLocation)
            * (1 - $coefAge);

        $feeNet = (int) round(max(0, $feeBruto));
        $iva = (int) round($feeNet * $ivaRate);
        $total = $feeNet + $iva;

        return [
            'fee_net' => $feeNet,
            'iva' => $iva,
            'total' => $total,
            'breakdown' => [
                'base_fee' => $baseFee,
                'iva_rate' => $ivaRate,
                'coef_category' => $coefCategory,
                'coef_demand' => $coefDemand,
                'coef_location' => $coefLocation,
                'coef_age_discount' => $coefAge,
            ],
        ];
    }

    protected static function baseFee(): int
    {
        $fromSetting = SystemSetting::get('contact_fee_base');

        return $fromSetting !== null
            ? (int) $fromSetting
            : (int) config('contact_fee.base_fee', 2500);
    }

    protected static function ivaRate(): float
    {
        $fromSetting = SystemSetting::get('contact_fee_iva_rate');

        return $fromSetting !== null
            ? (float) $fromSetting
            : (float) config('contact_fee.iva_rate', 0.19);
    }

    protected static function coefficientCategory(ServiceRequest $serviceRequest): float
    {
        $max = (float) config('contact_fee.coefficients.category_max', 0.5);
        $multiplier = $serviceRequest->category?->fee_multiplier ?? 0;

        return min($max, max(0, (float) $multiplier));
    }

    protected static function coefficientDemand(ServiceRequest $serviceRequest): float
    {
        $max = (float) config('contact_fee.coefficients.demand_max', 0.5);
        $bidsForMax = (int) config('contact_fee.demand.bids_for_max_coef', 15);
        $bids = (int) $serviceRequest->bids_count;

        if ($bidsForMax <= 0) {
            return 0;
        }

        $ratio = min(1, $bids / $bidsForMax);

        return $ratio * $max;
    }

    protected static function coefficientLocation(ServiceRequest $serviceRequest): float
    {
        $max = (float) config('contact_fee.coefficients.location_max', 0.3);
        $premiumCodes = config('contact_fee.premium_region_codes', ['13']);
        $regionCode = $serviceRequest->region?->code;

        if (! $regionCode || ! in_array($regionCode, $premiumCodes, true)) {
            return 0;
        }

        return $max;
    }

    protected static function coefficientAge(ServiceRequest $serviceRequest): float
    {
        $max = (float) config('contact_fee.coefficients.age_discount_max', 0.2);
        $daysForMax = (int) config('contact_fee.age.days_for_max_discount', 60);

        $publishedAt = $serviceRequest->published_at;
        if (! $publishedAt) {
            return 0;
        }

        $days = (int) $publishedAt->diffInDays(now(), false);
        if ($days <= 0) {
            return 0;
        }

        if ($daysForMax <= 0) {
            return 0;
        }

        $ratio = min(1, $days / $daysForMax);

        return $ratio * $max;
    }
}
