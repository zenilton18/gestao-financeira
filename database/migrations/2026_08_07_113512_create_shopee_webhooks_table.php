<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopee_webhooks', function (Blueprint $table) {

            $table->id();

            $table->string('msg_id')
                ->unique();

            $table->string('order_sn')
                ->nullable();

            $table->unsignedBigInteger('shop_id')
                ->nullable();

            $table->string('status')
                ->nullable();

            $table->integer('code')
                ->nullable();

            $table->json('payload');

            $table->boolean('processed')
                ->default(false);

            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('shopee_webhooks');
    }
};