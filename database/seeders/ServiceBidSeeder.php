<?php

namespace Database\Seeders;

use App\Helpers\ChileanDataHelper;
use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\Models\User;
use App\ServiceBidStatus;
use App\ServiceRequestStatus;
use Illuminate\Database\Seeder;

class ServiceBidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = User::query()->where('system_role', 'user')->get();

        if ($providers->isEmpty()) {
            return;
        }

        ServiceRequest::query()
            ->where('status', ServiceRequestStatus::Published->value)
            ->get()
            ->each(function (ServiceRequest $request) use ($providers): void {
                $bidders = $providers->random(min($providers->count(), random_int(1, 5)));

                foreach ($bidders as $bidder) {
                    ServiceBid::updateOrCreate(
                        [
                            'service_request_id' => $request->id,
                            'user_id' => $bidder->id,
                        ],
                        [
                            'amount' => fake()->randomFloat(0, 20000, 500000),
                            'currency' => 'CLP',
                            'message' => ChileanDataHelper::fleteBidMessage(),
                            'status' => ServiceBidStatus::Submitted->value,
                            'valid_until' => now()->addDays(15),
                        ],
                    );
                }
            });
    }
}
