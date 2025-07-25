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
        Schema::create('faixa_precificacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variavel_id');
            $table->decimal('valor_min');
            $table->decimal('valor_max');
            $table->decimal('percentual_reajuste')->nullable();
            $table->decimal('preco', 10,2)->nullable();
            $table->timestamps();
            $table->foreign('variavel_id')->references('id')->on('variaveis_precificacao')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faixa_precificacao');
    }
};
