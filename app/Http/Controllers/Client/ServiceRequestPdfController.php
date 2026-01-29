<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceRequestPdfController extends Controller
{
    public function __invoke(Request $request, ServiceRequest $serviceRequest, ServiceRequestPdfService $pdfService): StreamedResponse
    {
        abort_unless(auth()->user()->isClient(), 403);
        abort_unless(auth()->user()->belongsToTenant($serviceRequest->tenant), 403);

        $path = $serviceRequest->pdf_path;
        if (! $path || ! is_file(storage_path('app/public/'.$path))) {
            $path = $pdfService->generate($serviceRequest);
            if ($path !== null) {
                $serviceRequest->update(['pdf_path' => $path]);
            }
        }

        if (! $path || ! is_file(storage_path('app/public/'.$path))) {
            abort(404, __('No se pudo generar el PDF. Verifique que la extensión esté instalada.'));
        }

        $fullPath = storage_path('app/public/'.$path);

        return response()->streamDownload(
            function () use ($fullPath) {
                echo file_get_contents($fullPath);
            },
            'solicitud-'.$serviceRequest->id.'.pdf',
            [
                'Content-Type' => 'application/pdf',
            ],
            'inline',
        );
    }
}
