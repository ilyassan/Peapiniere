<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(RegisterUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            $jwtService = new JWTService();

            // Create payload
            $payload = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
            ];

            $token = $jwtService->generateToken($payload);

            if (!$token) {
                return response()->json(['message' => 'Failed to generate token'], 500);
            }
            
            return response()->json(['token' => $token], 201);

        } catch (\Throwable $th) {            
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}