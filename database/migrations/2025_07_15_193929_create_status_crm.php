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
        Schema::create('status_crm', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('descricao'); # descrição breve para ser usado como subtittle
            $table->integer('position')->unique();
            $table->boolean('padrao_sistema')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_crm');
    }
};
