<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
        
        $jwtService = new JWTService();

        if (!$jwtService->validateToken($token)) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $payload = $jwtService->decodeToken($token);

        $user = new User();
        $user->id = $payload->id;
        $user->name = $payload->name;
        $user->email = $payload->email;
        $user->role_id = $payload->role_id;

        $user->exists = true;
        $user->wasRecentlyCreated = false;

        app()->instance('jwt.user', $user);

        return $next($request);
    }
}