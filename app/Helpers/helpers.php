<?php

use App\Models\User;

if (! function_exists('user')) {
    /**
     * Get the currently authenticated user (from JWT payload).
     *
     * @return User|null
     */
    function user(): User | null
    {
        if (app()->has('jwt.user')) {
            return app('jwt.user');
        }
        return null;
    }
}