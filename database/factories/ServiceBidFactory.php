<?php

namespace Database\Factories;

use App\Models\ServiceRequest;
use App\Models\User;
use App\ServiceBidStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceBid>
 */
class ServiceBidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_request_id' => ServiceRequest::factory(),
            'user_id' => User::factory()->provider(),
            'amount' => fake()->randomFloat(2, 10, 5000),
            'currency' => 'CLP',
            'message' => fake()->optional()->sentence(),
            'status' => ServiceBidStatus::Submitted->value,
        ];
    }
}
