<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('Actividades-Economicas.txt');

        if (! file_exists($filePath)) {
            $this->command->warn("Archivo no encontrado: {$filePath}");
            $this->seedDefaultCategories();

            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->command->warn('El archivo está vacío');
            $this->seedDefaultCategories();

            return;
        }

        // Ignorar la primera línea (título)
        array_shift($lines);

        $categories = [];
        $currentCategory = null;
        $referenceCodeCounter = 1;

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Detectar si es una categoría principal
            // Las categorías principales suelen ser líneas que:
            // 1. Empiezan con "Servicios Profesionales de" o "Actividad"
            // 2. O son líneas que no tienen una categoría padre activa
            $isMainCategory = $this->isMainCategory($line, $currentCategory);

            if ($isMainCategory) {
                // Guardar la categoría anterior si existe
                if ($currentCategory !== null) {
                    $categories[] = $currentCategory;
                }

                // Crear nueva categoría principal
                $currentCategory = [
                    'name' => $line,
                    'key' => Str::slug($line),
                    'slug' => Str::slug($line),
                    'reference_code' => $referenceCodeCounter++,
                    'parent_id' => null,
                    'sort_order' => count($categories) + 1,
                    'subcategories' => [],
                ];
            } else {
                // Es una subcategoría
                if ($currentCategory === null) {
                    // Si no hay categoría padre, crear una genérica
                    $currentCategory = [
                        'name' => 'Categoría General',
                        'key' => 'categoria-general',
                        'slug' => 'categoria-general',
                        'reference_code' => $referenceCodeCounter++,
                        'parent_id' => null,
                        'sort_order' => 1,
                        'subcategories' => [],
                    ];
                }

                $subcategoryKey = Str::slug($line);
                $currentCategory['subcategories'][] = [
                    'name' => $line,
                    'key' => $subcategoryKey,
                    'slug' => $subcategoryKey,
                    'reference_code' => $referenceCodeCounter++,
                    'sort_order' => count($currentCategory['subcategories']) + 1,
                ];
            }
        }

        // Agregar la última categoría
        if ($currentCategory !== null) {
            $categories[] = $currentCategory;
        }

        // Insertar categorías y subcategorías en la base de datos
        $this->seedCategories($categories);

        // Completar slug y reference_code para categorías existentes que no los tengan
        $this->completeMissingData();
    }

    /**
     * Determina si una línea es una categoría principal
     */
    private function isMainCategory(string $line, ?array $currentCategory): bool
    {
        // Patrones que indican una categoría principal
        $mainCategoryPatterns = [
            'Servicios Profesionales de',
            'Actividad Comercial',
            'Actividad de Servicios',
            'Actividad Industrial',
            'Actividad Agrícola',
            'Actividad Ganadera',
            'Actividad Forestal',
            'Actividad Pesquera',
            'Actividad Minera',
            'Actividad de Construcción',
            'Actividad de Transporte',
            'Actividad de Telecomunicaciones',
            'Servicios de Instalaciones Eléctricas',
            'Servicios de Climatización y Aire Acondicionado',
            'Actividad de Gasfitería y Fontanería',
            'Servicios de Revestimientos y Pintura',
            'Actividad de Carpintería y Ebanistería',
            'Servicios de Estructuras Metálicas y Soldadura',
            'Servicios Profesionales de Topografía',
            'Actividad de Movimiento de Tierras y Excavación',
            'Servicios de Prevención de Riesgos Laborales',
            'Actividad de Gestión de Residuos y Reciclaje',
            'Servicios de Automatización y Domótica',
            'Actividad de Obras Civiles y Viales',
            'Servicios Profesionales de Geotecnia',
        ];

        // Verificar si la línea coincide con algún patrón de categoría principal
        foreach ($mainCategoryPatterns as $pattern) {
            if (str_starts_with($line, $pattern)) {
                return true;
            }
        }

        // También verificar patrones más generales
        if (str_starts_with($line, 'Servicios Profesionales de') ||
            str_starts_with($line, 'Actividad de ') ||
            str_starts_with($line, 'Actividad ') ||
            str_starts_with($line, 'Servicios de ')) {
            return true;
        }

        // Si no hay categoría padre activa, esta línea debe ser una categoría principal
        if ($currentCategory === null) {
            return true;
        }

        return false;
    }

    /**
     * Inserta las categorías y subcategorías en la base de datos
     */
    private function seedCategories(array $categories): void
    {
        $totalCategories = 0;
        $totalSubcategories = 0;

        foreach ($categories as $categoryData) {
            // Crear o actualizar categoría principal
            $category = ServiceCategory::query()->updateOrCreate(
                ['key' => $categoryData['key']],
                [
                    'name' => $categoryData['name'],
                    'slug' => $categoryData['slug'],
                    'parent_id' => null,
                    'sort_order' => $categoryData['sort_order'],
                ],
            );

            // Actualizar reference_code si no existe o es diferente
            if ($category->reference_code !== $categoryData['reference_code']) {
                // Verificar que el código no esté en uso
                $existing = ServiceCategory::query()
                    ->where('reference_code', $categoryData['reference_code'])
                    ->where('id', '!=', $category->id)
                    ->first();

                if ($existing === null) {
                    $category->update(['reference_code' => $categoryData['reference_code']]);
                } else {
                    // Si el código está en uso, buscar el siguiente disponible
                    $maxCode = ServiceCategory::query()->max('reference_code') ?? 0;
                    $newCode = max($maxCode + 1, $categoryData['reference_code']);
                    $category->update(['reference_code' => $newCode]);
                }
            }

            $totalCategories++;

            // Crear o actualizar subcategorías
            foreach ($categoryData['subcategories'] as $subcategoryData) {
                $subcategory = ServiceCategory::query()->updateOrCreate(
                    ['key' => $subcategoryData['key']],
                    [
                        'name' => $subcategoryData['name'],
                        'slug' => $subcategoryData['slug'],
                        'parent_id' => $category->id,
                        'sort_order' => $subcategoryData['sort_order'],
                    ],
                );

                // Actualizar reference_code si no existe o es diferente
                if ($subcategory->reference_code !== $subcategoryData['reference_code']) {
                    // Verificar que el código no esté en uso
                    $existing = ServiceCategory::query()
                        ->where('reference_code', $subcategoryData['reference_code'])
                        ->where('id', '!=', $subcategory->id)
                        ->first();

                    if ($existing === null) {
                        $subcategory->update(['reference_code' => $subcategoryData['reference_code']]);
                    } else {
                        // Si el código está en uso, buscar el siguiente disponible
                        $maxCode = ServiceCategory::query()->max('reference_code') ?? 0;
                        $newCode = max($maxCode + 1, $subcategoryData['reference_code']);
                        $subcategory->update(['reference_code' => $newCode]);
                    }
                }

                $totalSubcategories++;
            }
        }

        $this->command->info('Categorías y subcategorías sembradas exitosamente.');
        $this->command->info("Total categorías: {$totalCategories}");
        $this->command->info("Total subcategorías: {$totalSubcategories}");
    }

    /**
     * Completa los datos faltantes (slug y reference_code) para categorías existentes
     */
    private function completeMissingData(): void
    {
        // Obtener el máximo reference_code actual
        $maxCode = ServiceCategory::query()->max('reference_code') ?? 0;
        $nextCode = $maxCode + 1;

        // Completar categorías sin slug o reference_code
        $categoriesWithoutData = ServiceCategory::query()
            ->where(function ($query) {
                $query->whereNull('slug')
                    ->orWhereNull('reference_code');
            })
            ->get();

        foreach ($categoriesWithoutData as $category) {
            $updates = [];

            if (empty($category->slug)) {
                $updates['slug'] = Str::slug($category->name);
            }

            if (empty($category->reference_code)) {
                // Verificar que el código no esté en uso
                while (ServiceCategory::query()->where('reference_code', $nextCode)->exists()) {
                    $nextCode++;
                }
                $updates['reference_code'] = $nextCode;
                $nextCode++;
            }

            if (! empty($updates)) {
                $category->update($updates);
            }
        }

        // Verificar y corregir códigos duplicados
        $duplicates = ServiceCategory::query()
            ->select('reference_code')
            ->groupBy('reference_code')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('reference_code');

        foreach ($duplicates as $duplicateCode) {
            $categoriesWithCode = ServiceCategory::query()
                ->where('reference_code', $duplicateCode)
                ->orderBy('id')
                ->get();

            // Mantener el primero, actualizar los demás
            foreach ($categoriesWithCode->skip(1) as $category) {
                while (ServiceCategory::query()->where('reference_code', $nextCode)->exists()) {
                    $nextCode++;
                }
                $category->update(['reference_code' => $nextCode]);
                $nextCode++;
            }
        }

        if ($categoriesWithoutData->count() > 0 || $duplicates->count() > 0) {
            $this->command->info('Datos faltantes completados.');
        }
    }

    /**
     * Sembrar categorías por defecto si el archivo no existe
     */
    private function seedDefaultCategories(): void
    {
        $top = [
            ['key' => 'fletes', 'name' => 'Fletes y Transporte', 'sort_order' => 10],
            ['key' => 'mudanzas', 'name' => 'Mudanzas', 'sort_order' => 20],
            ['key' => 'logistica', 'name' => 'Logística', 'sort_order' => 30],
        ];

        $topIds = [];
        $referenceCode = 1;

        foreach ($top as $item) {
            $cat = ServiceCategory::query()->updateOrCreate(
                ['key' => $item['key']],
                [
                    'parent_id' => null,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'reference_code' => $referenceCode++,
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
                    'slug' => Str::slug($item['name']),
                    'reference_code' => $referenceCode++,
                    'sort_order' => $item['sort_order'],
                ],
            );
        }
    }
}
