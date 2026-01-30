<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: 'API v1',
        version: '1.0',
        description: 'API de oportunidades y cotizaciones. Requiere plan Pro o Enterprise. Autenticación: Bearer token (API Key). Límites: Pro 60 req/min, Enterprise 500 req/min.'
    ),
    servers: [new OA\Server(url: '/api', description: 'API base')],
    security: [['bearerAuth' => []]],
    tags: [
        new OA\Tag(name: 'Oportunidades', description: 'Listar oportunidades publicadas'),
        new OA\Tag(name: 'Cotizaciones', description: 'Crear y listar cotizaciones'),
    ],
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                name: 'Authorization',
                type: 'http',
                in: 'header',
                scheme: 'bearer',
                bearerFormat: 'Token',
                description: 'Header: Authorization: Bearer {token}. El token es la API Key creada en Configuración > Pagos y Planes.'
            ),
        ]
    )
)]
abstract class BaseOpenApi {}
