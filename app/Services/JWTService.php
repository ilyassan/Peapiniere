<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTService
{
    /**
     * @var string The secret key used to sign the JWT.  Store securely (env var).
     */
    private string $secretKey;

    /**
     * @var string The hashing algorithm to use.  HS256 is common.
     */
    private string $algorithm;

    public function __construct()
    {
        $this->secretKey = config('app.jwt_secret');
        $this->algorithm = 'HS256';
    }

    /**
     * Generates a JWT for the given user.
     *
     * @param array $payload  Payload with user data.
     * @param int $expirationTime (seconds)
     * @return string|null The JWT, or null on failure.
     */
    public function generateToken(array $payload, int $expirationTime = 3600): ?string
    {
        try {
            $payload['iss'] = request()->getHost(); // Issuer
            $payload['aud'] = request()->getHost(); // Audience
            $payload['iat'] = time();  // Issued At
            $payload['nbf'] = time();  // Not Before
            $payload['exp'] = time() + $expirationTime; // Expiration time

            return JWT::encode($payload, $this->secretKey, $this->algorithm);
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("JWT Generation Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Decodes a JWT.
     *
     * @param string $token The JWT to decode.
     * @return object|null The decoded JWT payload as an object, or null on failure.
     */
    public function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, $this->algorithm));
        } catch (Exception $e) {
            // Log the error
            error_log("JWT Decoding Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validates a JWT.  This checks signature, expiration, etc.
     *
     * @param string $token The JWT to validate.
     * @return bool True if valid, false otherwise.
     */
    public function validateToken(string $token): bool
    {
        $payload = $this->decodeToken($token);
        return $payload !== null;
    }
}