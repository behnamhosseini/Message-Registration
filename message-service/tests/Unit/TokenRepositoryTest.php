<?php

namespace Tests\Unit;

use App\Repositories\TokenRepository;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\TestCase;

class TokenRepositoryTest extends TestCase
{
    public function testItValidatesTokenSuccessfully()
    {
        Redis::shouldReceive('sismember')
            ->once()
            ->with('blacklist', 'some-token')
            ->andReturn(true);

        $repository = new TokenRepository();
        $this->assertTrue($repository->isTokenBlacklisted('some-token'));
    }
}
