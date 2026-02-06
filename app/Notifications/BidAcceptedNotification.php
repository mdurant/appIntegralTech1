<?php

namespace App\Notifications;

use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use Illuminate\Notifications\Notification;

/**
 * Notificación al usuario/proveedor cuando el cliente acepta su presupuesto y se crea la Orden de Trabajo.
 */
class BidAcceptedNotification extends Notification
{
    public function __construct(
        public ServiceBid $bid,
        public ServiceRequest $serviceRequest,
        public WorkOrder $workOrder,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bid_accepted',
            'message' => __('Tu presupuesto fue aceptado. Se ha creado la Orden de Trabajo.'),
            'service_request_id' => $this->serviceRequest->id,
            'service_request_reference_id' => $this->serviceRequest->reference_id,
            'service_request_title' => $this->serviceRequest->title,
            'work_order_id' => $this->workOrder->id,
            'bid_id' => $this->bid->id,
            'url' => route('provider.work-orders.show', $this->workOrder),
        ];
    }
}
