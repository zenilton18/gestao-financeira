<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('shopee_item_id')
                ->nullable()
                ->change();
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->unsignedBigInteger('shopee_model_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('shopee_item_id')
                ->nullable(false)
                ->change();
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->unsignedBigInteger('shopee_model_id')
                ->nullable(false)
                ->change();
        });
    }
};