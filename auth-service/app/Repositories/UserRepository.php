<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findByMobileOrCreate($mobile, $password) {
        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
            $user = User::create([
                'mobile' => $mobile,
                'password' => Hash::make($password),
            ]);
        }

        return $user;
    }

    public function validateUserPassword($user, $password) {
        return Hash::check($password, $user->password);
    }
}
