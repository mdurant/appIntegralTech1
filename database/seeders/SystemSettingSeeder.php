<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'quote_validity_days',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Días de vigencia de las cotizaciones (presupuestos)',
            ],
            [
                'key' => 'service_request_expiry_days',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Días de expiración de las solicitudes de servicio publicadas',
            ],
            [
                'key' => 'enable_ratings',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Habilitar sistema de valoraciones',
            ],
            [
                'key' => 'min_rating',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Valoración mínima permitida (estrellas)',
            ],
            [
                'key' => 'max_rating',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Valoración máxima permitida (estrellas)',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
