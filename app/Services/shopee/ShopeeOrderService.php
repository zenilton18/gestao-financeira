<?php

namespace App\Services\Shopee;


use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


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


        foreach($orders as $order)
        {

            $detail = $this->getOrderDetail(
                $order['order_sn']
            );


            if(empty($detail)){
                continue;
            }


            $this->saveOrder($detail);

        }


    }





    /**
     * Busca lista de pedidos
     */
    private function getOrderList()
    {


        $response = $this->api->get(

            '/api/v2/order/get_order_list',

            [

                'time_range_field'=>'create_time',

                'time_from'=>now()
                    ->subDays(15)
                    ->timestamp,


                'time_to'=>now()
                    ->timestamp,


                'page_size'=>50,


                'cursor'=>''

            ]

        );



        if(isset($response['error']) 
            && $response['error']) {

            throw new \Exception(
                $response['message']
            );

        }



        return 
            $response['response']['order_list']
            ?? [];

    }





    /**
     * Busca detalhes do pedido
     */
    private function getOrderDetail(string $orderSn)
    {


        $response = $this->api->get(

            '/api/v2/order/get_order_detail',

            [

                'order_sn_list'=>$orderSn,


                'response_optional_fields'=>
                'buyer_user_id,buyer_username,item_list,payment_info,shipping_info'

            ]

        );



        if(isset($response['error']) 
            && $response['error']) {

            throw new \Exception(
                $response['message']
            );

        }



        return
            $response['response']['order_list'][0]
            ?? [];

    }





 
    private function saveOrder(array $data)
    {
        DB::transaction(function () use ($data) {

            $order = Order::updateOrCreate(

                [
                    'shopee_order_id' => $data['order_sn']
                ],

                [
                    'status' => $data['order_status'] ?? null,

                    'buyer_username' => $data['buyer_username'] ?? null,

                    'order_date' => isset($data['create_time'])
                        ? date(
                            'Y-m-d H:i:s',
                            $data['create_time']
                        )
                        : null,

                    'raw_data' => $data
                ]

            );

            // Totais do pedido
            $totalAmount = 0;
            $productCost = 0;

            foreach ($data['item_list'] ?? [] as $item) {

                /*
                    Procura produto interno
                    pelo ID Shopee
                */
                $product = Product::where(
                    'shopee_item_id',
                    $item['item_id']
                )->first();

            
                /*
                    Caso não tenha produto vinculado
                */
                $cost = $product?->preco_custo ?? 0;

                $quantity = $item['model_quantity_purchased'] ?? 1;

                $price = $item['model_discounted_price'] ?? 0;

                // Totais do item
                $itemTotal = $price * $quantity;
                $itemCost = $cost * $quantity;
                $itemProfit = $itemTotal - $itemCost;

                // Acumula totais do pedido
                $totalAmount += $itemTotal;
                $productCost += $itemCost;

                OrderItem::updateOrCreate(

                    [

                        'order_id' => $order->id,

                        'shopee_item_id' => $item['item_id']

                    ],

                    [

                        'product_id' => $product?->id,

                        'product_name' => $item['item_name'],

                        'quantity' => $quantity,

                        'price' => $price,

                        'cost' => $cost,

                        'profit' => $itemProfit,

                        'raw_data' => $item

                    ]

                );

            }

            // Calcula totais do pedido
            $profit = $totalAmount - $productCost;

            $margin = $totalAmount > 0
                ? round(($profit / $totalAmount) * 100, 2)
                : 0;

                

            // Atualiza os valores financeiros do pedido
            $order->update([

                'total_amount' => $totalAmount,

                'product_cost' => $productCost,

                'profit' => $profit,

                'margin_percent' => $margin,

                // Por enquanto ficam zerados.
                // Posteriormente serão preenchidos com os valores retornados pela Shopee.
                'shopee_commission' => 0,

                'shopee_fee' => 0,

            ]);

        });
    }



}