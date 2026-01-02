<?php
namespace App\Services;

use App\Models\Integracao;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CodigoSocGedService
{
    /**
     * Busca os códigos GED (Gerenciamento Eletrônico de Documentos) do SOC
     * 
     * Realiza uma requisição GET para o endpoint configurado na integração do SOC,
     * filtrando por tipo de GED e código da empresa. A resposta é convertida para
     * UTF-8 para preservar caracteres especiais e retorna um array com os dados
     * dos GEDs encontrados.
     * 
     * @param string|int $tipoGed Código do tipo de GED a ser buscado
     * @param string|int $codEmpresa Código da empresa no sistema SOC
     * @return array|null Retorna um array associativo com os dados dos GEDs encontrados,
     *                    onde cada elemento contém:
     *                    - 'cod_ged': Código do GED
     *                    - 'nome_ged': Nome do GED
     *                    - 'data_emissao': Data de emissão do GED
     *                    Retorna null se nenhum GED for encontrado ou em caso de erro
     */
    public function getCodigoGed($tipoGed, $codEmpresa){
        $integracao = Integracao::where('slug', 'ws_soc_resgata_cod_ged')->first();
        $ged = array();

        try{
            $jsonString = "{\"empresa\":\"$codEmpresa\",\"codigo\":\"211289\",\"chave\":\"{$integracao->getDecryptedPassword()}\",\"tipoSaida\":\"json\",\"tipoBusca\":\"0\",\"sequencialFicha\":\"\",\"cpfFuncionario\":\"\",\"filtraPorTipoSocged\":\"1\",\"codigoTipoSocged\":\"$tipoGed\",\"dataInicio\":\"\",\"dataFim\":\"\",\"dataEmissaoInicio\":\"\",\"dataEmissaoFim\":\"\"}";            
            \Log::info('Preparando para requisitar o codigo ged solciitado cadastradas no SOC ', ['string da requisição' => $jsonString]);

            $response = Http::get($integracao->endpoint, [
                'parametro' => $jsonString
            ]);

            if($response->ok()){
                $body = $response->body();
                $bodyUtf8 = $this->convertToUtf8($body);

                $dados = json_decode($bodyUtf8, true);

                if(empty($dados)){
                    \Log::error('Não localizado código ged pela api de exporta dados do SOC');
                    return [];
                }

                foreach($dados as $dado){
                    $ged[] = [
                        'cod_ged' => $dado['CD_GED'],
                        'nome_ged' => $dado['NM_GED'],
                        'data_emissao' => $dado['DT_EMISSAO']
                    ];
                }
                
                \Log::info('Finalizado busca pelo ged', [$ged]);

                return $ged;
            }
        }catch (\Exception $e) {
            \Log::error("Erro: " . $e->getMessage());
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