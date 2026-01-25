<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $top = [
            ['key' => 'construccion', 'name' => 'Construcción', 'sort_order' => 10],
            ['key' => 'remodelacion', 'name' => 'Remodelación', 'sort_order' => 20],
            ['key' => 'mantencion', 'name' => 'Mantención', 'sort_order' => 30],
            ['key' => 'maestros', 'name' => 'Maestros', 'sort_order' => 40],
            ['key' => 'mudanzas', 'name' => 'Mudanzas', 'sort_order' => 50],
        ];

        $topIds = [];

        foreach ($top as $item) {
            $cat = ServiceCategory::query()->updateOrCreate(
                ['key' => $item['key']],
                [
                    'parent_id' => null,
                    'name' => $item['name'],
                    'sort_order' => $item['sort_order'],
                ],
            );

            $topIds[$item['key']] = $cat->id;
        }

        $sub = [
            ['parent' => 'construccion', 'key' => 'construccion-casa', 'name' => 'Construcción Casa', 'sort_order' => 10],
            ['parent' => 'construccion', 'key' => 'ampliar-casa', 'name' => 'Ampliar Casa', 'sort_order' => 20],
            ['parent' => 'remodelacion', 'key' => 'remodelacion-cocina', 'name' => 'Remodelación Cocina', 'sort_order' => 10],
            ['parent' => 'mantencion', 'key' => 'mantencion-aire-acondicionado', 'name' => 'Aire Acondicionado', 'sort_order' => 10],
            ['parent' => 'maestros', 'key' => 'maestros-albaniles', 'name' => 'Albañiles', 'sort_order' => 10],
            ['parent' => 'mudanzas', 'key' => 'mudanzas-fletes', 'name' => 'Fletes', 'sort_order' => 10],
        ];

        foreach ($sub as $item) {
            ServiceCategory::query()->updateOrCreate(
                ['key' => $item['key']],
                [
                    'parent_id' => $topIds[$item['parent']] ?? null,
                    'name' => $item['name'],
                    'sort_order' => $item['sort_order'],
                ],
            );
        }
    }
}
