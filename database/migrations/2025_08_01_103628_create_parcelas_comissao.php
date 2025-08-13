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
        Schema::create('parcelas_comissao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comissao_id');
            $table->tinyInteger ('numero_parcela');
            $table->decimal('valor_parcela', 5, 2);
            $table->date('data_prevista');
            $table->enum('status', ['paga', 'pendente', 'cancelada']);
            $table->foreign('comissao_id')->references('id')->on('comissoes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelas_comissao');
    }
};
