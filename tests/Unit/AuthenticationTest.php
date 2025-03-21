<?php

namespace Tests\Unit;

use App\Enums\RoleEnum;
use App\Http\Controllers\AuthController;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    protected $authController;
    protected $jwtService;

    public function setUp(): void
    {
        parent::setUp();
        $this->authController = new AuthController();
        $this->jwtService = Mockery::mock(JWTService::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_signup_creates_user_and_returns_token_with_valid_data()
    {
        // Mock the RegisterUserRequest
        $request = Mockery::mock(RegisterUserRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role_id' => RoleEnum::CLIENT,
        ]);

        // Mock the User model
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->name = 'John Doe';
        $user->email = 'john@example.com';
        $user->role_id = RoleEnum::CLIENT;

        // Mock User::create
        User::shouldReceive('create')->once()->with([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Mockery::on(function ($hashedPassword) {
                return Hash::check('password123', $hashedPassword);
            }),
            'role_id' => RoleEnum::CLIENT,
        ])->andReturn($user);

        // Mock JWTService
        $this->jwtService->shouldReceive('generateToken')->once()->with([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role_id' => RoleEnum::CLIENT,
        ])->andReturn('mock-token');

        // Inject the mocked JWTService into the controller
        $this->authController->setJwtService($this->jwtService);

        // Call the signup method
        $response = $this->authController->signup($request);

        // Assert the response
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['token' => 'mock-token'], $response->getData(true));
    }

    public function test_signup_returns_error_for_invalid_role()
    {
        // Mock the RegisterUserRequest
        $request = Mockery::mock(RegisterUserRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role_id' => RoleEnum::ADMIN, // Invalid role
        ]);

        // Call the signup method
        $response = $this->authController->signup($request);

        // Assert the response
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Invalid role selected.',
        ], $response->getData(true));
    }

    public function test_signup_handles_server_error()
    {
        // Mock the RegisterUserRequest to throw an exception
        $request = Mockery::mock(RegisterUserRequest::class);
        $request->shouldReceive('validated')->andThrow(new \Exception('Server error'));

        // Call the signup method
        $response = $this->authController->signup($request);

        // Assert the response
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Server error',
        ], $response->getData(true));
    }

    public function test_login_returns_token_with_valid_credentials()
    {
        // Mock the LoginUserRequest
        $request = Mockery::mock(LoginUserRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        // Mock the User model
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->name = 'John Doe';
        $user->email = 'john@example.com';
        $user->role_id = RoleEnum::CLIENT;
        $user->password = Hash::make('password123');

        // Mock User::where and first
        User::shouldReceive('where')->once()->with('email', 'john@example.com')->andReturnSelf();
        User::shouldReceive('first')->once()->andReturn($user);

        // Mock JWTService
        $this->jwtService->shouldReceive('generateToken')->once()->with([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role_id' => RoleEnum::CLIENT,
        ])->andReturn('mock-token');

        // Inject the mocked JWTService into the controller
        $this->authController->setJwtService($this->jwtService);

        // Call the login method
        $response = $this->authController->login($request);

        // Assert the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['token' => 'mock-token'], $response->getData(true));
    }

    public function test_login_returns_error_with_invalid_credentials()
    {
        // Mock the LoginUserRequest
        $request = Mockery::mock(LoginUserRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        // Mock the User model
        $user = Mockery::mock(User::class)->makePartial();
        $user->password = Hash::make('password123');

        // Mock User::where and first
        User::shouldReceive('where')->once()->with('email', 'john@example.com')->andReturnSelf();
        User::shouldReceive('first')->once()->andReturn($user);

        // Call the login method
        $response = $this->authController->login($request);

        // Assert the response
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['message' => 'Invalid credentials'], $response->getData(true));
    }

    public function test_login_handles_server_error()
    {
        // Mock the LoginUserRequest to throw an exception
        $request = Mockery::mock(LoginUserRequest::class);
        $request->shouldReceive('validated')->andThrow(new \Exception('Server error'));

        // Call the login method
        $response = $this->authController->login($request);

        // Assert the response
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals([
            'status' => false,
            'message' => 'Server error',
        ], $response->getData(true));
    }

    public function test_logout_returns_success_message()
    {
        // Call the logout method
        $response = $this->authController->logout();

        // Assert the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Logged out successfully'], $response->getData(true));
    }
}