<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Storage;

class ServiceRequestPdfService
{
    /**
     * Genera el PDF de la solicitud y lo guarda en storage.
     * Devuelve la ruta relativa (ej. service-requests/123/solicitud.pdf) o null si no se pudo generar.
     * Requiere barryvdh/laravel-dompdf o dompdf/dompdf instalado.
     */
    public function generate(ServiceRequest $serviceRequest): ?string
    {
        $serviceRequest->load([
            'category',
            'category.parent',
            'tenant',
            'attachments',
            'region',
            'commune',
        ]);

        $html = view('pdf.service-request', [
            'request' => $serviceRequest,
        ])->render();

        $dir = 'service-requests/'.$serviceRequest->id;
        $filename = 'solicitud.pdf';
        $relativePath = $dir.'/'.$filename;
        $fullPath = storage_path('app/public/'.$relativePath);

        Storage::disk('public')->makeDirectory($dir);

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->save($fullPath);

            return $relativePath;
        }

        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($fullPath, $dompdf->output());

            return $relativePath;
        }

        return null;
    }
}
