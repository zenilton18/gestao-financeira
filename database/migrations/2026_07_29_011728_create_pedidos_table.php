<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('pedidos', function (Blueprint $table) {


            $table->id();


            /*
            Origem do pedido
            shopee
            mercado_livre
            manual
            */

            $table->string('origem')
                ->default('manual');


            /*
            ID externo do marketplace
            Ex:
            Shopee order_sn
            */

            $table->string('pedido_externo')
                ->nullable()
                ->unique();



            /*
            Status interno
            */

            $table->string('status')
                ->default('novo');



            /*
            Status vindo da Shopee
            READY_TO_SHIP
            COMPLETED
            */

            $table->string('status_marketplace')
                ->nullable();



            /*
            Dados comprador
            */

            $table->string('nome_cliente')
                ->nullable();


            $table->string('usuario_cliente')
                ->nullable();



            /*
            Valores
            */


            $table->decimal(
                'valor_produtos',
                10,
                2
            )
            ->default(0);



            $table->decimal(
                'valor_frete',
                10,
                2
            )
            ->default(0);



            $table->decimal(
                'valor_desconto',
                10,
                2
            )
            ->default(0);



            $table->decimal(
                'valor_total',
                10,
                2
            )
            ->default(0);



            /*
            Financeiro futuro
            */

            $table->decimal(
                'taxas_marketplace',
                10,
                2
            )
            ->default(0);



            $table->decimal(
                'valor_repasse',
                10,
                2
            )
            ->default(0);



            /*
            Envio
            */

            $table->string('transportadora')
                ->nullable();


            $table->string('codigo_rastreio')
                ->nullable();



            /*
            Endereço completo Shopee
            */

            $table->json('endereco_entrega')
                ->nullable();



            /*
            Guardar retorno completo API
            */

            $table->json('dados_marketplace')
                ->nullable();



            $table->timestamp('data_pedido')
                ->nullable();



            $table->timestamps();

        });

    }



    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }

};