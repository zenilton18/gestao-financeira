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
        Schema::create('mercado_livre_connections', function (Blueprint $table) {
            $table->id();

            // Usuário do MGF que conectou a conta
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // ID do usuário/vendedor no Mercado Livre
            $table->string('mercadolivre_user_id');

            // Nickname da conta Mercado Livre
            $table->string('nickname')->nullable();

            // Tokens OAuth
            $table->text('access_token');
            $table->text('refresh_token');

            // Controle de validade do Access Token
            $table->timestamp('expires_at')->nullable();

            // Informações retornadas pelo OAuth
            $table->string('token_type')->nullable();
            $table->text('scope')->nullable();

            // Controle da conexão
            $table->boolean('active')->default(true);

            $table->timestamps();

            // Um usuário do MGF pode ter várias contas ML,
            // mas a mesma conta ML não pode ser cadastrada duas vezes.
            $table->unique('mercadolivre_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercado_livre_connections');
    }
};