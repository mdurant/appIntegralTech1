<?php

namespace Database\Seeders;

use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\ServiceBidStatus;
use App\ServiceRequestStatus;
use App\WorkOrderStatus;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $request = ServiceRequest::query()
            ->where('status', ServiceRequestStatus::Published->value)
            ->inRandomOrder()
            ->first();

        if (! $request) {
            return;
        }

        $bid = ServiceBid::query()
            ->where('service_request_id', $request->id)
            ->inRandomOrder()
            ->first();

        if (! $bid) {
            return;
        }

        $bid->update(['status' => ServiceBidStatus::Accepted->value]);
        $request->update(['status' => ServiceRequestStatus::Awarded->value, 'awarded_bid_id' => $bid->id]);

        WorkOrder::query()->updateOrCreate(
            ['service_request_id' => $request->id],
            [
                'service_bid_id' => $bid->id,
                'tenant_id' => $request->tenant_id,
                'awarded_to_user_id' => $bid->user_id,
                'status' => WorkOrderStatus::Open->value,
            ],
        );
    }
}
