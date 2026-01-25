<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequestAttachment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceRequestAttachmentController extends Controller
{
    public function __invoke(ServiceRequestAttachment $attachment): StreamedResponse
    {
        $attachment->loadMissing('serviceRequest.tenant');

        Gate::authorize('view', $attachment->serviceRequest);

        abort_unless(Storage::disk('public')->exists($attachment->path), 404);

        return Storage::disk('public')->response($attachment->path);
    }
}

