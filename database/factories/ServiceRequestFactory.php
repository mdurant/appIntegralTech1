<?php

namespace Database\Factories;

use App\Helpers\ChileanDataHelper;
use App\Models\ServiceCategory;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $location = ChileanDataHelper::randomLocation();
        $contactName = ChileanDataHelper::chileanName();

        return [
            'tenant_id' => Tenant::factory(),
            'category_id' => ServiceCategory::factory(),
            'created_by_user_id' => User::factory()->client(),
            'title' => ChileanDataHelper::fleteRequestTitle(),
            'description' => ChileanDataHelper::fleteRequestDescription(),
            'contact_name' => $contactName,
            'contact_email' => ChileanDataHelper::chileanEmail($contactName),
            'contact_phone' => ChileanDataHelper::chileanPhone(),
            'location_text' => $location['location_text'],
            'address' => ChileanDataHelper::chileanAddress(),
            'notes' => 'Servicio disponible de lunes a viernes. Se requiere vehículo con capacidad adecuada.',
            'status' => ServiceRequestStatus::Draft->value,
            'published_at' => null,
            'expires_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ServiceRequestStatus::Published->value,
            'published_at' => now(),
        ]);
    }
}
