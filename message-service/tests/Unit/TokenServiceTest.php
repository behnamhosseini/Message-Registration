<?php

namespace Tests\Unit;

use App\Repositories\TokenRepository;
use App\Services\TokenService;
use PHPUnit\Framework\TestCase;
use Tymon\JWTAuth\Claims\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Payload;

class TokenServiceTest extends TestCase
{
    public function testItValidatesTokenSuccessfully()
    {
        $mockRepo = $this->createMock(TokenRepository::class);
        $mockRepo->method('isTokenBlacklisted')->willReturn(false);
        $mockPayload= $this->createMock(Payload::class);
        $mockPayload->method('get')->willReturn(1);

        JWTAuth::shouldReceive('parseToken->check')->once()->andReturn(true);
        JWTAuth::shouldReceive('parseToken->getPayload')->once()->andReturn($mockPayload);

        $service = new TokenService($mockRepo);
        $result = $service->validateToken('valid-token');
        $this->assertEquals(['user_id' => 1, 'status' => null], $result);
    }
}
