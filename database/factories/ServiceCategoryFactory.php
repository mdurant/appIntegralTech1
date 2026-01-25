<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $choices = [
            ['key' => 'home', 'name' => 'Servicios de Hogar'],
            ['key' => 'business', 'name' => 'Servicios Empresa'],
        ];

        $selected = $choices[array_rand($choices)];

        return [
            'key' => $selected['key'].'-'.fake()->unique()->randomNumber(4),
            'name' => $selected['name'],
        ];
    }
}
