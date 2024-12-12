<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\Auth\HasJwtAssets;
use App\Http\Requests\Api\Auth\UserRegisterRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class RegisterController
{
    use HasJwtAssets;

    #[OA\Post(
        path: "/api/auth/register",
        operationId: "registerUser",
        summary: "Registers a new user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UserRegisterRequest")
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "User registered",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", type: "object"),
                        new OA\Property(property: "token", type: "string"),
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error")
        ]
    )]
    public function __invoke(UserRegisterRequest $request): Response
    {

        $user = User::create($request->validated());
        return response()->json([
            'user' => $user,
            'token' => $this->fromUser($user)
            ], Response::HTTP_CREATED);

    }

}
