<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Repositories\TokenRepository;
use App\Repositories\UserRepository;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthService {
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenRepository $tokenRepository
    ) {
    }

    public function authenticate($mobile, $password)
    {
        $user = $this->userRepository->findByMobileOrCreate($mobile, $password);

        if (!$this->userRepository->validateUserPassword($user, $password)) {
            return response()->api(['error' => 'Unauthorized'],401);
        }

        $token = $this->getValidTokenForUser($user);

        return response()->api([
            'token' => $token,
            'user' => new UserResource($user)
        ]);
    }

    public function getValidTokenForUser($user): string
    {
        $existingToken = $this->tokenRepository->getToken($user->id);

        if (!$existingToken || $this->isTokenInvalid($existingToken)) {
            $token = JWTAuth::claims(['id' => $user->id, 'mobile' => $user->mobile])->fromUser($user);
            $this->tokenRepository->storeToken($user->id, $token);
            return $token;
        }

        return $existingToken;
    }

    public function isTokenInvalid($token): bool
    {
        try {
            JWTAuth::setToken($token)->authenticate();
            return false;
        } catch (TokenExpiredException|\Exception $e) {
            return true;
        }
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            $this->tokenRepository->addToBlacklist($token);
            return response()->api([['message' => 'User successfully signed out']]);
        } catch (\Exception $e) {
            return response()->api(['error' => $e->getMessage()], 500);
        }
    }
}
