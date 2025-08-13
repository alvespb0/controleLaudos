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
        Schema::table('documentos_tecnicos', function (Blueprint $table) {
            $table->enum('tipo_documento', ['CAT', 'PPP', 'ADENDO', 'OS', 'ART', 'REAJUSTES LAUDOS', 'ASSESSORIA'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
