<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::Create([
            'nome' => 'Pendente',
            'cor' => '#ffc107'
        ]);
        Status::Create([
            'nome' => 'Cancelado',
            'cor' => '#dc3545'
        ]);
        Status::Create([
            'nome' => 'Concluido',
            'cor' => '#28a745'
        ]);
    }
}
