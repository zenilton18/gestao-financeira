<?php

namespace App\Services\Shopee;


use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;




class ShopeeOrderService
{


    public function __construct(
        protected ShopeeApiService $api
    )
    {

    }



    /**
     * Sincroniza pedidos Shopee
     */
    public function syncOrders()
    {

        $orders = $this->getOrderList();
       
        $orderSns = ['2608222VQJ6XC4'];

        foreach ($orders as $order) {

            $orderSns[] = $order['order_sn'];

        }

        $escrows = [];

        foreach (array_chunk($orderSns, 50) as $chunk) {

            $response = $this->getEscrowDetailBatch($chunk);
        
            foreach ($response as $item) {

                if (!isset($item['escrow_detail'])) {
                    continue;
                }


                $detail = $item['escrow_detail'];


                $escrows[$detail['order_sn']] = $detail;

            }

        }

        foreach ($orders as $order) {

            $detail = $this->getOrderDetail(
                $order['order_sn']
            );

            if (empty($detail)) {
                continue;
            }

            $escrow = $escrows[
                $order['order_sn']
            ] ?? null;

            $this->salvarPedido(
                $detail,
                $escrow
            );

        }


    }





    /**
     * Busca lista de pedidos
     */
   private function getOrderList(): array
    {
        set_time_limit(3000);

        $orders = [];

        $cursor = '';

        $timeFrom = strtotime('-1 days midnight');
        $timeTo   = time();

        do {

            $response = $this->api->get(

                '/api/v2/order/get_order_list',

                [

                    'time_range_field' => 'create_time',

                    'time_from' => strtotime('2026-08-17 00:00:00'),

                    'time_to' => strtotime('2026-08-30 23:59:59'),

                    'page_size' => 100,

                    'cursor' => $cursor,

                    'response_optional_fields' => 'order_status'

                ]

            );


            if (
                isset($response['error']) &&
                $response['error']
            ) {

                throw new \Exception(
                    $response['message']
                    ?? 'Erro ao buscar pedidos.'
                );

            }

            foreach (
                $response['response']['order_list'] ?? []
                as $order
            ) {

                $orders[] = $order;

            }

            $cursor =
                $response['response']['next_cursor']
                ?? '';

        } while (!empty($cursor));

        return collect($orders)

            ->unique('order_sn')

            ->values()

            ->all();
    }


    /**
     * Sincroniza somente um pedido
     */
    public function syncOrder(string $orderSn)
    {
       
        Log::info('[ShopeeOrderService] Buscando detalhes do pedido', ['order_sn' => $orderSn]);

        $detail = $this->getOrderDetail($orderSn);

        if (empty($detail)) {
            Log::error('[ShopeeOrderService] Pedido não encontrado na API Shopee', ['order_sn' => $orderSn]);
            throw new \Exception("Pedido {$orderSn} não encontrado na Shopee");
        }

       
        $escrow = null;
        try {
            $escrowResponse = $this->getEscrowDetailBatch([$orderSn]);
            foreach ($escrowResponse as $item) {
                if (isset($item['escrow_detail']) && ($item['escrow_detail']['order_sn'] ?? null) === $orderSn) {
                    $escrow = $item['escrow_detail'];
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[ShopeeOrderService] Não foi possível obter dados de Escrow (financeiro)', [
                'order_sn' => $orderSn,
                'erro' => $e->getMessage()
            ]);
        }

        return $this->salvarPedido(
            $detail,
            $escrow
        );
    }

   
   
