<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            // Adiciona a coluna 'custo' após a coluna 'preco' (opcional, pode remover o ->after se preferir)
            $table->decimal('custo', 10, 2)->default(0)->after('preco');
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn('custo');
        });
    }
};