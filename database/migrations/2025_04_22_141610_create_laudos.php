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
        Schema::create('laudos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->date('data_previsao')->nullable(); # pode ser null, data de previsão é algo muito relativo, vai do operador comercial definir previsão ou não
            $table->date('data_conclusao')->nullable(); # pode ser null, data de conclusão vai ser definida somente ao fim do laudo, pela área técnica
            $table->date('data_fim_contrato'); # é vinculado ao laudo lo no cadsatro do laudo
            $table->integer('numero_clientes'); # campo OBRIGATÓRIO
            $table->unsignedBigInteger('tecnico_id')->nullable(); # pode ser null, tecnico responsável é definido após o cadastro do laudo
            $table->unsignedBigInteger('status_id')->nullable(); # pode ser null, status é vinculado após o cadastro do laudo, pela área técnica
            $table->unsignedBigInteger('cliente_id')->nullable(); # é vinculado a qual cliente pertence esse laudo logo no cadastro do laudo
            $table->unsignedBigInteger('comercial_id')->nullable(); # é vinculado o responsável pela venda logo no cadastro do laudo
            $table->timestamps();

            $table->foreign('tecnico_id')->references('id')->on('op_tecnicos')->nullOnDelete();
            $table->foreign('status_id')->references('id')->on('status')->nullOnDelete();
            $table->foreign('cliente_id')->references('id')->on('cliente')->nullOnDelete();
            $table->foreign('comercial_id')->references('id')->on('op_comercial')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laudos');
    }
};
