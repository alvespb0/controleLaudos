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
        Schema::create('percentuais_comissao', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_cliente', ['novo', 'renovacao', 'resgatado'])->unique();
            $table->decimal('percentual', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percentuais_comissao');
    }
};
