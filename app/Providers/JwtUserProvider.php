<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use App\Models\User;

class JwtUserProvider implements UserProvider
{
    public function retrieveById($data)
    {
        $user = new User();
        $user->id = $data->id;
        $user->name = $data->name;
        $user->email = $data->email;
        $user->role_id = $data->role_id;

        $user->exists = true;
        $user->wasRecentlyCreated = false;

        return $user;
    }

    public function retrieveByToken($identifier, $token)
    {
        // Not needed for JWT
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Not needed for JWT
    }

    public function retrieveByCredentials(array $credentials)
    {
        // Not needed for JWT
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Not needed for JWT
        return false;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // Not needed for JWT
        return false;
    }
}