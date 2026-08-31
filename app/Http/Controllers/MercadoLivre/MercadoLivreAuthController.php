<?php

namespace App\Http\Controllers\MercadoLivre;

use App\Http\Controllers\Controller;
use App\Models\MercadoLivreConnection;
use App\Services\MercadoLivre\MercadoLivreAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoLivreAuthController extends Controller
{
    /**
     * Inicia o processo de autenticação OAuth
     * com o Mercado Livre.
     */
    public function redirect(Request $request)
    {
        $codeVerifier = rtrim(
            strtr(
                base64_encode(random_bytes(64)),
                '+/',
                '-_'
            ),
            '='
        );

        $codeChallenge = rtrim(
            strtr(
                base64_encode(
                    hash('sha256', $codeVerifier, true)
                ),
                '+/',
                '-_'
            ),
            '='
        );

        $state = bin2hex(random_bytes(32));

        /*
         * Salva os dados necessários na sessão.
         */
        $request->session()->put(
            'mercadolivre_oauth_state',
            $state
        );

        $request->session()->put(
            'mercadolivre_code_verifier',
            $codeVerifier
        );

        /*
         * Como o sistema é autenticado,
         * também guardamos o usuário que iniciou a conexão.
         */
        $request->session()->put(
            'mercadolivre_user_id',
            $request->user()->id
        );

        $query = http_build_query([
            'response_type' => 'code',

            'client_id' => config(
                'services.mercadolivre.client_id'
            ),

            'redirect_uri' => config(
                'services.mercadolivre.redirect_uri'
            ),

            'state' => $state,

            'code_challenge' => $codeChallenge,

            'code_challenge_method' => 'S256',

            /*
             * Solicita novamente o consentimento do usuário.
             *
             * Isso é importante porque alteramos as permissões
             * da aplicação no DevCenter.
             */
            'prompt' => 'consent',
        ]);

        $url =
            'https://auth.mercadolivre.com.br/authorization?' .
            $query;

        Log::info(
            '[Mercado Livre OAuth] Iniciando autorização.',
            [
                'mgf_user_id' =>
                    $request->user()->id,

                'redirect_uri' =>
                    config(
                        'services.mercadolivre.redirect_uri'
                    ),
            ]
        );

        return redirect()->away($url);
    }

    /**
     * Callback do Mercado Livre.
     *
     * Troca o código OAuth por tokens,
     * consulta a conta e salva no banco.
     */
    public function callback(
        Request $request,
        MercadoLivreAuthService $mercadoLivreAuthService
    ) {
        /*
         * Verifica se houve erro no OAuth.
         */
        if ($request->has('error')) {

            Log::error(
                '[Mercado Livre OAuth] Erro na autorização.',
                [
                    'error' =>
                        $request->get('error'),

                    'description' =>
                        $request->get('error_description'),
                ]
            );

            return redirect('/')
                ->with(
                    'error',
                    'A autorização do Mercado Livre foi cancelada.'
                );
        }

        /*
         * =========================================================
         * STATE
         * =========================================================
         */

        $state = $request->get('state');

        $sessionState = $request->session()->pull(
            'mercadolivre_oauth_state'
        );

        if (
            !$state ||
            !$sessionState ||
            !hash_equals(
                $sessionState,
                $state
            )
        ) {

            Log::warning(
                '[Mercado Livre OAuth] State inválido.'
            );

            abort(
                403,
                'State OAuth inválido.'
            );
        }

        /*
         * =========================================================
         * CODE
         * =========================================================
         */

        $code = $request->get('code');

        if (!$code) {

            Log::error(
                '[Mercado Livre OAuth] Código não recebido.'
            );

            return redirect('/')
                ->with(
                    'error',
                    'Código de autorização não recebido.'
                );
        }

        /*
         * =========================================================
         * PKCE
         * =========================================================
         */

        $codeVerifier = $request->session()->pull(
            'mercadolivre_code_verifier'
        );

        if (!$codeVerifier) {

            Log::error(
                '[Mercado Livre OAuth] Code verifier não encontrado.'
            );

            return redirect('/')
                ->with(
                    'error',
                    'Sessão OAuth inválida. Tente novamente.'
                );
        }

        /*
         * =========================================================
         * USUÁRIO DO MGF
         * =========================================================
         */

        $mgfUserId = $request->session()->pull(
            'mercadolivre_user_id'
        );

        /*
         * Como o callback está protegido por auth,
         * também temos o usuário diretamente.
         */
        $mgfUser = $request->user();

        if (!$mgfUser) {

            Log::error(
                '[Mercado Livre OAuth] Usuário MGF não encontrado.'
            );

            return redirect('/')
                ->with(
                    'error',
                    'Usuário do sistema não encontrado.'
                );
        }

        /*
         * Preferimos o ID salvo na sessão.
         */
        $mgfUserId = $mgfUserId ?: $mgfUser->id;

        try {

            /*
             * =====================================================
             * 1. TROCA CODE POR TOKEN
             * =====================================================
             */

            $tokenData =
                $mercadoLivreAuthService
                    ->exchangeCodeForToken(
                        $code,
                        $codeVerifier
                    );

            if (
                empty(
                    $tokenData['access_token']
                )
            ) {

                throw new \RuntimeException(
                    'Access token não retornado pelo Mercado Livre.'
                );
            }

            /*
             * =====================================================
             * 2. CONSULTA USUÁRIO MERCADO LIVRE
             * =====================================================
             */

            $mercadoLivreUser =
                $mercadoLivreAuthService
                    ->getUser(
                        $tokenData['access_token']
                    );

            if (
                empty(
                    $mercadoLivreUser['id']
                )
            ) {

                throw new \RuntimeException(
                    'Não foi possível identificar a conta do Mercado Livre.'
                );
            }

            /*
             * =====================================================
             * 3. CALCULA EXPIRAÇÃO
             * =====================================================
             */

            $expiresAt = null;

            if (
                !empty(
                    $tokenData['expires_in']
                )
            ) {

                $expiresAt =
                    now()->addSeconds(
                        (int) $tokenData['expires_in']
                    );
            }

            /*
             * =====================================================
             * 4. SALVA NO BANCO
             * =====================================================
             */

            $connection =
                MercadoLivreConnection::updateOrCreate(
                    [
                        'mercadolivre_user_id' =>
                            (string)
                            $mercadoLivreUser['id'],
                    ],
                    [
                        'user_id' =>
                            $mgfUserId,

                        'nickname' =>
                            $mercadoLivreUser['nickname']
                            ?? null,

                        'access_token' =>
                            $tokenData['access_token'],

                        'refresh_token' =>
                            $tokenData['refresh_token']
                            ?? null,

                        'expires_at' =>
                            $expiresAt,

                        'token_type' =>
                            $tokenData['token_type']
                            ?? null,

                        'scope' =>
                            $tokenData['scope']
                            ?? null,

                        'active' =>
                            true,
                    ]
                );

            /*
             * =====================================================
             * 5. LOG
             * =====================================================
             */

            Log::info(
                '[Mercado Livre OAuth] Conta conectada.',
                [
                    'connection_id' =>
                        $connection->id,

                    'mgf_user_id' =>
                        $mgfUserId,

                    'mercadolivre_user_id' =>
                        $mercadoLivreUser['id'],

                    'nickname' =>
                        $mercadoLivreUser['nickname']
                        ?? null,

                    'expires_at' =>
                        $expiresAt?->toDateTimeString(),

                    'scope' =>
                        $tokenData['scope']
                        ?? null,
                ]
            );

            /*
             * =====================================================
             * 6. REDIRECIONA
             * =====================================================
             */

            return redirect()
                ->route('shopee.dashboard')
                ->with(
                    'success',
                    'Mercado Livre conectado com sucesso!'
                );

        } catch (\Throwable $e) {

            Log::error(
                '[Mercado Livre OAuth] Erro ao conectar.',
                [
                    'mgf_user_id' =>
                        $mgfUserId,

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            return redirect('/')
                ->with(
                    'error',
                    'Erro ao conectar o Mercado Livre: ' .
                    $e->getMessage()
                );
        }
    }
}