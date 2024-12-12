<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\Auth\HasJwtAssets;
use App\Http\Requests\Api\Auth\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Process\Exception\RuntimeException;
use Tymon\JWTAuth\JWTGuard;

class LoginController
{

    use HasJwtAssets;

    #[OA\Post(
        path: "/api/auth/login",
        operationId: "loginUser",
        summary: "Logs in a user and returns a JWT token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UserLoginRequest")
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Login successful",
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
    public function __invoke(UserLoginRequest $request): Response
    {
        $this->checkForJwtGuard(Auth('api'));

        if (!Auth::attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $token = Auth::attempt($request->validated());

        return $this->respondWithToken($token);
    }

}
