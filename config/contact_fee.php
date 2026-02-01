<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fee base y IVA (Motor de precio-oferta)
    |--------------------------------------------------------------------------
    | Valor mínimo por desbloqueo de contacto. Puede sobreescribirse desde
    | system_settings (contact_fee_base, contact_fee_iva_rate).
    */

    'base_fee' => (int) env('CONTACT_FEE_BASE', 2500),

    'iva_rate' => (float) env('CONTACT_FEE_IVA_RATE', 0.19),

    /*
    |--------------------------------------------------------------------------
    | Límites de coeficientes (entre 0 y el valor indicado)
    |--------------------------------------------------------------------------
    | Evitan que el precio se dispare. Fórmula:
    | Fee_bruto = Base * (1 + Coef_categoria) * (1 + Coef_demanda)
    |             * (1 + Coef_ubicacion) * (1 - Coef_antiguedad)
    */

    'coefficients' => [
        'category_max' => 0.5,   // Por fee_multiplier en service_categories
        'demand_max' => 0.5,     // Por cantidad de cotizaciones (bids) en la solicitud
        'location_max' => 0.3,   // Por región premium (códigos en premium_region_codes)
        'age_discount_max' => 0.2, // Descuento por antigüedad de la publicación
    ],

    /*
    |--------------------------------------------------------------------------
    | Regiones con coeficiente de ubicación (código región Chile)
    |--------------------------------------------------------------------------
    | Región Metropolitana (13) u otras consideradas "premium" suman location_max.
    */

    'premium_region_codes' => ['13', 'RM'],

    /*
    |--------------------------------------------------------------------------
    | Escalas para demanda y antigüedad
    |--------------------------------------------------------------------------
    */

    'demand' => [
        'bids_for_max_coef' => 15,  // Con 15+ cotizaciones se aplica demand_max
    ],

    'age' => [
        'days_for_max_discount' => 60, // Con 60+ días publicada se aplica age_discount_max
    ],

];
