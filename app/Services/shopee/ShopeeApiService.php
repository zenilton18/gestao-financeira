<?php

namespace App\Services\Shopee;

use App\Models\ShopeeConnection;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;



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




   public function get(
    string $path,
    array $query = []
): array {

    Log::info('ENTROU NO GET SHOPEE', [
        'path' => $path,
        'query' => $query
    ]);


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

            'partner_id' =>
                $this->partnerId,

            'timestamp' =>
                $timestamp,

            'access_token' =>
                $this->connection->access_token,

            'shop_id' =>
                (int) $this->connection->shop_id,

            'sign' =>
                $sign

        ],

        $query

    );


    $client = new Client([

        'verify' => false,

        'curl' => [

            CURLOPT_SSL_VERIFYPEER => false,

            CURLOPT_SSL_VERIFYHOST => false,

        ],

        'connect_timeout' => 30,

        'timeout' => 400,

    ]);


    try {

        $response = $client->get(

            $this->baseUrl . $path,

            [

                'query' => $params,

            ]

        );


        $body =
            $response
                ->getBody()
                ->getContents();


        $data =
            json_decode(
                $body,
                true
            );


        if (!is_array($data)) {

            throw new \Exception(
                'Resposta inválida da API Shopee.'
            );

        }


        return $data;


    } catch (\GuzzleHttp\Exception\ClientException $e) {

        $statusCode =
            $e->getResponse()
                ?->getStatusCode();


        $responseBody = null;


        if ($e->getResponse()) {

            $responseBody =
                json_decode(
                    $e->getResponse()
                        ->getBody()
                        ->getContents(),
                    true
                );

        }


        /*
        |--------------------------------------------------------------------------
        | RATE LIMIT SHOPEE
        |--------------------------------------------------------------------------
        */

        if ($statusCode === 429) {

            Log::warning(
                '[ShopeeApiService] Rate limit atingido',
                [

                    'path' =>
                        $path,

                    'query' =>
                        $query,

                    'response' =>
                        $responseBody,

                ]
            );


            throw new \Exception(
                'A Shopee limitou temporariamente as consultas da API. ' .
                'Aguarde alguns minutos e tente novamente.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | OUTROS ERROS HTTP
        |--------------------------------------------------------------------------
        */

        Log::error(
            '[ShopeeApiService] Erro HTTP',
            [

                'status' =>
                    $statusCode,

                'path' =>
                    $path,

                'query' =>
                    $query,

                'response' =>
                    $responseBody,

            ]
        );


        throw $e;

    }

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




        $client = new Client([

            'verify' => false,

            'curl' => [

                CURLOPT_SSL_VERIFYPEER => false,

                CURLOPT_SSL_VERIFYHOST => false,

            ],

            'connect_timeout' => 30,

            'timeout' => 180,

        ]);




        $response = $client->post(

            $this->baseUrl .
            $path .
            '?' .
            http_build_query($params),

            [

                'headers' => [

                    'Content-Type' => 'application/json'

                ],

                'json' => $body

            ]

        );



        return json_decode(

            $response->getBody()->getContents(),

            true

        );

    }

    /**
     * Faz requisição POST para a API Shopee e retorna o conteúdo bruto da resposta (PDF / Binary)
     */
    public function postRaw(
        string $path,
        array $body = [],
        array $query = []
    ): string {
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
                'partner_id'   => $this->partnerId,
                'timestamp'    => $timestamp,
                'access_token' => $this->connection->access_token,
                'shop_id'      => (int) $this->connection->shop_id,
                'sign'         => $sign
            ],
            $query
        );

        $client = new Client([
            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
            'connect_timeout' => 30,
            'timeout' => 180,
        ]);

        $response = $client->post(
            $this->baseUrl .
            $path .
            '?' .
            http_build_query($params),
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $body
            ]
        );

        return $response->getBody()->getContents();
    }

}