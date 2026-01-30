<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OpportunityResource;
use App\Models\ServiceRequest;
use App\ServiceRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class OpportunityController extends Controller
{
    #[OA\Get(
        path: '/api/v1/opportunities',
        summary: 'Listar oportunidades',
        description: 'Lista oportunidades (solicitudes de servicio) publicadas y vigentes. Solo planes Pro y Enterprise. Filtros opcionales: category_id, region_id. Paginación: per_page (1-100, default 15).',
        tags: ['Oportunidades'],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'region_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de oportunidades'),
            new OA\Response(response: 403, description: 'Acceso a API requiere plan Pro o Enterprise'),
            new OA\Response(response: 429, description: 'Límite de solicitudes excedido (rate limit)'),
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ServiceRequest::query()
            ->where('status', ServiceRequestStatus::Published)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->with('category', 'region', 'commune')
            ->orderByDesc('published_at');

        $query->when($request->filled('category_id'), function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        });
        $query->when($request->filled('region_id'), function ($q) use ($request) {
            $q->where('region_id', $request->region_id);
        });

        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);
        $opportunities = $query->paginate($perPage);

        return OpportunityResource::collection($opportunities);
    }
}
