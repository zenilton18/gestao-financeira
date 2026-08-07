<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessShopeeOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\ShopeeWebhook;

class ShopeeWebhookController extends Controller
{
    /**
     * Recebe os eventos Push enviados pela Shopee.
     */
 public function handle(Request $request): JsonResponse
    {
        Log::info('Shopee Webhook recebido', [
            'body' => $request->all(),
        ]);

        $orderSn = $request->input('data.ordersn');

        if ($orderSn) {

            ProcessShopeeOrder::dispatch(
                $orderSn
            );

            Log::info('Pedido enviado para fila', [
                'order_sn' => $orderSn
            ]);
            $data = $request->input('data', []);


ShopeeWebhook::updateOrCreate(

    [
        'msg_id' => $request->input('msg_id'),
    ],

    [

        'order_sn' => $data['ordersn'] ?? null,

        'shop_id' => $request->input('shop_id'),

        'status' => $data['status'] ?? null,

        'code' => $request->input('code'),

        'payload' => $request->all(),

    ]

);
        }


    return response()->json([
        'code' => 0,
        'message' => 'ok'
    ], 200);
    }
}

