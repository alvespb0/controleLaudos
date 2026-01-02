<?php
namespace App\Services;

use App\Models\Empresas_Soc;
use App\Models\Integracao;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class EmpresasSocService
{
    /**
     * Busca e atualiza as empresas cadastradas no SOC através da API
     * 
     * Realiza uma requisição GET para o endpoint configurado na integração,
     * converte a resposta para UTF-8 para preservar caracteres especiais,
     * e salva/atualiza os registros no banco de dados.
     * 
     * @return bool|string Retorna true em caso de sucesso ou uma string com mensagem de erro
     */
    public function GetEmpresasSoc(){
        $integracao = Integracao::where('slug', 'ws_soc_empresas_cadastradas')->first();

        $codEmpresaPrincipal = ENV('COD_EMPRESA_SOC');

        try{
            $jsonString = "{\"empresa\":\"$codEmpresaPrincipal\",\"codigo\":\"211287\",\"chave\":\"{$integracao->getDecryptedPassword()}\",\"tipoSaida\":\"json\"}";
            
            \Log::info('Preparando para requisitar as empresas cadastradas no SOC ', ['string da requisição' => $jsonString]);

            $response = Http::get($integracao->endpoint, [
                'parametro' => $jsonString
            ]);

            if($response->ok()){
                $body = $response->body();
                $bodyUtf8 = $this->convertToUtf8($body);

                $dados = json_decode($bodyUtf8, true);
                if(empty($dados)){
                    \Log::error('Não localizado empresas pela api de exporta dados do SOC');
                    return null;
                }

                foreach($dados as $dado){
                    $cnpj = preg_replace('/\D/', '', $dado['CNPJ']);
                    Empresas_Soc::updateOrCreate(
                        ['codigo_soc' => $dado['CODIGO']],
                        [
                            'nome' => $dado['RAZAOSOCIAL'],
                            'cnpj' => $cnpj
                        ]
                    );
                }
            }

            \Log::info('Finalizado Busca de empresas cadastradas no SOC');
            
            return true;
        }catch (\Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }

    /**
     * Converte uma string para UTF-8, preservando caracteres especiais
     * 
     * Tenta detectar o encoding atual e converte para UTF-8.
     * Se a detecção falhar, tenta converter de ISO-8859-1 para UTF-8.
     * 
     * @param string $string String a ser convertida
     * @return string String convertida para UTF-8
     */
    private function convertToUtf8(string $string): string
    {
        // Detecta o encoding atual
        $encoding = mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        
        // Se já está em UTF-8, retorna como está
        if ($encoding === 'UTF-8') {
            return $string;
        }
        
        // Converte para UTF-8
        return mb_convert_encoding($string, 'UTF-8', $encoding ?: 'ISO-8859-1');
    }
}