<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/signup",
     *     summary="Register a new user",
     *     description="Registers a new user with the provided details and returns a JWT token.",
     *     operationId="signup",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role_id"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role_id", type="integer", example="2")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error or invalid role",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid role selected.")
     *         )
     *     )
     * )
     */
    public function signup(RegisterUserRequest $request)
    {
        try {
            if ($request->role_id == RoleEnum::ADMIN) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid role selected.',
                ], 500);
            }

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
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to generate token'
                ], 500);
            }

            return response()->json(["message" => "Registered Successfully."], 201)->cookie('jwt', $token, 60, '/', null, true, true, false, 'Strict');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user",
     *     description="Authenticates a user with the provided credentials and returns a JWT token.",
     *     operationId="login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function login(LoginUserRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

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
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to generate token'
                ], 500);
            }

            return response()->json(["message" => "Logged in successfully."], 200)->cookie('jwt', $token, 60, '/', null, true, true, false, 'Strict');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     description="Logs out the currently authenticated user.",
     *     operationId="logout",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        return response()->json(['message' => 'Logged out successfully'], 200)->withCookie(cookie()->forget('jwt'));;
    }
}