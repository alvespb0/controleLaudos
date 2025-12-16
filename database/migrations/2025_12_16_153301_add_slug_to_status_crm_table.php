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
        Schema::table('status_crm', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('nome');
        });

        $map = [
            1 => 'lead',
            2 => 'contato',
            3 => 'proposta',
            4 => 'negociacao',
            5 => 'ganho', 
            6 => 'perdido',
        ];

        foreach ($map as $id => $slug) {
            DB::table('status_crm')
                ->where('id', $id)
                ->update(['slug' => $slug]);
        }

        Schema::table('status_crm', function (Blueprint $table) {
            $table->string('slug')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_crm', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
