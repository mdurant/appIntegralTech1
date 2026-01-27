<?php

namespace Database\Factories;

use App\Helpers\ChileanDataHelper;
use App\SystemRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ChileanDataHelper::chileanName();
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0] ?? $name;
        $lastName = $nameParts[1] ?? '';

        return [
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => ChileanDataHelper::chileanEmail($name),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'system_role' => 'user',
            'current_tenant_id' => null,
            'rut' => ChileanDataHelper::chileanRut(),
            'giro_sii' => null,
            'gender' => fake()->randomElement(['hombre', 'mujer']),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'fantasy_name' => null,
            'economic_activity' => null,
            'region_id' => null,
            'commune_id' => null,
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'system_role' => SystemRole::Admin->value,
        ]);
    }

    public function guest(): static
    {
        return $this->state(fn () => [
            'system_role' => SystemRole::Guest->value,
        ]);
    }

    public function client(): static
    {
        return $this->state(fn () => [
            'system_role' => SystemRole::Client->value,
        ]);
    }

    public function provider(): static
    {
        return $this->state(fn () => [
            'system_role' => SystemRole::User->value,
            'giro_sii' => ChileanDataHelper::giroSii(),
            'fantasy_name' => ChileanDataHelper::fleteCompanyName(),
            'economic_activity' => 'Transporte de carga y fletes',
        ]);
    }
}
