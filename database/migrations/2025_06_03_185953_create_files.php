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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('nome_arquivo');
            $table->enum('tipo', ['orcamento', 'contrato', 'outro'])->default('outro'); # Enum utilizado para escalabilidade e segurança
            $table->string('caminho');
            $table->date('data_referencia')->nullable(); # Data do contrato/orçamento/etc
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('laudo_id')->nullable();
            $table->unsignedBigInteger('criado_por')->nullable();

            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente')->onDelete('cascade');
            $table->foreign('laudo_id')->references('id')->on('laudos')->onDelete('cascade');
            $table->foreign('criado_por')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
