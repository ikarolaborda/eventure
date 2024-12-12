<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\Auth\HasJwtAssets;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Process\Exception\RuntimeException;
use Tymon\JWTAuth\JWTGuard;

class RefreshController extends Controller
{
    use HasJwtAssets;

    #[OA\Post(
        path: "/api/auth/refresh",
        operationId: "refreshToken",
        summary: "Refreshes the JWT token",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Token refreshed",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "token_type", type: "string"),
                        new OA\Property(property: "expires_in", type: "integer"),
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function __invoke(): JsonResponse
    {
        $guard = $this->checkForJwtGuard(Auth('api'));

        return response()->json([
            'access_token' => $guard->refresh(),
            'token_type' => 'Bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
        ]);
    }
}
