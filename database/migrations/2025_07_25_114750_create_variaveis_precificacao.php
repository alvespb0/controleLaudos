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
        Schema::create('variaveis_precificacao', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('campo_alvo');
            $table->string('tipo'); # bool, numeric, string, etc.
            $table->boolean('ativo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variaveis_precificacao');
    }
};
