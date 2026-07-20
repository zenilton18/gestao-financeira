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

    
     $this->service->syncOrders();
        $orders = \App\Models\Order::
        with('items.product')
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



}