<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\StatusSeeder;
use Database\Seeders\StatusCRMSeeder;
use Database\Seeders\VariavelPadraoSeeder;
use Database\Seeders\PercentuaisComissaoSeeder;
use Database\Seeders\IntegracoesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(AdminUserSeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(StatusCRMSeeder::class);
        $this->call(VariavelPadraoSeeder::class);
        $this->call(PercentuaisComissaoSeeder::class);
        $this->call(IntegracoesSeeder::class);
    }
}
