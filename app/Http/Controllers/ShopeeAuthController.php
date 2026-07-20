<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShopeeConnection;
use App\Services\Shopee\ShopeeAuthService;


class ShopeeAuthController extends Controller
{

    public function callback(
        Request $request,
        ShopeeAuthService $service
    )
    {

        $token = $service->getAccessToken(
            $request->code,
            $request->shop_id
        );


        if (!empty($token['error'])) {

            return response()->json([
                'error' => $token['error'],
                'message' => $token['message']
            ]);

        }


        $connection = ShopeeConnection::updateOrCreate(

            [
                'shop_id' => $request->shop_id
            ],

            [
                'access_token' => $token['access_token'],

                'refresh_token' => $token['refresh_token'],

                'expires_at' => now()
                    ->addSeconds($token['expire_in'])

            ]

        );


        return response()->json([

            'message' => 'Shopee conectada com sucesso',

            'connection' => $connection

        ]);

    }

}