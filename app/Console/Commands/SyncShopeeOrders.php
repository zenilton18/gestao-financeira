<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Services\Shopee\ShopeeOrderService;



class SyncShopeeOrders extends Command
{

    /**
     * Nome do comando no terminal
     */
    protected $signature = 'shopee:sync-orders';



    /**
     * Descrição do comando
     */
    protected $description = 'Sincroniza todos os pedidos da Shopee';



    /**
     * Executa o comando
     */
    public function handle(
        ShopeeOrderService $service
    )
    {

        $this->info(
            'Iniciando sincronização dos pedidos Shopee...'
        );



        try {


            $total = $service->syncOrders();



            $this->newLine();


            $this->info(
                "Pedidos sincronizados: {$total}"
            );



            return Command::SUCCESS;



        } catch(\Exception $e) {


            $this->error(
                'Erro na sincronização: '
                .
                $e->getMessage()
            );



            return Command::FAILURE;

        }

    }

}