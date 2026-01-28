<?php

namespace App\Http\Controllers;

use App\Models\ServiceBid;
use App\Services\ServiceBidService;
use Illuminate\Http\Response;

class ServiceBidPdfController extends Controller
{
    public function __construct(public ServiceBidService $serviceBidService) {}

    /**
     * Descarga el PDF de una cotización
     */
    public function download(ServiceBid $serviceBid): Response
    {
        // Verificar autorización: el profesional que creó la cotización o el cliente del tenant
        $user = auth()->user();

        $canView = false;

        if ($user->isProvider() && $serviceBid->user_id === $user->id) {
            $canView = true;
        }

        if ($user->isClient() && $user->belongsToTenant($serviceBid->serviceRequest->tenant)) {
            $canView = true;
        }

        if ($user->isAdministrator()) {
            $canView = true;
        }

        abort_unless($canView, 403);

        $pdf = $this->serviceBidService->generatePdf($serviceBid);

        $filename = "cotizacion-{$serviceBid->id}-{$serviceBid->created_at->format('Y-m-d')}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Muestra el PDF en el navegador
     */
    public function show(ServiceBid $serviceBid): Response
    {
        // Verificar autorización: el profesional que creó la cotización o el cliente del tenant
        $user = auth()->user();

        $canView = false;

        if ($user->isProvider() && $serviceBid->user_id === $user->id) {
            $canView = true;
        }

        if ($user->isClient() && $user->belongsToTenant($serviceBid->serviceRequest->tenant)) {
            $canView = true;
        }

        if ($user->isAdministrator()) {
            $canView = true;
        }

        abort_unless($canView, 403);

        $pdf = $this->serviceBidService->generatePdf($serviceBid);

        return $pdf->stream("cotizacion-{$serviceBid->id}.pdf");
    }
}
