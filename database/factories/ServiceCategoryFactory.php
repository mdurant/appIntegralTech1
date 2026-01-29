<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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

        $name = $selected['name'];
        $key = $selected['key'].'-'.fake()->unique()->randomNumber(4);

        return [
            'key' => $key,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->randomNumber(4),
            'reference_code' => ServiceCategory::query()->max('reference_code') + 1,
        ];
    }
}
