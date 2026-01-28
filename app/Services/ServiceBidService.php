<?php

namespace App\Services;

use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\User;
use App\ServiceBidStatus;
use App\ServiceRequestStatus;

class ServiceBidService
{
    public function __construct(public WorkOrderService $workOrderService) {}

    public function submit(
        User $actor,
        ServiceRequest $serviceRequest,
        string $amount,
        ?string $message = null,
        string $currency = 'CLP',
    ): ServiceBid {
        // Convertir a entero (sin decimales) para formato chileno
        $amountInteger = (int) round((float) $amount);

        // Obtener vigencia desde configuración del sistema
        $validityDays = (int) SystemSetting::get('quote_validity_days', 15);

        return ServiceBid::updateOrCreate(
            [
                'service_request_id' => $serviceRequest->id,
                'user_id' => $actor->id,
            ],
            [
                'amount' => $amountInteger,
                'currency' => $currency,
                'message' => $message,
                'status' => ServiceBidStatus::Submitted,
                'valid_until' => now()->addDays($validityDays),
            ],
        );
    }

    public function withdraw(ServiceBid $bid): ServiceBid
    {
        $bid->update([
            'status' => ServiceBidStatus::Withdrawn,
        ]);

        return $bid;
    }

    public function accept(ServiceBid $bid): ServiceBid
    {
        $request = $bid->serviceRequest;

        $request->bids()
            ->whereKeyNot($bid->getKey())
            ->update([
                'status' => ServiceBidStatus::Rejected,
            ]);

        $bid->update([
            'status' => ServiceBidStatus::Accepted,
        ]);

        $request->update([
            'status' => ServiceRequestStatus::Awarded,
            'awarded_bid_id' => $bid->id,
        ]);

        $this->workOrderService->createFromBid($bid);

        return $bid;
    }

    /**
     * Genera un PDF de la cotización
     * Requiere barryvdh/laravel-dompdf instalado
     */
    public function generatePdf(ServiceBid $bid)
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            throw new \RuntimeException('Laravel DomPDF no está instalado. Ejecuta: composer require barryvdh/laravel-dompdf');
        }

        $bid->load(['user', 'serviceRequest.category', 'serviceRequest.tenant']);

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quote', [
            'bid' => $bid,
        ])->setPaper('a4', 'portrait');
    }
}