    public function getOrderDetail(string $orderSn)
    {
        $response = $this->api->get(
            '/api/v2/order/get_order_detail',
            [
                'order_sn_list' => $orderSn,

                'response_optional_fields' =>
                    'buyer_user_id,buyer_username,recipient_address,item_list,payment_info,shipping_carrier,package_list'
            ]
        );

        if (isset($response['error']) && $response['error']) {

            Log::error('[ShopeeOrderService] Erro na API get_order_detail', [
                'order_sn' => $orderSn,
                'response' => $response
            ]);

            throw new \Exception(
                $response['message']
                ?? 'Erro ao buscar detalhe do pedido na Shopee'
            );
        }

        $detail = $response['response']['order_list'][0] ?? [];

        /*
        * Log temporário para conferirmos
        * exatamente o que a Shopee está retornando.
        */
        Log::info('[ShopeeOrderService] Detalhes logísticos do pedido', [
            'order_sn' => $orderSn,
            'package_list' => $detail['package_list'] ?? [],
            'shipping_carrier' => $detail['shipping_carrier'] ?? null,
        ]);

        return $detail;
    }


    private function salvarPedido(array $detail, ?array $escrow = null)
    {
        
        $orderSn = $detail['order_sn'];
    

        $codigoRastreio = null;

        foreach ($detail['package_list'] ?? [] as $package) {

            $packageNumber = $package['package_number'] ?? null;

            if (empty($packageNumber)) {
                continue;
            }

            /*
            * A API get_order_detail fornece o package_number,
            * mas o tracking_number é obtido pela API logística.
            */
            $codigoRastreio = $this->getTrackingNumber(
                $orderSn,
                $packageNumber
            );

            if (!empty($codigoRastreio)) {
                break;
            }
        }

        Log::info(
            '[ShopeeOrderService] Código de rastreio identificado',
            [
                'order_sn' => $orderSn,
                'codigo_rastreio' => $codigoRastreio,
            ]
        );





        Log::info('[ShopeeOrderService] Iniciando gravação do pedido no banco', ['order_sn' => $orderSn]);


        /*
        * Valor pago pelo cliente
        */
        $valorTotal = 0;

        if (isset($detail['payment_info'][0]['payment_amount'])) {

            $valorTotal = $detail['payment_info'][0]['payment_amount'];

        }



        /*
        * Calcula valor dos produtos
        */
        $valorProdutos = 0;

        foreach ($detail['item_list'] ?? [] as $item) {

            $valorProdutos +=

                (
                    $item['model_discounted_price']
                    ?? 0
                )

                *

                (
                    $item['model_quantity_purchased']
                    ?? 1
                );

        }



        /*
        * Dados financeiros Shopee
        */
        $taxasMarketplace = 0;

        $valorRepasse = 0;


        if ($escrow) {

            $income = $escrow['order_income'] ?? [];


            $taxasMarketplace =

                ($income['commission_fee'] ?? 0)

                +

                ($income['service_fee'] ?? 0)

                +

                ($income['seller_transaction_fee'] ?? 0);



            $valorRepasse =
                $income['escrow_amount']
                ?? 0;

        }
        /*
        * Cria ou atualiza pedido
        */
        try {
                /*
                * Cria ou atualiza pedido
                */
            
                $pedido = Pedido::updateOrCreate(
                    ['pedido_externo' => $orderSn],
                    [
                        'origem' => 'shopee',

                        'status_marketplace' =>
                            $detail['order_status'] ?? null,

                        'usuario_cliente' =>
                            $detail['buyer_username'] ?? null,

                        'valor_total' =>
                            $valorTotal,

                        'valor_produtos' =>
                            $valorProdutos,

                        'taxas_marketplace' =>
                            $taxasMarketplace,

                        'valor_repasse' =>
                            $valorRepasse,

                        'custo_total' =>
                            0,

                        'lucro_bruto' =>
                            0,

                        'transportadora' =>
                            $detail['shipping_carrier'] ?? null,

                        /*
                        * Código de rastreio obtido da Shopee
                        */
                        'codigo_rastreio' =>
                            $codigoRastreio,

                        'endereco_entrega' =>
                            isset($detail['recipient_address'])
                                ? json_encode($detail['recipient_address'])
                                : null,

                        'data_pedido' =>
                            isset($detail['create_time'])
                                ? date('Y-m-d H:i:s', $detail['create_time'])
                                : null,

                        'dados_marketplace' =>
                            json_encode($detail),
                    ]
                );


                Log::info('[ShopeeOrderService] Pedido salvo/atualizado na tabela "pedidos"', [
                    'id' => $pedido->id,
                    'pedido_externo' => $pedido->pedido_externo
                ]);

                /*
                * Recria itens
                */
                $pedido->itens()->delete();

                $custoTotal = 0;

                foreach ($detail['item_list'] ?? [] as $item) {
                    $produto = null;
                    $variacao = null;

                    if (!empty($item['model_id'])) {
                        $variacao = ProductVariation::where('shopee_model_id', $item['model_id'])->first();
                        if ($variacao) {
                            $produto = $variacao->produto;
                        }
                    }

                    if (!$produto) {
                        $produto = Product::where('shopee_item_id', $item['item_id'] ?? 0)->first();
                    }

                    $custoUnitario = $variacao->custo ?? ($produto->preco_custo ?? 0);
                    $quantidade = $item['model_quantity_purchased'] ?? 1;
                    $custoItem = $custoUnitario * $quantidade;
                    $custoTotal += $custoItem;

                    $valorItem = ($item['model_discounted_price'] ?? 0) * $quantidade;

                    PedidoItem::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $produto?->id,
                        'product_variation_id' => $variacao?->id,
                        'marketplace_item_id' => $item['item_id'] ?? null,
                        'marketplace_model_id' => $item['model_id'] ?? null,
                        'nome_produto' => $item['item_name'] ?? '',
                        'sku_marketplace' => $item['model_sku'] ?? null,
                        'variacao' => $item['model_name'] ?? null,
                        'quantidade' => $quantidade,
                        'preco_unitario' => $item['model_discounted_price'] ?? 0,
                        'valor_total' => $valorItem,
                        'custo' => $custoItem,
                        'lucro' => $valorItem - $custoItem,
                        'dados_marketplace' => json_encode($item) // Garante conversão para JSON
                    ]);
                }

