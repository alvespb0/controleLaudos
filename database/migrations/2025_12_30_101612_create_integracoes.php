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
        Schema::create('integracoes', function (Blueprint $table) {
            $table->id();
            $table->string('sistema');
            $table->string('descricao')->nullable();
            $table->string('slug')->unique();
            $table->string('endpoint');
            $table->string('username')->nullable(); # nem todas as integrações necessitam username, os que necessitarem de oAuth2.0 serão feitas tabelas separadas
            $table->text('password_enc')->nullable(); # Vai ser lançado como nullable, para que seja lançado as integrações disponiveis (sistema e nome) e posteriormente o usuario coloque o pass
            $table->enum('auth', ['basic', 'bearer', 'wss']);
            $table->enum('tipo', ['soap', 'rest']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracoes');
    }
};
