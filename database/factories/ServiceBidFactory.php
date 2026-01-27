<?php

namespace Database\Factories;

use App\Helpers\ChileanDataHelper;
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
            'amount' => fake()->randomFloat(0, 20000, 500000),
            'currency' => 'CLP',
            'message' => ChileanDataHelper::fleteBidMessage(),
            'status' => ServiceBidStatus::Submitted->value,
        ];
    }
}
