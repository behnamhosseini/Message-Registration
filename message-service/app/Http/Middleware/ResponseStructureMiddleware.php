<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\isInstanceOf;

class ResponseStructureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof Response) {
            $originalContent = $response->getContent();
            $customStructure = [
                'data' => json_decode($originalContent),
                'server_time' => now()->toIso8601String(),
            ];
            $response->setContent(json_encode($customStructure));
        }

        return $response;
    }
}
