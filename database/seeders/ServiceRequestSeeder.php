<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceRequestFieldAnswer;
use App\Models\ServiceRequest;
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

                $request = ServiceRequest::create([
                    'tenant_id' => $tenant->id,
                    'category_id' => $category->id,
                    'created_by_user_id' => $owner->id,
                    'contact_name' => $owner->name,
                    'contact_email' => $owner->email,
                    'contact_phone' => fake()->phoneNumber(),
                    'title' => fake()->sentence(6),
                    'description' => fake()->paragraphs(asText: true),
                    'location_text' => fake()->city().', Chile',
                    'address' => fake()->streetAddress(),
                    'notes' => fake()->paragraphs(asText: true),
                    'status' => ServiceRequestStatus::Draft->value,
                ]);

                $this->seedAnswers($request);
            }

            foreach (range(1, 3) as $i) {
                $category = $categories[array_rand($categories)];

                $request = ServiceRequest::create([
                    'tenant_id' => $tenant->id,
                    'category_id' => $category->id,
                    'created_by_user_id' => $owner->id,
                    'contact_name' => $owner->name,
                    'contact_email' => $owner->email,
                    'contact_phone' => fake()->phoneNumber(),
                    'title' => fake()->sentence(6),
                    'description' => fake()->paragraphs(asText: true),
                    'status' => ServiceRequestStatus::Published->value,
                    'published_at' => now()->subDays(random_int(0, 7)),
                    'expires_at' => now()->addDays(15 - random_int(0, 7)),
                    'location_text' => fake()->city().', Chile',
                    'address' => fake()->streetAddress(),
                    'notes' => fake()->paragraphs(asText: true),
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

            if ($field->type->value === 'select' && $field->options->count()) {
                $value = $field->options->random()->value;
            } elseif ($field->type->value === 'number') {
                $value = (string) random_int(1, 100);
            } elseif ($field->type->value === 'date') {
                $value = now()->addDays(random_int(1, 60))->format('Y-m-d');
            } elseif ($field->type->value === 'textarea') {
                $value = fake()->paragraph();
            } else {
                $value = fake()->sentence();
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
