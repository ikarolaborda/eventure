<?php

namespace App\Traits\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\Process\Exception\RuntimeException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWTGuard;

trait HasJwtAssets
{
    protected JWTGuard $guard;
    public function checkForJwtGuard($guard): JWTGuard | null
    {
        if (! $guard instanceof JWTGuard) {
            throw new RuntimeException('Wrong guard returned.');
        }

        $this->guard = $guard;

        return $this->guard;
    }

    public function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard->factory()->getTTL() * 60
        ]);
    }

    public function fromUser(User $user): string
    {
        return JWTAuth::fromUser($user);
    }
}
