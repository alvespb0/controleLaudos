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
            $table->enum('tipo', ['faixa', 'valor', 'bool']);
            $table->string('campo_alvo');
            $table->decimal('valor', 10, 2)->nullable(); # Para os tipos valor e bool
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
