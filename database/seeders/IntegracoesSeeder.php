<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Integracao;

class IntegracoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Integracao::create([
            'sistema' => 'SOC',
            'descricao' => 'WS Soc para download de SOCGED',
            'endpoint' => 'https://ws1.soc.com.br/WSSoc/DownloadArquivosWs',
            'slug' => 'ws_soc_download_ged',
            'auth' => 'wss',
            'tipo' => 'soap'
        ]);

        Integracao::create([
            'sistema' => 'SOC',
            'descricao' => 'WS Soc para cadastro de empresas',
            'endpoint' => 'https://ws1.soc.com.br/WebSoc/exportadados',
            'slug' => 'ws_soc_empresas_cadastradas',
            'auth' => 'bearer',
            'tipo' => 'rest'
        ]);

        Integracao::create([
            'sistema' => 'SOC',
            'descricao' => 'WS Soc para resgatar o codigo ged dos laudos',
            'endpoint' => 'https://ws1.soc.com.br/WebSoc/exportadados',
            'slug' => 'ws_soc_resgata_cod_ged',
            'auth' => 'bearer',
            'tipo' => 'rest'
        ]);

    }
}
