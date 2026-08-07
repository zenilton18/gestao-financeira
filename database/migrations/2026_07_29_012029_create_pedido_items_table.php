<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('pedido_items', function (Blueprint $table) {


            $table->id();


            /*
            Ligação com pedido
            */

            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->cascadeOnDelete();



            /*
            Dados vindos da Shopee
            */

            $table->string('marketplace_item_id')
                ->nullable();


            $table->string('marketplace_model_id')
                ->nullable();



            $table->string('nome_produto');


            $table->string('sku_marketplace')
                ->nullable();



            $table->string('variacao')
                ->nullable();



            /*
            Quantidade e valores
            */

            $table->integer('quantidade')
                ->default(1);



            $table->decimal(
                'preco_unitario',
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
            Futuro vínculo estoque
            */

            $table->foreignId('produto_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();



            /*
            Custos e lucro
            */

            $table->decimal(
                'custo',
                10,
                2
            )
            ->default(0);



            $table->decimal(
                'lucro',
                10,
                2
            )
            ->default(0);



            /*
            Guarda resposta original Shopee
            */

            $table->json('dados_marketplace')
                ->nullable();



            $table->timestamps();

        });

    }



    public function down(): void
    {

        Schema::dropIfExists('pedido_items');

    }

};