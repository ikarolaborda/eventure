<?php

namespace App\Http\Controllers\Api\Documentation;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: "1.0.0",
        description: "Simple API for Online Event Booking",
        title: "Eventure API"
    ),
    servers: [
        new OA\Server(url: "http://localhost:8000", description: "Local development server")
    ],
    security: [
        ["bearerAuth" => []]
    ]
)]
#[OA\Components(
    securitySchemes: [
        new OA\SecurityScheme(
            securityScheme: "bearerAuth",
            type: "http",
            bearerFormat: "JWT",
            scheme: "bearer"
        )
    ]
)]
class SwaggerOpenApi
{
}
