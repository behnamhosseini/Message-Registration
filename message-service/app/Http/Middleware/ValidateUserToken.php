<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;

class ValidateUserToken
{

    public function __construct(protected TokenService $tokenService)
    {
    }
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $validation = $this->tokenService->validateToken($token);

        if (isset($validation['status'])) {
            return response()->json(['error' => $validation['error']], $validation['status']);
        }
        $request->request->add(['user_id' => $validation['user_id']]);
        return $next($request);
    }
}
