<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Percentuais_Comissao;

class PercentuaisComissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Percentuais_Comissao::create([
            'tipo_cliente' => 'novo',
            'percentual' => 10
        ]);

        Percentuais_Comissao::create([
            'tipo_cliente' => 'renovacao',
            'percentual' => 7
        ]);
        
        Percentuais_Comissao::create([
            'tipo_cliente' => 'resgatado',
            'percentual' => 3
        ]);
    }
}
