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
    $query = \App\Models\Order::with('items.product');


    /*
    |--------------------------------------------------------------------------
    | Busca
    |--------------------------------------------------------------------------
    */

    if (request()->filled('search')) {

        $search = request('search');

        $query->where(function ($q) use ($search) {

            $q->where(
                'shopee_order_id',
                'like',
                "%{$search}%"
            )
            ->orWhere(
                'buyer_username',
                'like',
                "%{$search}%"
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    if (request()->filled('status')) {

        $query->where(
            'status',
            request('status')
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Período
    |--------------------------------------------------------------------------
    */

    if (request()->filled('periodo')) {

        match (request('periodo')) {

            'hoje' => $query->whereDate(
                'order_date',
                today()
            ),

            'mes' => $query->whereMonth(
                'order_date',
                now()->month
            ),

            '30' => $query->where(
                'order_date',
                '>=',
                now()->subDays(30)
            ),

            default => null

        };

    }


    /*
    |--------------------------------------------------------------------------
    | Cards do Dashboard
    |--------------------------------------------------------------------------
    */

    $statsQuery = clone $query;


    $stats = [
        'total' => $statsQuery->count(),

        'faturamento' => $statsQuery->sum(
            'total_amount'
        ),

        'lucro' => $statsQuery->sum(
            'profit'
        ),

        'ticket_medio' => $statsQuery->avg(
            'total_amount'
        ),
    ];


    /*
    |--------------------------------------------------------------------------
    | Lista de pedidos
    |--------------------------------------------------------------------------
    */

    $orders = $query
        ->latest()
        ->paginate(20)
        ->withQueryString();



    return view(
        'shopee.orders.lista',
        compact(
            'orders',
            'stats'
        )
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