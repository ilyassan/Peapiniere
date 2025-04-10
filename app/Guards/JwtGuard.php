<?php

namespace App\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use App\Services\JWTService;
use Illuminate\Support\Facades\Log;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $provider;
    protected $user;
    protected $jwtService;

    public function __construct(UserProvider $provider, Request $request, JWTService $jwtService)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->jwtService = $jwtService;
        $this->user = null;
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        // Get the token from the request
        $token = $this->request->cookie('jwt');;

        // If no token is provided, return null
        if (!$token) {
            return null;
        }

        // Validate the token
        if (!$this->jwtService->validateToken($token)) {
            return null;
        }

        // Decode the token to get the payload
        $payload = $this->jwtService->decodeToken($token);

        // Retrieve the user from the payload using the provider
        $this->user = $this->provider->retrieveById($payload);

        return $this->user;
    }

    public function validate(array $credentials = [])
    {
        // Not needed for JWT
        return false;
    }
}