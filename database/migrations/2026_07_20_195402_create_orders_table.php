<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            // id original Shopee
            $table->string('shopee_order_id')
                ->unique();

            $table->string('status')
                ->nullable();

            $table->timestamp('order_date')
                ->nullable();


            /*
             Valores financeiros
            */

            $table->decimal('total_amount',10,2)
                ->default(0);

            $table->decimal('shopee_commission',10,2)
                ->default(0);

            $table->decimal('shopee_fee',10,2)
                ->default(0);


            /*
              Custos internos
            */

            $table->decimal('product_cost',10,2)
                ->default(0);

            $table->decimal('profit',10,2)
                ->default(0);

            $table->decimal('margin_percent',8,2)
                ->default(0);



            // dados cliente

            $table->string('buyer_username')
                ->nullable();


            $table->json('raw_data')
                ->nullable();


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};