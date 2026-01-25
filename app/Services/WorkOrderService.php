<?php

namespace App\Services;

use App\Models\ServiceBid;
use App\Models\WorkOrder;
use App\WorkOrderStatus;

class WorkOrderService
{
    public function createFromBid(ServiceBid $bid): WorkOrder
    {
        $request = $bid->serviceRequest;

        return WorkOrder::create([
            'service_request_id' => $request->id,
            'service_bid_id' => $bid->id,
            'tenant_id' => $request->tenant_id,
            'awarded_to_user_id' => $bid->user_id,
            'status' => WorkOrderStatus::Open,
        ]);
    }
}
