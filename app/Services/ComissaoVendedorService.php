<?php
namespace App\Services;

use App\Models\Percentuais_Comissao;
use App\Models\Comissoes;
use App\Models\Parcelas_Comissao;
use App\Models\Lead;
use Carbon\Carbon;

class ComissaoVendedorService
{
    /**
     * Cria uma nova comissão para o lead informado.
     *
     * - Se o lead já possuir uma comissão registrada, ela será excluída antes da criação de uma nova.
     * - A comissão é calculada com base no valor definido do lead e o percentual de comissão do vendedor.
     * - O status inicial da comissão é definido como "pendente".
     *
     * @param \App\Models\Lead $lead O lead para o qual a comissão será criada. O objeto deve possuir as relações
     *                                `vendedor` (com atributo `percentual_comissao`) e, opcionalmente, `comissao`.
     *
     * @return bool Retorna `true` após criar a comissão com sucesso.
     *
     * @throws \Exception Pode lançar exceções se os relacionamentos esperados não estiverem carregados
     *                    ou se o modelo estiver malformado.
     */
    public function createComissao(Lead $lead){
        $percentuais = Percentuais_Comissao::all();
        
        if($lead->comissao_personalizada !== null){
            $porcentagemTotal = $lead->comissao_personalizada;
            if(!$lead->recomendador_id){
                $comissao = Comissoes::create([
                    'lead_id' => $lead->id,
                    'vendedor_id' => $lead->vendedor->id,
                    'valor_comissao' => $lead->valor_definido * ($porcentagemTotal/100),
                    'percentual_aplicado' => $porcentagemTotal,
                    'tipo_comissao' => 'vendedor',
                    'status' => 'pendente'
                ]);
                $this->createParcelasComissao($comissao);                   
            }else{
                $comissao = Comissoes::create([
                    'lead_id' => $lead->id,
                    'vendedor_id' => $lead->vendedor->id,
                    'valor_comissao' => max(0, $lead->valor_definido * (($porcentagemTotal - 2) / 100)),
                    'percentual_aplicado' => $porcentagemTotal - 2,
                    'tipo_comissao' => 'vendedor',
                    'status' => 'pendente'
                ]);
                Comissoes::create([
                    'lead_id' => $lead->id,
                    'valor_comissao' => $lead->valor_definido * 0.02,
                    'percentual_aplicado' => 2,
                    'tipo_comissao' => 'indicador',
                    'status' => 'pendente',
                    'recomendador_id' => $lead->recomendador_id
                ]);
                $this->createParcelasComissao($comissao);
            }
            return true;
        }

        foreach($percentuais as $value){
            $porcentagemTotal = $value->percentual;
            if($lead->cliente->tipo_cliente === $value->tipo_cliente){
                if(!$lead->recomendador_id){
                    $comissao = Comissoes::create([
                        'lead_id' => $lead->id,
                        'vendedor_id' => $lead->vendedor->id,
                        'valor_comissao' => $lead->valor_definido * ($porcentagemTotal/100),
                        'percentual_aplicado' => $porcentagemTotal,
                        'tipo_comissao' => 'vendedor',
                        'status' => 'pendente'
                    ]);
                    $this->createParcelasComissao($comissao);                   
                }else{ # SE HOUVE INDICAÇÃO, o vendedor recebe - 2 % da porcentagem total da comissão, 2% destinado ao indicador
                    $comissao = Comissoes::create([
                        'lead_id' => $lead->id,
                        'vendedor_id' => $lead->vendedor->id,
                        'valor_comissao' => $lead->valor_definido * (($porcentagemTotal - 2)/100),
                        'percentual_aplicado' => $porcentagemTotal - 2,
                        'tipo_comissao' => 'vendedor',
                        'status' => 'pendente'
                    ]);
                    Comissoes::create([
                        'lead_id' => $lead->id,
                        'valor_comissao' => $lead->valor_definido * 0.02,
                        'percentual_aplicado' => 2,
                        'tipo_comissao' => 'indicador',
                        'status' => 'pendente',
                        'recomendador_id' => $lead->recomendador_id
                    ]);
                    $this->createParcelasComissao($comissao);
                }
            }
        }
        return true;
    }

    private function createParcelasComissao($comissao){
        $num_parcelas = $comissao->lead->num_parcelas;
        $dataBase = Carbon::now()->addMonth()->day(10);

        for($i = 1; $i <= $num_parcelas; $i++){
            Parcelas_Comissao::create([
                'comissao_id' => $comissao->id,
                'numero_parcela' => $i,
                'valor_parcela' => ($comissao->valor_comissao/$num_parcelas),
                'data_prevista' => $dataBase->copy()->addMonthsNoOverflow($i - 1),
                'status' => 'pendente'
            ]);
        }

        return true;
    }


}
?>