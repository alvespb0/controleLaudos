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
        Schema::create('empresas_soc', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codigo_soc')->unique();
            $table->string('cnpj')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable(); # usuario pode vincular o cliente ao cadastro soc, facilitando a busca do cliente ao clicar em baixar ged
            $table->foreign('cliente_id')->references('id')->on('cliente')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas_soc');
    }
};
