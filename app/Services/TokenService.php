<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Str;


class TokenService
{

    public function create($user)
    {

        $plainToken = Str::random(80);


        Token::create([

            'user_id' => $user->id,

            'token' => hash('sha256', $plainToken),

            'device_name' => request()->header('User-Agent'),

            'ip_address' => request()->ip(),

            'expires_at' => now()->addHours(2)

        ]);


        return $plainToken;

    }

}