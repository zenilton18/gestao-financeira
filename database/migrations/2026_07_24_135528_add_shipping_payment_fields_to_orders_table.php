<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('orders', function (Blueprint $table) {


            $table->string('payment_method')
                ->nullable();


            $table->decimal('shipping_fee',10,2)
                ->default(0);


            $table->decimal('discount_amount',10,2)
                ->default(0);


            $table->string('tracking_number')
                ->nullable();


            $table->string('shipping_carrier')
                ->nullable();


            $table->json('shipping_address')
                ->nullable();


        });

    }



    public function down(): void
    {

        Schema::table('orders', function (Blueprint $table) {


            $table->dropColumn([

                'payment_method',

                'shipping_fee',

                'discount_amount',

                'tracking_number',

                'shipping_carrier',

                'shipping_address'

            ]);


        });

    }

};