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
            ['key' => 'fletes', 'name' => 'Fletes y Transporte', 'sort_order' => 10],
            ['key' => 'mudanzas', 'name' => 'Mudanzas', 'sort_order' => 20],
            ['key' => 'logistica', 'name' => 'Logística', 'sort_order' => 30],
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
            ['parent' => 'fletes', 'key' => 'fletes-urbanos', 'name' => 'Fletes Urbanos', 'sort_order' => 10],
            ['parent' => 'fletes', 'key' => 'fletes-interurbanos', 'name' => 'Fletes Interurbanos', 'sort_order' => 20],
            ['parent' => 'fletes', 'key' => 'fletes-carga-pesada', 'name' => 'Carga Pesada', 'sort_order' => 30],
            ['parent' => 'fletes', 'key' => 'fletes-urgentes', 'name' => 'Fletes Urgentes', 'sort_order' => 40],
            ['parent' => 'mudanzas', 'key' => 'mudanzas-residenciales', 'name' => 'Mudanzas Residenciales', 'sort_order' => 10],
            ['parent' => 'mudanzas', 'key' => 'mudanzas-comerciales', 'name' => 'Mudanzas Comerciales', 'sort_order' => 20],
            ['parent' => 'logistica', 'key' => 'logistica-distribucion', 'name' => 'Distribución', 'sort_order' => 10],
            ['parent' => 'logistica', 'key' => 'logistica-almacenamiento', 'name' => 'Almacenamiento', 'sort_order' => 20],
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
