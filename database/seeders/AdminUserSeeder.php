<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mudar123.com'], // Evita duplicar
            [
                'name' => 'admin',
                'email' => 'admin@mudar123.com',
                'password' => 'admin',
                'tipo' => 'admin'
            ]
        );
    }
}
