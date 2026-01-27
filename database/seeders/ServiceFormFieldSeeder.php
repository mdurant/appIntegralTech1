<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceFormFieldOption;
use App\ServiceFormFieldType;
use Illuminate\Database\Seeder;

class ServiceFormFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategory = ServiceCategory::query()->where('key', 'fletes-urbanos')->first();

        if (! $subcategory) {
            return;
        }

        $fields = [
            [
                'key' => 'tipo_carga',
                'label' => '¿Qué tipo de carga necesitas transportar?',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 10,
                'options' => [
                    ['value' => 'muebles', 'label' => 'Muebles'],
                    ['value' => 'electrodomesticos', 'label' => 'Electrodomésticos'],
                    ['value' => 'cajas', 'label' => 'Cajas y pertenencias'],
                    ['value' => 'mercaderia', 'label' => 'Mercadería comercial'],
                    ['value' => 'materiales', 'label' => 'Materiales de construcción'],
                    ['value' => 'vehiculo', 'label' => 'Vehículo o moto'],
                    ['value' => 'otro', 'label' => 'Otro'],
                ],
            ],
            [
                'key' => 'volumen_aproximado',
                'label' => 'Volumen aproximado de carga',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 20,
                'options' => [
                    ['value' => 'pequeno', 'label' => 'Pequeño (hasta 5 m³)'],
                    ['value' => 'mediano', 'label' => 'Mediano (5-15 m³)'],
                    ['value' => 'grande', 'label' => 'Grande (15-30 m³)'],
                    ['value' => 'muy_grande', 'label' => 'Muy grande (más de 30 m³)'],
                ],
            ],
            [
                'key' => 'distancia',
                'label' => 'Distancia del traslado',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 30,
                'options' => [
                    ['value' => 'local', 'label' => 'Local (misma comuna)'],
                    ['value' => 'regional', 'label' => 'Regional (misma región)'],
                    ['value' => 'interregional', 'label' => 'Interregional (otra región)'],
                ],
            ],
            [
                'key' => 'plazo_realizacion',
                'label' => 'Plazo para realizar el servicio',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 40,
                'options' => [
                    ['value' => 'urgente', 'label' => 'Urgente (hoy o mañana)'],
                    ['value' => 'esta_semana', 'label' => 'Esta semana'],
                    ['value' => 'proxima_semana', 'label' => 'Próxima semana'],
                    ['value' => 'este_mes', 'label' => 'Este mes'],
                    ['value' => 'flexible', 'label' => 'Flexible'],
                ],
            ],
            [
                'key' => 'requiere_embalaje',
                'label' => '¿Requiere servicio de embalaje?',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 50,
                'options' => [
                    ['value' => 'si', 'label' => 'Sí, necesito embalaje'],
                    ['value' => 'no', 'label' => 'No, ya está embalado'],
                    ['value' => 'parcial', 'label' => 'Parcial, algunos artículos'],
                ],
            ],
            [
                'key' => 'direccion_origen',
                'label' => 'Dirección de origen',
                'type' => ServiceFormFieldType::Textarea,
                'required' => true,
                'sort_order' => 60,
            ],
            [
                'key' => 'direccion_destino',
                'label' => 'Dirección de destino',
                'type' => ServiceFormFieldType::Textarea,
                'required' => true,
                'sort_order' => 70,
            ],
        ];

        foreach ($fields as $fieldData) {
            $field = ServiceFormField::query()->updateOrCreate(
                [
                    'service_category_id' => $subcategory->id,
                    'key' => $fieldData['key'],
                ],
                [
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type']->value,
                    'required' => $fieldData['required'],
                    'sort_order' => $fieldData['sort_order'],
                ],
            );

            if (! isset($fieldData['options'])) {
                continue;
            }

            foreach ($fieldData['options'] as $idx => $opt) {
                ServiceFormFieldOption::query()->updateOrCreate(
                    [
                        'service_form_field_id' => $field->id,
                        'value' => $opt['value'],
                    ],
                    [
                        'label' => $opt['label'],
                        'sort_order' => $idx * 10,
                    ],
                );
            }
        }
    }
}
