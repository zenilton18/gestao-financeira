<?php

namespace App\Services\MercadoLivre;

use App\Models\MercadoLivreConnection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MercadoLivreApiService
{
    public function __construct(
        protected MercadoLivreAuthService $authService
    ) {
    }

    /**
     * Executa uma requisição GET autenticada na API do Mercado Livre.
     */
    public function get(
        MercadoLivreConnection $connection,
        string $endpoint,
        array $query = []
    ): array {
        $accessToken = $this->authService->getValidAccessToken(
            $connection
        );

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->get(
                'https://api.mercadolibre.com' . $endpoint,
                $query
            );

        /*
         * Se o Mercado Livre informar que o token não é válido,
         * tentamos renovar uma vez e repetir a requisição.
         */
        if ($response->status() === 401) {

            Log::warning(
                '[Mercado Livre API] Token rejeitado. Tentando renovar.',
                [
                    'connection_id' => $connection->id,
                ]
            );

            $connection = $this->authService->refreshToken(
                $connection->fresh()
            );

            $response = Http::withToken(
                $connection->access_token
            )
                ->acceptJson()
                ->get(
                    'https://api.mercadolibre.com' . $endpoint,
                    $query
                );
        }

        return $this->processResponse(
            $response,
            $endpoint
        );
    }

    /**
     * Processa a resposta da API.
     */
    protected function processResponse(
        Response $response,
        string $endpoint
    ): array {
        if (!$response->successful()) {

            Log::error(
                '[Mercado Livre API] Erro na requisição.',
                [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]
            );

            echo '<pre>';
print_r($response->json());
echo '</pre>';
die();
            throw new RuntimeException(
                'Erro na API do Mercado Livre. HTTP ' .
                $response->status()
            );
        }

        return $response->json();
    }
}