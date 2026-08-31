<?php

namespace App\Services\MercadoLivre;

use App\Models\MercadoLivreConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MercadoLivreAuthService
{
    /**
     * Troca o código de autorização por tokens.
     */
    public function exchangeCodeForToken(
        string $code,
        string $codeVerifier
    ): array {
        $response = Http::asForm()
            ->post(
                'https://api.mercadolibre.com/oauth/token',
                [
                    'grant_type' => 'authorization_code',

                    'client_id' => config(
                        'services.mercadolivre.client_id'
                    ),

                    'client_secret' => config(
                        'services.mercadolivre.client_secret'
                    ),

                    'code' => $code,

                    'redirect_uri' => config(
                        'services.mercadolivre.redirect_uri'
                    ),

                    'code_verifier' => $codeVerifier,
                ]
            );

        if (!$response->successful()) {

            Log::error(
                '[Mercado Livre OAuth] Erro ao obter token.',
                [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]
            );

            throw new RuntimeException(
                'Não foi possível obter o token do Mercado Livre.'
            );
        }

        return $response->json();
    }

    /**
     * Consulta os dados do usuário autenticado.
     */
    public function getUser(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get(
                'https://api.mercadolibre.com/users/me'
            );

        if (!$response->successful()) {

            Log::error(
                '[Mercado Livre API] Erro ao consultar usuário.',
                [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]
            );

            throw new RuntimeException(
                'Não foi possível consultar o usuário do Mercado Livre.'
            );
        }

        return $response->json();
    }

    /**
     * Renova o access token usando o refresh token.
     *
     * O Mercado Livre pode retornar um novo refresh_token.
     * Por isso, sempre atualizamos os dois tokens no banco.
     */
    public function refreshToken(
        MercadoLivreConnection $connection
    ): MercadoLivreConnection {
        if (empty($connection->refresh_token)) {

            throw new RuntimeException(
                'A conexão do Mercado Livre não possui refresh token.'
            );
        }

        $response = Http::asForm()
            ->post(
                'https://api.mercadolibre.com/oauth/token',
                [
                    'grant_type' => 'refresh_token',

                    'client_id' => config(
                        'services.mercadolivre.client_id'
                    ),

                    'client_secret' => config(
                        'services.mercadolivre.client_secret'
                    ),

                    'refresh_token' =>
                        $connection->refresh_token,
                ]
            );

        if (!$response->successful()) {

            Log::error(
                '[Mercado Livre OAuth] Erro ao renovar token.',
                [
                    'connection_id' =>
                        $connection->id,

                    'status' =>
                        $response->status(),

                    'response' =>
                        $response->json(),
                ]
            );

            /*
             * Se a renovação falhou, não apagamos os tokens.
             *
             * Isso é importante para podermos diagnosticar
             * posteriormente o motivo da falha.
             */
            throw new RuntimeException(
                'Não foi possível renovar o token do Mercado Livre.'
            );
        }

        $tokenData = $response->json();

        /*
         * Valida o novo access token.
         */
        if (empty($tokenData['access_token'])) {

            throw new RuntimeException(
                'O Mercado Livre não retornou um novo access token.'
            );
        }

        /*
         * Calcula a nova data de expiração.
         */
        $expiresAt = null;

        if (!empty($tokenData['expires_in'])) {

            $expiresAt = now()->addSeconds(
                (int) $tokenData['expires_in']
            );
        }

        /*
         * Atualiza a conexão.
         *
         * IMPORTANTE:
         * Se o Mercado Livre não retornar outro refresh_token,
         * mantemos o refresh_token atual.
         */
        $connection->access_token =
            $tokenData['access_token'];

        $connection->refresh_token =
            $tokenData['refresh_token']
            ?? $connection->refresh_token;

        $connection->expires_at =
            $expiresAt;

        $connection->token_type =
            $tokenData['token_type']
            ?? $connection->token_type;

        $connection->scope =
            $tokenData['scope']
            ?? $connection->scope;

        $connection->active = true;

        $connection->save();

        Log::info(
            '[Mercado Livre OAuth] Token renovado com sucesso.',
            [
                'connection_id' =>
                    $connection->id,

                'mercadolivre_user_id' =>
                    $connection->mercadolivre_user_id,

                'expires_at' =>
                    $connection->expires_at?->toDateTimeString(),
            ]
        );

        return $connection->fresh();
    }

    /**
     * Retorna um access token válido.
     *
     * Se o token estiver próximo de expirar,
     * renova automaticamente.
     */
    public function getValidAccessToken(
        MercadoLivreConnection $connection
    ): string {
        /*
         * Se o token ainda é válido, usamos o atual.
         */
        if (!$connection->tokenExpired()) {

            return $connection->access_token;
        }

        /*
         * Token expirado ou próximo de expirar.
         * Fazemos a renovação.
         */
        $connection = $this->refreshToken(
            $connection
        );

        return $connection->access_token;
    }
}