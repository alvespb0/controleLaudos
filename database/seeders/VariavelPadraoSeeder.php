<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Variaveis_Precificacao;

class VariavelPadraoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Variaveis_Precificacao::Create([
            'nome' => 'Numero de Funcionarios',
            'campo_alvo' => 'Numero de funcionários',
            'tipo' => 'faixa',
        ]);

        Variaveis_Precificacao::Create([
            'nome' => 'Distancia',
            'campo_alvo' => 'Deslocamento',
            'tipo' => 'valor',
        ]);

        Variaveis_Precificacao::Create([
            'nome' => 'Imposto',
            'campo_alvo' => 'Imposto em cima do valor bruto',
            'tipo' => 'percentual',
            'valor' => 22
        ]);
    }
}
