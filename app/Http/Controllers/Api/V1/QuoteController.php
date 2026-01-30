<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreQuoteRequest;
use App\Http\Resources\Api\V1\QuoteResource;
use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\ServiceBidStatus;
use App\ServiceRequestStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class QuoteController extends Controller
{
    #[OA\Get(
        path: '/api/v1/quotes',
        summary: 'Listar mis cotizaciones',
        description: 'Lista las cotizaciones enviadas por el usuario autenticado. Paginación: per_page (1-100, default 15).',
        tags: ['Cotizaciones'],
        parameters: [
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de cotizaciones'),
            new OA\Response(response: 403, description: 'Acceso a API requiere plan Pro o Enterprise'),
            new OA\Response(response: 429, description: 'Límite de solicitudes excedido (rate limit)'),
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Auth::user()->bids()->with('serviceRequest.category')->orderByDesc('created_at');
        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);
        $quotes = $query->paginate($perPage);

        return QuoteResource::collection($quotes);
    }

    #[OA\Post(
        path: '/api/v1/quotes',
        summary: 'Crear cotización',
        description: 'Crea una cotización para una oportunidad. Requiere token con capacidad full_access (no read_only).',
        tags: ['Cotizaciones'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['service_request_id', 'amount'],
                properties: [
                    new OA\Property(property: 'service_request_id', type: 'integer', example: 1),
                    new OA\Property(property: 'amount', type: 'number', example: 150000),
                    new OA\Property(property: 'currency', type: 'string', example: 'CLP'),
                    new OA\Property(property: 'message', type: 'string'),
                    new OA\Property(property: 'valid_until', type: 'string', format: 'date'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Cotización creada'),
            new OA\Response(response: 403, description: 'Acceso denegado o token solo lectura'),
            new OA\Response(response: 422, description: 'Validación fallida o oportunidad no disponible'),
            new OA\Response(response: 429, description: 'Límite de solicitudes excedido (rate limit)'),
        ]
    )]
    public function store(StoreQuoteRequest $request): JsonResponse|QuoteResource
    {
        $user = Auth::user();

        if ($user->tokenCan('read_only')) {
            return response()->json([
                'message' => __('Este token solo tiene permiso de lectura. Usa una clave con acceso completo para enviar cotizaciones.'),
            ], 403);
        }

        $serviceRequest = ServiceRequest::findOrFail($request->validated('service_request_id'));

        if ($serviceRequest->status !== ServiceRequestStatus::Published) {
            return response()->json([
                'message' => __('La oportunidad no está publicada o ya no acepta cotizaciones.'),
            ], 422);
        }

        if ($serviceRequest->expires_at && $serviceRequest->expires_at->isPast()) {
            return response()->json([
                'message' => __('La oportunidad ha expirado.'),
            ], 422);
        }

        $existingBid = ServiceBid::withoutGlobalScope('nonExpired')
            ->where('service_request_id', $serviceRequest->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($existingBid) {
            return response()->json([
                'message' => __('Ya has enviado una cotización para esta oportunidad.'),
            ], 422);
        }

        $bid = ServiceBid::create([
            'service_request_id' => $serviceRequest->id,
            'user_id' => $user->id,
            'amount' => $request->validated('amount'),
            'currency' => $request->validated('currency', 'CLP'),
            'message' => $request->validated('message'),
            'status' => ServiceBidStatus::Submitted,
            'valid_until' => $request->validated('valid_until'),
        ]);

        $bid->load('serviceRequest.category');

        return (new QuoteResource($bid))->response()->setStatusCode(201);
    }
}
