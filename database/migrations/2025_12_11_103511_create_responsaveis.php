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
        Schema::create('responsaveis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laudo_id');
            $table->unsignedBigInteger('tecnico_id');
            $table->enum('tipo', ['levantamento', 'engenheiro', 'digitacao']);
            $table->foreign('laudo_id')->references('id')->on('laudos')->onDelete('cascade');
            $table->foreign('tecnico_id')->references('id')->on('op_tecnicos')->onDelete('cascade');
            $table->timestamps();
        });

        DB::table('laudos')
            ->whereNotNull('tecnico_id')
            ->orderBy('id') // obrigatório no chunk()
            ->chunk(100, function ($laudos) {
                foreach ($laudos as $laudo) {
                    DB::table('responsaveis')->insert([
                        'laudo_id'   => $laudo->id,
                        'tecnico_id' => $laudo->tecnico_id,
                        'tipo'       => 'levantamento',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsaveis');
    }
};
