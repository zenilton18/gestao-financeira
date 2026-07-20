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
        Schema::create('shopee_credentials', function (Blueprint $table) {

            $table->id();

            // Futuramente poderá relacionar com empresa/tenant
            // $table->foreignId('empresa_id')->nullable()->constrained();

            $table->unsignedBigInteger('shop_id')->unique();

            $table->string('shop_name')->nullable();

            $table->string('access_token', 255);

            $table->string('refresh_token', 255);

            $table->timestamp('access_token_expires_at');

            $table->timestamp('refresh_token_expires_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopee_credentials');
    }
};