<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['pagar', 'receber']);
            $table->string('descricao');
            $table->decimal('valor', 10, 2); // Valor individual da parcela
            $table->date('data_vencimento');
            $table->enum('status', ['pendente', 'pago'])->default('pendente');
            $table->integer('numero_parcela')->default(1);
            $table->integer('total_parcelas')->default(1);
            $table->uuid('grupo_id')->nullable(); // Para agrupar as parcelas do mesmo lançamento
            $table->timestamps();
            $table->date('data_pagamento')->nullable()->after('data_vencimento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas');
    }
};