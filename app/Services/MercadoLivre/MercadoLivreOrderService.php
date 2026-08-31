<?php

namespace App\Services\MercadoLivre;

use App\Models\MercadoLivreConnection;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MercadoLivreOrderService
{
    public function __construct(
        protected MercadoLivreApiService $api
    ) {
    }

    /**
     * Obtém os dados financeiros completos de um pedido do Mercado Livre.
     */
    public function getOrderFinanceiro(
        MercadoLivreConnection $connection,
        string $orderId
    ): array {
        /*
        |--------------------------------------------------------------------------
        | 1. PEDIDO
        |--------------------------------------------------------------------------
        */

        $order = $this->api->get($connection, "/orders/{$orderId}");

        /*
        |--------------------------------------------------------------------------
        | 2. DESCONTOS
        |--------------------------------------------------------------------------
        */

        $discounts = $this->getDiscounts($connection, $orderId);

        /*
        |--------------------------------------------------------------------------
        | 3. PAYMENT
        |--------------------------------------------------------------------------
        */

        $paymentId = data_get($order, 'payments.0.id');

        /*
        |--------------------------------------------------------------------------
        | 4. BILLING
        |--------------------------------------------------------------------------
        */

        $billing = $this->getBilling($connection, $orderId);

        /*
        |--------------------------------------------------------------------------
        | 5. COBRANÇAS DO PAGAMENTO
        |--------------------------------------------------------------------------
        */

        $paymentCharges = $this->getPaymentCharges(
            $connection,
            $paymentId
        );

        /*
        |--------------------------------------------------------------------------
        | 6. SHIPMENT
        |--------------------------------------------------------------------------
        */

        $shippingId = data_get($order, 'shipping.id');

        $shipment = $this->getShipment(
            $connection,
            $shippingId
        );

        /*
        |--------------------------------------------------------------------------
        | 7. CUSTOS DO SHIPMENT
        |--------------------------------------------------------------------------
        */

        $shippingCosts = $this->getShippingCosts(
            $connection,
            $shippingId
        );

        /*
        |--------------------------------------------------------------------------
        | 8. VALOR DA VENDA
        |--------------------------------------------------------------------------
        */

        $valorVenda = (float) ($order['total_amount'] ?? 0);

        /*
        |--------------------------------------------------------------------------
        | 9. COMISSÃO
        |--------------------------------------------------------------------------
        */

        $comissao = $this->getComissao($billing);

        /*
        |--------------------------------------------------------------------------
        | 10. COBRANÇAS DO BILLING
        |--------------------------------------------------------------------------
        */

        $cobrancasBilling = 0;
        $detalhesBilling = [];

        foreach ($billing['results'] ?? [] as $billingItem) {
            $chargeInfo = data_get($billingItem, 'charge_info');

            if (!$chargeInfo) {
                continue;
            }

            $valor = (float) ($chargeInfo['detail_amount'] ?? 0);
            $tipo = $chargeInfo['detail_type'] ?? null;

            if ($tipo === 'CHARGE') {
                $cobrancasBilling += $valor;
            }

            $detalhesBilling[] = [
                'detail_id' => $chargeInfo['detail_id'] ?? null,
                'descricao' => $chargeInfo['transaction_detail'] ?? null,
                'valor' => round($valor, 2),
                'tipo' => $tipo,
                'subtipo' => $chargeInfo['detail_sub_type'] ?? null,
                'debitado_da_operacao' =>
                    $chargeInfo['debited_from_operation'] ?? null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 11. FRETE
        |--------------------------------------------------------------------------
        */

        $freteVendedor = $this->getSellerShippingCost(
            $shippingCosts,
            $connection
        );

        $freteShipment = $this->findShippingCost($shipment);

        $freteFinal = $freteVendedor > 0
            ? $freteVendedor
            : $freteShipment;

        /*
        |--------------------------------------------------------------------------
        | 12. DESCONTOS
        |--------------------------------------------------------------------------
        */

        $descontoTotal = $this->getDiscountTotal($discounts);

        /*
        |--------------------------------------------------------------------------
        | 13. VALOR LÍQUIDO ESTIMADO
        |--------------------------------------------------------------------------
        */

        $valorLiquido = $valorVenda
            - $comissao
            - $freteFinal
            - $descontoTotal;

        /*
        |--------------------------------------------------------------------------
        | 14. LIBERAÇÃO DO PAGAMENTO
        |--------------------------------------------------------------------------
        */

        $paymentInfo = data_get(
            $billing,
            'results.0.payment_info.0',
            []
        );

        /*
        |--------------------------------------------------------------------------
        | 15. RETORNO
        |--------------------------------------------------------------------------
        */

        return [
            'order' => $order,
            'discounts' => $discounts,
            'billing' => $billing,
            'payment_charges' => $paymentCharges,
            'shipment' => $shipment,
            'shipping_costs' => $shippingCosts,

            'financeiro' => [
                'pedido' => $order['id'] ?? null,
                'payment_id' => $paymentId,
                'shipping_id' => $shippingId,
                'pack_id' => $order['pack_id'] ?? null,

                'valor_venda' => round($valorVenda, 2),
                'comissao' => round($comissao, 2),
                'frete_vendedor' => round($freteFinal, 2),
                'descontos' => round($descontoTotal, 2),
                'cobrancas_billing' => round($cobrancasBilling, 2),
                'valor_liquido_estimado' => round($valorLiquido, 2),

                'money_release_status' =>
                    $paymentInfo['money_release_status'] ?? null,

                'money_release_date' =>
                    $paymentInfo['money_release_date'] ?? null,

                'money_release_days' =>
                    $paymentInfo['money_release_days'] ?? null,

                'status' => $order['status'] ?? null,
                'currency_id' => $order['currency_id'] ?? 'BRL',

                'detalhes_billing' => $detalhesBilling,
            ],
        ];
    }

    /**
     * Salva ou atualiza um pedido do Mercado Livre no banco.
     */
    public function salvarPedido(
        MercadoLivreConnection $connection,
        string $orderId
    ): Pedido {
        $dados = $this->getOrderFinanceiro(
            $connection,
            $orderId
        );

        $order = $dados['order'];
        $financeiro = $dados['financeiro'];
        $shipment = $dados['shipment'];

        return DB::transaction(function () use (
            $order,
            $financeiro,
            $shipment
        ) {
            $pedido = Pedido::where('origem', 'mercadolivre')
                ->where('pedido_externo', (string) $order['id'])
                ->first();

            if (!$pedido) {
                $pedido = new Pedido();

                $pedido->origem = 'mercadolivre';
                $pedido->pedido_externo = (string) $order['id'];
            }

            $valorProdutos = $this->getOrderProductsValue($order);
            $valorFrete = (float) ($financeiro['frete_vendedor'] ?? 0);
            $valorDesconto = (float) ($financeiro['descontos'] ?? 0);
            $taxasMarketplace = (float) ($financeiro['comissao'] ?? 0);

            $valorTotal = (float) ($order['total_amount'] ?? 0);

            $pedido->status = 'importado';
            $pedido->status_marketplace = $order['status'] ?? null;

            $pedido->nome_cliente = data_get(
                $order,
                'buyer.nickname'
            );

            $pedido->usuario_cliente = data_get(
                $order,
                'buyer.id'
            );

            $pedido->valor_produtos = $valorProdutos;
            $pedido->valor_frete = $valorFrete;
            $pedido->valor_desconto = $valorDesconto;
            $pedido->valor_total = $valorTotal;
            $pedido->taxas_marketplace = $taxasMarketplace;

            $pedido->valor_repasse = (float) (
                $financeiro['valor_liquido_estimado'] ?? 0
            );

            $pedido->transportadora = $this->getCarrier(
                $shipment
            );

            $pedido->codigo_rastreio = $this->getTrackingCode(
                $shipment
            );

            $pedido->endereco_entrega = $this->getShippingAddress(
                $shipment
            );

            $pedido->dados_marketplace = [
                'order' => $order,
                'financeiro' => $financeiro,
                'shipment' => $shipment,
                'importado_em' => now()->toDateTimeString(),
            ];

            $pedido->data_pedido = $order['date_created'] ?? null;

            $pedido->save();

            /*
            |--------------------------------------------------------------------------
            | ITENS
            |--------------------------------------------------------------------------
            */

            $custoTotal = 0;

            foreach ($order['order_items'] ?? [] as $item) {
                $quantidade = (int) ($item['quantity'] ?? 0);
                $precoUnitario = (float) ($item['unit_price'] ?? 0);

                $valorTotalItem = $quantidade * $precoUnitario;

                /*
                 * Por enquanto o produto do MGF ainda não está
                 * vinculado ao item do Mercado Livre.
                 */
                $custoItem = 0;
                $lucroItem = $valorTotalItem - $custoItem;

                $custoTotal += $custoItem;

                $pedidoItem = $pedido->itens()
                    ->where('marketplace_item_id', data_get($item, 'item.id'))
                    ->first();

                if (!$pedidoItem) {
                    $pedidoItem = $pedido->itens()->make();
                }

                $pedidoItem->marketplace_item_id = data_get(
                    $item,
                    'item.id'
                );

                $pedidoItem->marketplace_model_id = data_get(
                    $item,
                    'item.variation_id'
                );

                $pedidoItem->product_variation_id = null;

                $pedidoItem->nome_produto = data_get(
                    $item,
                    'item.title'
                );

                $pedidoItem->sku_marketplace = data_get(
                    $item,
                    'item.seller_sku'
                );

                $pedidoItem->variacao = $this->getItemVariation(
                    $item
                );

                $pedidoItem->quantidade = $quantidade;
                $pedidoItem->preco_unitario = $precoUnitario;
                $pedidoItem->valor_total = $valorTotalItem;

                $pedidoItem->produto_id = null;
                $pedidoItem->custo = $custoItem;
                $pedidoItem->lucro = $lucroItem;

                $pedidoItem->dados_marketplace = $item;

                $pedidoItem->save();
            }

            /*
            |--------------------------------------------------------------------------
            | CUSTO E LUCRO DO PEDIDO
            |--------------------------------------------------------------------------
            */

            $pedido->custo_total = $custoTotal;

            $pedido->lucro_bruto =
                $valorTotal
                - $taxasMarketplace
                - $valorFrete
                - $valorDesconto
                - $custoTotal;

            $pedido->save();

            return $pedido->load('itens');
        });
    }

    /**
     * Busca descontos do pedido.
     */
    protected function getDiscounts(
        MercadoLivreConnection $connection,
        string $orderId
    ): array {
        try {
            return $this->api->get(
                $connection,
                "/orders/{$orderId}/discounts"
            );
        } catch (\Throwable $e) {
            Log::info(
                '[Mercado Livre] Pedido sem descontos ou descontos indisponíveis.',
                [
                    'order_id' => $orderId,
                    'message' => $e->getMessage(),
                ]
            );

            return ['details' => []];
        }
    }

    /**
     * Busca informações de Billing.
     */
    protected function getBilling(
        MercadoLivreConnection $connection,
        string $orderId
    ): array {
        try {
            return $this->api->get(
                $connection,
                '/billing/integration/periods/key/2026-08-01/group/ML/details',
                [
                    'document_type' => 'BILL',
                    'order_ids' => $orderId,
                    'limit' => 100,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning(
                '[Mercado Livre] Erro ao consultar Billing.',
                [
                    'order_id' => $orderId,
                    'message' => $e->getMessage(),
                ]
            );

            return ['results' => []];
        }
    }

    /**
     * Busca cobranças do pagamento.
     */
    protected function getPaymentCharges(
        MercadoLivreConnection $connection,
        ?string $paymentId
    ): array {
        if (!$paymentId) {
            return ['payment_details' => []];
        }

        try {
            return $this->api->get(
                $connection,
                "/billing/integration/payment/{$paymentId}/charges",
                ['limit' => 100]
            );
        } catch (\Throwable $e) {
            Log::warning(
                '[Mercado Livre] Erro ao consultar cobranças do pagamento.',
                [
                    'payment_id' => $paymentId,
                    'message' => $e->getMessage(),
                ]
            );

            return ['payment_details' => []];
        }
    }

    /**
     * Busca o Shipment.
     */
    protected function getShipment(
        MercadoLivreConnection $connection,
        ?string $shippingId
    ): array {
        if (!$shippingId) {
            return [];
        }

        try {
            return $this->api->get(
                $connection,
                "/shipments/{$shippingId}"
            );
        } catch (\Throwable $e) {
            Log::warning(
                '[Mercado Livre] Erro ao consultar shipment.',
                [
                    'shipping_id' => $shippingId,
                    'message' => $e->getMessage(),
                ]
            );

            return [];
        }
    }

    /**
     * Busca os custos do Shipment.
     */
    protected function getShippingCosts(
        MercadoLivreConnection $connection,
        ?string $shippingId
    ): array {
        if (!$shippingId) {
            return [];
        }

        try {
            return $this->api->get(
                $connection,
                "/shipments/{$shippingId}/costs"
            );
        } catch (\Throwable $e) {
            Log::warning(
                '[Mercado Livre] Erro ao consultar custos do envio.',
                [
                    'shipping_id' => $shippingId,
                    'message' => $e->getMessage(),
                ]
            );

            return [];
        }
    }

    /**
     * Obtém a comissão do Billing.
     */
    protected function getComissao(array $billing): float
    {
        foreach ($billing['results'] ?? [] as $billingItem) {
            $saleFee = data_get(
                $billingItem,
                'sales_info.0.sale_fee.net'
            );

            if ($saleFee !== null) {
                return (float) $saleFee;
            }
        }

        return 0;
    }

    /**
     * Soma os descontos encontrados.
     */
    protected function getDiscountTotal(array $discounts): float
    {
        $total = 0;

        foreach ($discounts['details'] ?? [] as $detail) {
            foreach ($detail['items'] ?? [] as $item) {
                $total += (float) ($item['discount_amount'] ?? 0);
            }
        }

        return $total;
    }

    /**
     * Obtém o custo de envio suportado pelo vendedor.
     */
    protected function getSellerShippingCost(
        array $shippingCosts,
        MercadoLivreConnection $connection
    ): float {
        $total = 0;

        foreach ($shippingCosts['senders'] ?? [] as $sender) {
            if (
                (string) ($sender['user_id'] ?? '')
                === (string) $connection->mercadolivre_user_id
            ) {
                $total += (float) ($sender['cost'] ?? 0);
            }
        }

        return $total;
    }

    /**
     * Tenta localizar o custo de envio dentro do Shipment.
     */
    protected function findShippingCost(array $shipment): float
    {
        $possiblePaths = [
            'cost',
            'shipping_cost',
            'lead_time.cost',
            'lead_time.list_cost',
            'shipping_option.cost',
            'shipping_option.list_cost',
        ];

        foreach ($possiblePaths as $path) {
            $value = data_get($shipment, $path);

            if (is_numeric($value) && (float) $value > 0) {
                return (float) $value;
            }
        }

        return 0;
    }

    /**
     * Obtém o valor dos produtos do pedido.
     */
    protected function getOrderProductsValue(array $order): float
    {
        $total = 0;

        foreach ($order['order_items'] ?? [] as $item) {
            $total +=
                (float) ($item['unit_price'] ?? 0)
                * (int) ($item['quantity'] ?? 0);
        }

        return round($total, 2);
    }

    /**
     * Obtém transportadora do Shipment.
     */
    protected function getCarrier(array $shipment): ?string
    {
        return data_get($shipment, 'tracking_method')
            ?? data_get($shipment, 'shipping_option.name')
            ?? data_get($shipment, 'carrier');
    }

    /**
     * Obtém código de rastreamento.
     */
    protected function getTrackingCode(array $shipment): ?string
    {
        return data_get($shipment, 'tracking_number')
            ?? data_get($shipment, 'tracking_code');
    }

    /**
     * Obtém endereço de entrega.
     */
    protected function getShippingAddress(array $shipment): ?array
    {
        return data_get($shipment, 'receiver_address')
            ?? data_get($shipment, 'destination.receiver_address');
    }

    /**
     * Obtém informação da variação do item.
     */
    protected function getItemVariation(array $item): ?string
    {
        $variationAttributes = data_get(
            $item,
            'item.variation_attributes'
        );

        if (!$variationAttributes) {
            return null;
        }

        $variations = [];

        foreach ($variationAttributes as $attribute) {
            $name = $attribute['name'] ?? null;
            $value = $attribute['value_name'] ?? null;

            if ($name && $value) {
                $variations[] = "{$name}: {$value}";
            }
        }

        return $variations
            ? implode(' | ', $variations)
            : null;
    }

    /**
     * Localiza a conexão ativa.
     */
    public function getConnection(
        int $userId
    ): MercadoLivreConnection {
        $connection = MercadoLivreConnection::where(
            'user_id',
            $userId
        )
            ->where('active', true)
            ->latest()
            ->first();

        if (!$connection) {
            throw new RuntimeException(
                'Nenhuma conta do Mercado Livre conectada.'
            );
        }

        return $connection;
    }
}