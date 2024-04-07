<?php

namespace Tests\Feature\auth;

use App\Models\User;
use App\Repositories\TokenRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testSuccessfulLogin()
    {
        User::create([
            'mobile' => $this->faker->phoneNumber(),
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'mobile' => '09129681057',
            'password' => 123456,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id',
                    'name',
                ],
            ],
            'server_time',
        ]);
    }

    public function testWhenTheUserDoesNotExist()
    {
        $response = $this->postJson('/api/auth/login', [
            'mobile' => '09129681057',
            'password' => 123456,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id',
                    'name',
                ],
            ],
            'server_time',
        ]);
    }


    public function testLoginFailsWithIncorrectCredentials()
    {
        User::create([
            'mobile' => '09129681057',
            'password' => bcrypt('correctPassword'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'mobile' => '09129681057',
            'password' => 'incorrectPassword',
        ]);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'data' => [
                'error',
            ],
            'server_time',
        ]);
    }

    public function testLoginRateLimiting()
    {
        for ($i = 0; $i <= 30; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'mobile' => '09129681057',
                'password' => 'password',
            ]);

            $response->assertSuccessful();
        }

        $response = $this->postJson('/api/auth/login', [
            'mobile' => '09129681057',
            'password' => 'password',
        ]);

        $response->assertStatus(429);
    }

    public function testItCreatesNewTokenIfNoneExists()
    {
        $user = User::create([
            'name' => 'Test User',
            'mobile' => '09129681057',
            'password' => bcrypt('password'),
        ]);

        $mock = Mockery::mock(TokenRepository::class);
        $this->app->instance(TokenRepository::class, $mock);

        $userId = $user->id;
        $mock->shouldReceive('getToken')->once()->with($userId)->andReturn(null);
        $mock->shouldReceive('storeToken')->once()->withArgs(function ($argUserId, $token) {
            return $argUserId == 1 && is_string($token);
        });

        JWTAuth::shouldReceive('claims')->once()->andReturnSelf();
        JWTAuth::shouldReceive('fromUser')->once()->andReturn('fakeToken');

        $authService = new AuthService(new UserRepository(), $mock);
        $token = $authService->getValidTokenForUser($user);
        $this->assertEquals('fakeToken', $token);
    }

    public function testItReturnsExistingValidToken()
    {
        $user = User::create([
            'name' => 'Test User',
            'mobile' => '09129681057',
            'password' => bcrypt('secret'),
        ]);

        $mockTokenRepository = \Mockery::mock(TokenRepository::class);
        $this->app->instance(TokenRepository::class, $mockTokenRepository);

        $existingToken = 'existingToken';
        $mockTokenRepository->shouldReceive('getToken')->once()->with($user->id)->andReturn($existingToken);

        JWTAuth::shouldReceive('setToken')->once()->with($existingToken)->andReturnSelf();
        JWTAuth::shouldReceive('authenticate')->once()->andReturn(true);

        $authService = new AuthService(new \App\Repositories\UserRepository(), $mockTokenRepository);

        $token = $authService->getValidTokenForUser($user);
        $this->assertEquals($existingToken, $token);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
