<?php

// app/Http/Controllers/FinanceiroController.php
namespace App\Http\Controllers;

use App\Models\Lancamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FinanceiroController extends Controller
{
    public function store(Request $request)
    {
    
   
        $validated = $request->validate([
            'data_emissao' => 'required|date',
            'observacao' => 'nullable|string|max:255',
            'valor_total' => 'required|numeric|min:0',
            'quantidade_parcelas' => 'required|integer|min:1',
            'categoria_id' => 'required|in:1,2',

            'parcelas' => 'required|array|min:1',

            'parcelas.*.vencimento' => 'required|date',
            'parcelas.*.valor' => 'required|numeric|min:0',
            'parcelas.*.situacao' => 'required|in:1,2',

            'parcelas.*.data_pagamento' => 'nullable|date',
            'parcelas.*.valor_pago' => 'nullable|numeric|min:0',
            'parcelas.*.forma_pagamento' => 'nullable|string|max:50',
        ]);

        // 2. Iniciando a transação para salvar com segurança
        DB::beginTransaction();


        try {
            // Cria o Lançamento Pai
            $lancamento = Lancamento::create([
                'data_emissao'        => $validated['data_emissao'],
                'valor'         => $validated['valor_total'],
                'descricao'         => $validated['valor_total']
            ]);
    

            // Cria cada uma das parcelas vinculadas a ele
            foreach ($validated['parcelas'] as $parcelaData) {
               $parcela =  $lancamento->parcelas()->create([
                    'vencimento'      => $parcelaData['vencimento'],
                    'valor'           => $parcelaData['valor'],
                    'situacao_id'        => $parcelaData['situacao'],
                    'data_vencimento'        => $parcelaData['vencimento']
                ]);

                if ($parcelaData['situacao'] == 2) {

                    $parcela->pagamento()->create([

                        'data_pagamento' => $parcelaData['data_pagamento'],
                        'valor_pago' => $parcelaData['valor_pago'],
                        'forma_pagamento_id' => $parcelaData['forma_pagamento'],

                    ]);

                }
            }

            // Se tudo deu certo, confirma as gravações no banco
              DB::commit();
     

            return response()->json([
                'success' => true,
                'message' => 'Lançamento e parcelas gravados com sucesso!'
            ], 201);

        } catch (\Exception $e) {
            // Se der qualquer erro no processo, desfaz tudo
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar o lançamento. Tente novamente.',
                'error' => $e->getMessage() // Opcional (remover em produção)
            ], 500);
        }
    }

    public function autorizarShopee()
    {
        // 1. Pega as credenciais limpando qualquer espaço em branco acidental
        $partnerId = (int) trim(env('SHOPEE_PARTNER_ID'));
        $partnerKey = trim(env('SHOPEE_PARTNER_KEY'));
        
        // 2. Esta URL precisa bater exatamente com o que está salvo no console da Shopee
        $redirectUrl = "https://gestao-financeira.test/"; 
        
        // 3. Configurações de rota do Sandbox
        $host = "https://partner.test-stable.shopeemobile.com";
        $path = "/api/v2/shop/auth_partner";
        $timestamp = time();
        
        // 4. Monta a string base EXATA para a assinatura
        // ATENÇÃO: Não pode ter espaços, barras invertidas ou parâmetros extras aqui
        $baseString = $partnerId . $path . $timestamp;
        
        // 5. Gera a assinatura HMAC-SHA256
        $sign = hash_hmac('sha256', $baseString, $partnerKey);
        
        // 6. Monta a URL final de redirecionamento codificando apenas o parâmetro 'redirect'
        $authUrl = $host . $path . '?' . http_build_query([
            'partner_id' => $partnerId,
            'timestamp'  => $timestamp,
            'sign'       => $sign,
            'redirect'   => $redirectUrl
        ]);
        
        return redirect()->away($authUrl);
    }
 
    public function callback(Request $request)
    {
        $partnerId = (int) env('SHOPEE_PARTNER_ID');
        $partnerKey = env('SHOPEE_PARTNER_KEY');

        $code = $request->code;
        $shopId = (int) $request->shop_id;

        $timestamp = time();
        $path = "/api/v2/auth/token/get";

        // Monta a string para assinatura (Continua exatamente igual)
        $baseString = $partnerId . $path . $timestamp;

        // Gera assinatura
        $sign = hash_hmac('sha256', $baseString, $partnerKey);

        // NOVA URL DA SHOPEE SANDBOX V2 GLOBAL (Necessária para SG)
        $url = "https://openplatform.sandbox.test-stable.shopee.sg{$path}";

        // Envia para Shopee
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url . '?' . http_build_query([
            'partner_id' => $partnerId,
            'timestamp'  => $timestamp,
            'sign'       => $sign,
        ]), [
            'code'       => $code,
            'shop_id'    => $shopId,
            'partner_id' => $partnerId,
        ]);

        dd([
            'enviado' => [
                'code' => $code,
                'shop_id' => $shopId,
            ],
            'resposta_shopee' => $response->json()
        ]);
    }

    public function testarBuscaPedidos()
    {
        $partnerId = (int) env('SHOPEE_PARTNER_ID');
        $partnerKey = env('SHOPEE_PARTNER_KEY');
        
        $accessToken = "4b4a73486e6864467441517443545a52"; 
        $shopId = (int) "227749103"; 

        $timestamp = time();
        
        // USANDO O MÓDULO DE PEDIDOS (Garantido no Sandbox)
        $path = "/api/v2/order/get_order_detail";

        $baseString = $partnerId . $path . $timestamp . $accessToken . $shopId;
        $sign = hash_hmac('sha256', $baseString, $partnerKey);

        $url = "https://openplatform.sandbox.test-stable.shopee.sg{$path}";

        $queryParams = [
            'partner_id'   => $partnerId,
            'timestamp'    => $timestamp,
            'sign'         => $sign,
            'access_token' => $accessToken,
            'shop_id'      => $shopId,
            // Coloque aqui a lista de pedidos separada por vírgula (ex: '240715ABC123,240715XYZ456')
            'order_sn_list' => '260714M01H1MF1',
            // ATENÇÃO AQUI: Pedimos explicitamente os campos de valores do pedido
            'response_optional_fields' => 'total_amount,escrow_tax_amount,buyer_total_amount'
        ];

        $response = Http::get($url, $queryParams);

        dd([
            'status' => $response->status(),
            'dados'  => $response->json()
        ]);
    }
}

