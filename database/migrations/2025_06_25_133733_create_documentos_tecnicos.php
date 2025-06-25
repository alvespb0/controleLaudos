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
        Schema::create('documentos_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_documento', ['CAT', 'PPP', 'ADENDO']);
            $table->string('descricao');
            $table->date('data_elaboracao'); # data de acontecimento da cat por exemplo
            $table->date('data_conclusao')->nullable(); # data de conclusão, já lançado esocial e afins
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('tecnico_id')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente')->nullOnDelete();
            $table->foreign('status_id')->references('id')->on('status')->nullOnDelete();
            $table->foreign('tecnico_id')->references('id')->on('op_tecnicos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_tecnicos');
    }
};
