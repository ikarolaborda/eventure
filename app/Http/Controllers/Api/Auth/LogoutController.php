<?php

namespace App\Http\Controllers\Api\Auth;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class LogoutController
{
    #[OA\Post(
        path: "/api/auth/logout",
        operationId: "logoutUser",
        summary: "Logs out the authenticated user",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Logout successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string"),
                        new OA\Property(property: "msg", type: "string"),
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function __invoke(string $token)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'status' => 'success',
                'msg' => 'You have successfully logged out.'
            ]);
        } catch (JWTException $e) {
            JWTAuth::unsetToken();
            return response()->json([
                'status' => 'error',
                'msg' => 'Failed to logout, please try again.' . $e->getMessage()
            ]);
        }
    }

}
