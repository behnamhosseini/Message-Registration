<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Redis;

class TokenRepository
{
    public function isTokenBlacklisted($token)
    {
        return Redis::sismember('blacklist', $token);
    }
}
