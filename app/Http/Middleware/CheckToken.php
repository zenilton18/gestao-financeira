<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Token;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{

    public function handle(Request $request, Closure $next): Response
    {

        // Pega o token enviado no Header Authorization
        $bearer = $request->bearerToken();


        if (!$bearer) {

            return response()->json([
                'message' => 'Token não informado'
            ], 401);

        }


        // Faz o mesmo hash que salvamos no banco
        $tokenHash = hash('sha256', $bearer);



        // Procura token válido
        $token = Token::where('token', $tokenHash)
            ->where('revoked', false)
            ->first();



        if (!$token) {

            return response()->json([
                'message'=>'Token inválido'
            ],401);

        }



        // Verifica expiração
        if ($token->expires_at < now()) {


            return response()->json([
                'message'=>'Token expirado'
            ],401);


        }



        // Atualiza último acesso
        $token->update([
            'last_used_at'=>now()
        ]);



        // Continua a requisição
        return $next($request);

    }

}