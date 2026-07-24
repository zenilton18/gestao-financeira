<?php

namespace App\Http\Controllers;


use App\Services\Shopee\ShopeeOrderService;


class ShopeeOrderController extends Controller
{


    public function __construct(
        protected ShopeeOrderService $service
    )
    {

    }



    public function index()
    {
        $orders = \App\Models\Order::with('items.product')
            ->latest()
            ->paginate(20);

        return view(
            'shopee.orders.lista',
            compact('orders')
        );
    }




    public function sync()
    {

        $this->service->syncOrders();


        return back()
            ->with(
                'success',
                'Pedidos sincronizados'
            );

    }
    public function show(\App\Models\Order $order)
    {
        $order->load('items.product');

        return view(
            'shopee.orders.show.show',
            compact('order')
        );
    }
    public function syncOne(\App\Models\Order $order)
    {

        $this->service->syncOrder(
            $order->shopee_order_id
        );


        return back()
            ->with(
                'success',
                'Pedido atualizado com sucesso'
            );

    }



}