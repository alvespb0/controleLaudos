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
        Schema::create('endereco_cliente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->string('cep');
            $table->string('bairro');
            $table->string('rua');
            $table->string('numero');
            $table->string('complemento')->nullable();
            $table->string('cidade');
            $table->string('uf');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->float('distancia')->nullable();
            $table->timestamps();
            $table->foreign('cliente_id')->references('id')->on('cliente')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endereco_cliente');
    }
};
