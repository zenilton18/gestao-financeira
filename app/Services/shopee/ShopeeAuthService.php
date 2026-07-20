<?php

namespace App\Services\Shopee;

use App\Models\ShopeeConnection;
use Illuminate\Support\Facades\Http;
use Exception;

class ShopeeAuthService
{
    protected int $partnerId;

    protected string $partnerKey;

    protected string $baseUrl;


    public function __construct()
    {
        $this->partnerId = (int) config('shopee.partner_id');

        $this->partnerKey = config('shopee.partner_key');

        $this->baseUrl = config('shopee.base_url');
    }


    /**
     * Troca o code recebido da Shopee por access_token
     */
    public function getAccessToken(
        string $code,
        string $shopId
    ): array {

        $path = "/api/v2/auth/token/get";

        $timestamp = time();


        $signString =
            $this->partnerId .
            $path .
            $timestamp;


        $sign = hash_hmac(
            'sha256',
            $signString,
            $this->partnerKey
        );


        $url = $this->baseUrl . $path;


        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])
        ->post($url . '?' . http_build_query([

            'partner_id' => $this->partnerId,

            'timestamp' => $timestamp,

            'sign' => $sign

        ]), [

            'code' => $code,

            'shop_id' => (int) $shopId,

            'partner_id' => $this->partnerId

        ]);


        $data = $response->json();


        if (!empty($data['error'])) {

            throw new Exception(
                $data['message']
            );

        }


        return $data;
    }



    /**
     * Renova access_token usando refresh_token
     */
    public function refreshAccessToken(
        ShopeeConnection $connection
    ): ShopeeConnection {


        $path = "/api/v2/auth/access_token/get";


        $timestamp = time();


        $signString =
            $this->partnerId .
            $path .
            $timestamp;


        $sign = hash_hmac(
            'sha256',
            $signString,
            $this->partnerKey
        );


        $url = $this->baseUrl . $path;


        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])
        ->post($url . '?' . http_build_query([

            'partner_id' => $this->partnerId,

            'timestamp' => $timestamp,

            'sign' => $sign

        ]), [

            'refresh_token' => $connection->refresh_token,

            'shop_id' => (int) $connection->shop_id,

            'partner_id' => $this->partnerId

        ]);


        $data = $response->json();



        if (!empty($data['error'])) {

            throw new Exception(
                $data['message']
            );

        }



        $connection->update([

            'access_token' => $data['access_token'],

            'refresh_token' => $data['refresh_token'],

            'expires_at' => now()
                ->addSeconds($data['expire_in'])

        ]);



        return $connection->fresh();

    }
}