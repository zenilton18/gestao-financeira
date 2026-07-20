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
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            
    
            $table->foreignId('lancamento_id')->constrained()->onDelete('cascade');
   
            $table->decimal('valor', 10, 2); // 10 dígitos no total, 2 casas decimais (essencial para dinheiro)
            $table->date('data_vencimento');
            $table->integer('situacao_id');
            $table->timestamps();
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};
