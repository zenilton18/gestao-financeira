<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('order_items', function(Blueprint $table){

            $table->id();


            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();


            $table->string('shopee_item_id')
                ->nullable();


            $table->string('product_name');


            $table->integer('quantity')
                ->default(1);



            $table->decimal('price',10,2)
                ->default(0);


            /*
             ligação com estoque
            */

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();



            $table->decimal('cost',10,2)
                ->default(0);


            $table->decimal('profit',10,2)
                ->default(0);



            $table->json('raw_data')
                ->nullable();


            $table->timestamps();

        });

    }



    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }

};