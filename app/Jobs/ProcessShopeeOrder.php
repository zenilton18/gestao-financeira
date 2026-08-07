<?php

namespace App\Jobs;

use App\Services\Shopee\ShopeeOrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;


class ProcessShopeeOrder implements ShouldQueue
{
    use Queueable;


    public int $tries = 3;

    public int $timeout = 120;


    public function __construct(
        public string $orderSn
    ) {
    }



 public function handle(
    ShopeeOrderService $service
): void {

    Log::info('Processando pedido Shopee via Push', [
        'order_sn' => $this->orderSn,
    ]);


    $pedido = $service->syncOrder(
        $this->orderSn
    );


    Log::info('Pedido Shopee salvo/atualizado', [
        'order_sn' => $this->orderSn,
        'pedido_id' => $pedido->id ?? null,
    ]);

}
}