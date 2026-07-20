<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('shop_id');

            $table->unsignedBigInteger('shopee_item_id')->unique();

            $table->string('nome');

            $table->string('sku')->nullable();

            $table->string('marca')->nullable();

            $table->unsignedBigInteger('categoria_id')->nullable();

            $table->string('imagem')->nullable();
            $table->string('codigo_barras')->nullable();

            $table->string('codigo_interno')->nullable();

            // $table->foreignId('supplier_id')->nullable()->constrained();

            $table->decimal('preco_custo', 10, 2)->nullable();

            $table->decimal('preco_venda', 10, 2)->nullable();

            $table->integer('estoque_minimo')->default(0);

            $table->string('localizacao')->nullable();

            $table->text('observacoes')->nullable();
            $table->string('status')->nullable();

            $table->boolean('possui_variacao')->default(false);

            $table->integer('estoque_total')->default(0);

            $table->decimal('peso',10,2)->nullable();

            $table->decimal('comprimento',10,2)->nullable();

            $table->decimal('largura',10,2)->nullable();

            $table->decimal('altura',10,2)->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};