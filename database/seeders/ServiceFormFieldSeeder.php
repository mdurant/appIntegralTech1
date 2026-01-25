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
        $subcategory = ServiceCategory::query()->where('key', 'construccion-casa')->first();

        if (! $subcategory) {
            return;
        }

        $fields = [
            [
                'key' => 'plazo_realizacion',
                'label' => 'Plazo de realización',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 10,
                'options' => [
                    ['value' => '1_semana', 'label' => '1 semana'],
                    ['value' => '3_semanas', 'label' => '3 semanas'],
                    ['value' => '1_mes', 'label' => '1 mes'],
                    ['value' => 'mas_de_3_meses', 'label' => 'Más de 3 meses'],
                    ['value' => '1_ano', 'label' => '1 año'],
                ],
            ],
            [
                'key' => 'superficie_aproximada',
                'label' => '¿Superficie aproximada a ampliar?',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 20,
                'options' => [
                    ['value' => 'menos_20', 'label' => 'Menos de 20 m²'],
                    ['value' => 'mas_21', 'label' => 'Más de 21 m²'],
                    ['value' => 'mas_50', 'label' => 'Más de 50 m²'],
                    ['value' => 'mas_100', 'label' => 'Más de 100 m²'],
                ],
            ],
            [
                'key' => 'tipo_construccion',
                'label' => '¿Qué tipo de construcción necesitas?',
                'type' => ServiceFormFieldType::Text,
                'required' => true,
                'sort_order' => 30,
            ],
            [
                'key' => 'necesita_planos',
                'label' => '¿Necesitas además los planos?',
                'type' => ServiceFormFieldType::Select,
                'required' => true,
                'sort_order' => 40,
                'options' => [
                    ['value' => 'si', 'label' => 'Necesito planos'],
                    ['value' => 'no', 'label' => 'No necesito planos'],
                ],
            ],
            [
                'key' => 'localizacion',
                'label' => 'Localización del trabajo',
                'type' => ServiceFormFieldType::Text,
                'required' => true,
                'sort_order' => 50,
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
