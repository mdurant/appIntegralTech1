<?php

namespace App\Notifications;

use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use Illuminate\Notifications\Notification;

/**
 * Notificación al cliente cuando un usuario/proveedor envía una propuesta económica.
 * Se envía de forma síncrona (sin cola) para que el cliente la vea de inmediato.
 */
class BidReceivedNotification extends Notification
{
    public function __construct(
        public ServiceBid $bid,
        public ServiceRequest $serviceRequest,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bid_received',
            'message' => __('Has recibido una propuesta económica para tu solicitud.'),
            'service_request_id' => $this->serviceRequest->id,
            'service_request_reference_id' => $this->serviceRequest->reference_id,
            'service_request_title' => $this->serviceRequest->title,
            'bid_id' => $this->bid->id,
            'bid_amount' => $this->bid->amount,
            'bid_currency' => $this->bid->currency,
            'from_user_name' => $this->bid->user?->name,
            'url' => route('client.requests.show', $this->serviceRequest),
        ];
    }
}
