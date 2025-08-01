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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('vendedor_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('nome_contato')->nullable();
            $table->integer('num_funcionarios')->nullable();
            $table->decimal('valor_min_sugerido')->nullable();
            $table->decimal('valor_max_sugerido')->nullable();
            $table->decimal('valor_definido')->nullable();
            $table->integer('num_parcelas')->default(1);
            $table->boolean('orcamento_gerado')->default(false);
            $table->boolean('contrato_gerado')->default(false);
            $table->date('proximo_contato')->nullable();
            $table->boolean('notificado')->default(false);
            $table->timestamps();
            $table->foreign('cliente_id')->references('id')->on('cliente')->onDelete('cascade');
            $table->foreign('vendedor_id')->references('id')->on('op_comercial')->nullOnDelete();
            $table->foreign('status_id')->references('id')->on('status_crm')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
