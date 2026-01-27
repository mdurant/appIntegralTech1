<?php

namespace Database\Seeders;

use App\Helpers\ChileanDataHelper;
use App\Models\Commune;
use App\Models\Region;
use Illuminate\Database\Seeder;
use ReflectionClass;

class CommuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reflection = new ReflectionClass(ChileanDataHelper::class);
        $property = $reflection->getProperty('regiones');
        $property->setAccessible(true);
        $regiones = $property->getValue(null);

        foreach ($regiones as $regionData) {
            $region = Region::where('name', $regionData['region'])->first();

            if (! $region) {
                continue;
            }

            $sortOrder = 1;
            foreach ($regionData['comunas'] as $comunaName) {
                Commune::updateOrCreate(
                    [
                        'region_id' => $region->id,
                        'name' => $comunaName,
                    ],
                    [
                        'region_id' => $region->id,
                        'name' => $comunaName,
                        'code' => $this->generateCode($comunaName),
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function generateCode(string $name): string
    {
        // Generar código simple basado en el nombre
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 5));
        $counter = 1;
        $originalCode = $code;

        while (Commune::where('code', $code)->exists()) {
            $code = $originalCode.$counter;
            $counter++;
        }

        return $code;
    }
}
