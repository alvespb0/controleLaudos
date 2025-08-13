<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cliente', function (Blueprint $table) {
            $table->enum('tipo_cliente', ['novo', 'renovacao', 'resgatado'])->nullable();
        });

        DB::statement("
            UPDATE cliente
            SET tipo_cliente = CASE
                WHEN cliente_novo = 1 THEN 'novo'
                ELSE 'renovacao'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
