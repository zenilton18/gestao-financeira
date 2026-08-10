<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->string('produto_id')->nullable()->after('centro_custo');
            $table->integer('quantidade')->default(1)->after('produto_id');
        });
    }

    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropColumn(['produto_id', 'quantidade']);
        });
    }
};