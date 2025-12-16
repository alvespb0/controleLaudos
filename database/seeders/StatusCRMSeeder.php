<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status_Crm;

class StatusCRMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status_Crm::Create([
            'nome' => 'Lead',
            'descricao' => 'Novos contatos',
            'position' => 1,
            'padrao_sistema' => 1,
            'slug' => 'lead'
        ]);

        Status_Crm::Create([
            'nome' => 'Contato',
            'descricao' => 'Contato Inicial Pendente',
            'position' => 2,
            'padrao_sistema' => 1,
            'slug' => 'contato'
        ]);

        Status_Crm::Create([
            'nome' => 'Proposta',
            'descricao' => 'Proposta Incial Pendente',
            'position' => 3,
            'padrao_sistema' => 1,
            'slug' => 'proposta'
        ]);

        Status_Crm::Create([
            'nome' => 'Negociação',
            'descricao' => 'Negociação em andamento',
            'position' => 4,
            'padrao_sistema' => 1,
            'slug' => 'negociacao'
        ]);

        Status_Crm::Create([
            'nome' => 'Fechado (Ganho)',
            'descricao' => 'Oportunidade ganha',
            'position' => 5,
            'padrao_sistema' => 1,
            'slug' => 'ganho'
        ]);

        Status_Crm::Create([
            'nome' => 'Fechado (Perdido)',
            'descricao' => 'Oportunidade perdida',
            'position' => 6,
            'padrao_sistema' => 1,
            'slug' => 'perdido'
        ]);
    }
}
