<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Redis;


class TokenRepository
{
    public function addToBlacklist($token)
    {
        Redis::sadd('blacklist', $token);
    }

    public function storeToken($userId, $token)
    {
        Redis::setex('user_token:' . $userId, 60 * 10, $token);
    }

    public function getToken($userId)
    {
        return Redis::get('user_token:' . $userId);
    }
}
