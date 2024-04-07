<?php

namespace App\Services;

use App\Repositories\TokenRepository;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{

    public function __construct( protected TokenRepository $tokenRepository){}

    public function validateToken($token)
    {
        if (!$token || $this->tokenRepository->isTokenBlacklisted($token)) {
            return ['error' => 'Token is invalid', 'status' => 401];
        }

        try {
            if (!JWTAuth::parseToken()->check()) {
                return ['error' => 'Token is expired', 'status' => 401];
            }

            $payload = JWTAuth::parseToken()->getPayload();
            return ['user_id' => $payload->get('sub'), 'status' => null];
        } catch (\Exception $e) {
            return ['error' => 'Token processing error', 'status' => 401];
        }
    }
}