                /*
                * Atualiza financeiro final
                */
                $pedido->update([
                    'custo_total' => $custoTotal,
                    'lucro_bruto' => $valorRepasse - $custoTotal,
                ]);

                return $pedido;

            } catch (\Throwable $e) {
                Log::error('[ShopeeOrderService] ERRO ao salvar pedido no Banco de Dados', [
                    'order_sn' => $orderSn,
                    'mensagem' => $e->getMessage(),
                    'linha' => $e->getLine(),
                    'arquivo' => $e->getFile()
                ]);
                throw $e;
            }
    }

    /**
     * Busca o código de rastreio de um pacote na Shopee
     */
    private function getTrackingNumber(
        string $orderSn,
        string $packageNumber
    ): ?string {

        Log::info(
            '[ShopeeOrderService] Buscando tracking number',
            [
                'order_sn' => $orderSn,
                'package_number' => $packageNumber,
            ]
        );

        $response = $this->api->get(
            '/api/v2/logistics/get_tracking_number',
            [
                'order_sn' => $orderSn,
                'package_number' => $packageNumber,
            ]
        );

        Log::info(
            '[ShopeeOrderService] Resposta get_tracking_number',
            [
                'order_sn' => $orderSn,
                'package_number' => $packageNumber,
                'response' => $response,
            ]
        );

        if (
            isset($response['error']) &&
            $response['error']
        ) {

            Log::error(
                '[ShopeeOrderService] Erro ao buscar tracking number',
                [
                    'order_sn' => $orderSn,
                    'package_number' => $packageNumber,
                    'response' => $response,
                ]
            );

            return null;
        }

        return $response['response']['tracking_number']
            ?? null;
    }


private function getEscrowDetailBatch(array $orderSns): array
{

    $response = $this->api->post(

        '/api/v2/payment/get_escrow_detail_batch',

        [
            'order_sn_list' => array_values($orderSns)
        ]

    );


    if (
        isset($response['error']) &&
        $response['error']
    ) {

        throw new \Exception(
            $response['message']
        );

    }


    return $response['response'] ?? [];

}



}