<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('tokens', function (Blueprint $table) {


            $table->id();


            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();


            $table->unsignedBigInteger('empresa_id')
                  ->nullable();


            $table->string('token', 255)
                  ->unique();


            $table->string('device_name')
                  ->nullable();


            $table->ipAddress('ip_address')
                  ->nullable();


            $table->timestamp('expires_at');


            $table->timestamp('last_used_at')
                  ->nullable();


            $table->boolean('revoked')
                  ->default(false);


            $table->timestamps();

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }

};