<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lancamentos', function (Blueprint $table) {
            $table->id();
            
            // 🔗 ESTE É O VÍNCULO COM A CATEGORIA!
            // Ele cria um campo 'categoria_id' e avisa o banco que ele se conecta à tabela 'categorias'
            $table->foreignId('categoria_id')->constrained()->onDelete('cascade');
            $table->string('descricao');
            $table->date('data_emissao');
            $table->decimal('valor', 10, 2); // 10 dígitos no total, 2 casas decimais (essencial para dinheiro)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamentos');
    }
};
