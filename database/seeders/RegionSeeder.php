<?php

namespace Database\Seeders;

use App\Helpers\ChileanDataHelper;
use App\Models\Region;
use Illuminate\Database\Seeder;
use ReflectionClass;

class RegionSeeder extends Seeder
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

        $sortOrder = 1;
        foreach ($regiones as $regionData) {
            Region::updateOrCreate(
                ['name' => $regionData['region']],
                [
                    'name' => $regionData['region'],
                    'code' => $this->generateCode($regionData['region']),
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]
            );
        }
    }

    private function generateCode(string $name): string
    {
        // Generar código simple basado en el nombre
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 5));
        $counter = 1;
        $originalCode = $code;

        while (Region::where('code', $code)->exists()) {
            $code = $originalCode.$counter;
            $counter++;
        }

        return $code;
    }
}
