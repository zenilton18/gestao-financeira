<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('shopee_model_id');

            $table->string('nome');

            $table->string('sku')->nullable();

            $table->decimal('preco',10,2)->default(0);

            $table->integer('estoque')->default(0);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};