<?php

namespace Database\Seeders;

use App\Helpers\ChileanDataHelper;
use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestFieldAnswer;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ServiceCategory::query()->whereNotNull('parent_id')->get()->all();

        if (count($categories) === 0) {
            return;
        }

        Tenant::query()->with('users')->get()->each(function (Tenant $tenant) use ($categories): void {
            /** @var User|null $owner */
            $owner = $tenant->users->first();

            if (! $owner) {
                return;
            }

            foreach (range(1, 3) as $i) {
                $category = $categories[array_rand($categories)];
                $location = ChileanDataHelper::randomLocation();
                $contactName = ChileanDataHelper::chileanName();

                // Obtener región y comuna desde la BD
                $region = \App\Models\Region::where('name', $location['region'])->first();
                $commune = $region
                    ? \App\Models\Commune::where('region_id', $region->id)
                        ->where('name', $location['comuna'])
                        ->first()
                    : null;

                $request = ServiceRequest::create([
                    'tenant_id' => $tenant->id,
                    'category_id' => $category->id,
                    'created_by_user_id' => $owner->id,
                    'contact_name' => $contactName,
                    'contact_email' => ChileanDataHelper::chileanEmail($contactName),
                    'contact_phone' => ChileanDataHelper::chileanPhone(),
                    'title' => ChileanDataHelper::fleteRequestTitle(),
                    'description' => ChileanDataHelper::fleteRequestDescription(),
                    'location_text' => $location['location_text'],
                    'address' => ChileanDataHelper::chileanAddress(),
                    'region_id' => $region?->id,
                    'commune_id' => $commune?->id,
                    'notes' => 'Por favor, contactar con anticipación para coordinar el servicio. Disponibilidad preferiblemente en horario laboral.',
                    'status' => ServiceRequestStatus::Draft->value,
                ]);

                $this->seedAnswers($request);
            }

            foreach (range(1, 3) as $i) {
                $category = $categories[array_rand($categories)];
                $location = ChileanDataHelper::randomLocation();
                $contactName = ChileanDataHelper::chileanName();

                // Obtener región y comuna desde la BD
                $region = \App\Models\Region::where('name', $location['region'])->first();
                $commune = $region
                    ? \App\Models\Commune::where('region_id', $region->id)
                        ->where('name', $location['comuna'])
                        ->first()
                    : null;

                $request = ServiceRequest::create([
                    'tenant_id' => $tenant->id,
                    'category_id' => $category->id,
                    'created_by_user_id' => $owner->id,
                    'contact_name' => $contactName,
                    'contact_email' => ChileanDataHelper::chileanEmail($contactName),
                    'contact_phone' => ChileanDataHelper::chileanPhone(),
                    'title' => ChileanDataHelper::fleteRequestTitle(),
                    'description' => ChileanDataHelper::fleteRequestDescription(),
                    'status' => ServiceRequestStatus::Published->value,
                    'published_at' => now()->subDays(random_int(0, 7)),
                    'expires_at' => now()->addDays(15 - random_int(0, 7)),
                    'location_text' => $location['location_text'],
                    'address' => ChileanDataHelper::chileanAddress(),
                    'region_id' => $region?->id,
                    'commune_id' => $commune?->id,
                    'notes' => 'Servicio disponible de lunes a viernes. Se requiere vehículo con capacidad adecuada para la carga.',
                ]);

                $this->seedAnswers($request);
            }
        });
    }

    private function seedAnswers(ServiceRequest $request): void
    {
        $fields = ServiceFormField::query()
            ->where('service_category_id', $request->category_id)
            ->with('options')
            ->get();

        foreach ($fields as $field) {
            $value = null;

            // Campos específicos de fletes
            if ($field->key === 'direccion_origen' || $field->key === 'direccion_destino') {
                $location = ChileanDataHelper::randomLocation();
                $value = ChileanDataHelper::chileanAddress().', '.$location['comuna'].', '.$location['region'];
            } elseif ($field->type->value === 'select' && $field->options->count()) {
                $value = $field->options->random()->value;
            } elseif ($field->type->value === 'number') {
                $value = (string) random_int(1, 100);
            } elseif ($field->type->value === 'date') {
                $value = now()->addDays(random_int(1, 60))->format('Y-m-d');
            } elseif ($field->type->value === 'textarea') {
                $value = ChileanDataHelper::fleteRequestDescription();
            } elseif ($field->type->value === 'text') {
                // Para campos de texto genéricos, usar datos relacionados con fletes
                $textos = [
                    'Muebles de sala y comedor',
                    'Electrodomésticos y cajas',
                    'Mercadería comercial',
                    'Materiales de construcción',
                    'Mudanza completa de departamento',
                ];
                $value = fake()->randomElement($textos);
            } else {
                // Fallback para campos de texto genéricos
                $value = 'Información adicional sobre el servicio de flete solicitado';
            }

            ServiceRequestFieldAnswer::updateOrCreate(
                [
                    'service_request_id' => $request->id,
                    'service_form_field_id' => $field->id,
                ],
                [
                    'value' => $value,
                ],
            );
        }
    }
}
