<?php

namespace App\Http\Middleware;

use App\Support\Jwt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($header, 7);
        $payload = Jwt::decode($token);

        if (! $payload || ! isset($payload['sub'])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->attributes->set('sub', $payload['sub']);

        return $next($request);
    }
}
