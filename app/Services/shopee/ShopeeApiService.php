<?php

namespace App\Services\Shopee;

use App\Models\ShopeeConnection;
use Illuminate\Support\Facades\Http;

class ShopeeApiService
{
    protected int $partnerId;

    protected string $partnerKey;

    protected string $baseUrl;

    protected ShopeeConnection $connection;

    protected ShopeeAuthService $auth;


    public function __construct(
        ShopeeAuthService $auth
    ) {

        $this->auth = $auth;


        $this->partnerId = (int) config('shopee.partner_id');

        $this->partnerKey = config('shopee.partner_key');

        $this->baseUrl = config('shopee.base_url');


        $this->connection = ShopeeConnection::firstOrFail();


        $this->checkToken();

    }


    /**
     * Verifica se o access_token ainda é válido
     */
    private function checkToken(): void
    {

        if (
            !$this->connection->expires_at ||
            $this->connection->expires_at->isPast()
        ) {

            $this->connection =
                $this->auth->refreshAccessToken(
                    $this->connection
                );

        }

    }



    /**
     * Faz requisições GET para API Shopee
     */
    public function get(
        string $path,
        array $query = []
    ): array {


        $this->checkToken();


        $timestamp = time();


        $signString =
            $this->partnerId .
            $path .
            $timestamp .
            $this->connection->access_token .
            $this->connection->shop_id;


        $sign = hash_hmac(
            'sha256',
            $signString,
            $this->partnerKey
        );



        $params = array_merge(

            [

                'partner_id' => $this->partnerId,

                'timestamp' => $timestamp,

                'access_token' =>
                    $this->connection->access_token,

                'shop_id' =>
                    (int) $this->connection->shop_id,

                'sign' => $sign

            ],

            $query

        );



        $response = Http::get(

            $this->baseUrl . $path,

            $params

        );



        return $response->json();

    }




    /**
     * Faz requisições POST para API Shopee
     */
    public function post(
        string $path,
        array $body = [],
        array $query = []
    ): array {


        $this->checkToken();


        $timestamp = time();



        $signString =
            $this->partnerId .
            $path .
            $timestamp .
            $this->connection->access_token .
            $this->connection->shop_id;



        $sign = hash_hmac(
            'sha256',
            $signString,
            $this->partnerKey
        );



        $params = array_merge(

            [

                'partner_id' => $this->partnerId,

                'timestamp' => $timestamp,

                'access_token' =>
                    $this->connection->access_token,

                'shop_id' =>
                    (int) $this->connection->shop_id,

                'sign' => $sign

            ],

            $query

        );




        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])
        ->post(

            $this->baseUrl .
            $path .
            '?' .
            http_build_query($params),

            $body

        );



        return $response->json();

    }

}