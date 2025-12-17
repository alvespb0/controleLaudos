<?php
namespace App\Services;

use App\Models\Cliente;
use App\Models\Variaveis_Precificacao;

class PrecificacaoLeadService
{
    /**
     * Calcula o valor mínimo e máximo sugerido para um lead com base em precificação por distância e número de funcionários.
     *
     * Esta função utiliza duas outras funções auxiliares:
     * - `precificaDistancia($cliente)`: retorna o valor fixo baseado na distância.
     * - `precificaNumFuncionarios($num_funcionarios)`: retorna um array com 'preco_min' 'preco_max' e 'percentual_reajuste'.
     *
     * A fórmula do valor final considera a soma do valor da distância e do valor ajustado por número de funcionários.
     * Com base no total, são calculadas sugestões de preço mínimo (−5%) e máximo (+5%).
     *
     * @param object $cliente           Objeto do cliente, que deve conter endereço e distância válidos.
     * @param int    $num_funcionarios Quantidade de funcionários da empresa do lead.
     *
     * @return array Retorna um array com os valores sugeridos:
     *               - 'valor_min_sugerido' (float): Valor baseado nas faixas de precificação
     *               - 'valor_max_sugerido' (float): Valor baseado nas faixas de precificação
     */
    public function precificaLead(Cliente $cliente, $num_funcionarios){
        $precoDist = $this->precificaDistancia($cliente); # retorna array, percentual e preço
        $precoFunc = $this->precificaNumFuncionarios($num_funcionarios); # retorna array, percentual e preco
        
        $precoFinalMin = 0;
        $precoFinalMax = 0;

        if ($precoDist != null) {
            $precoFinalMin += $precoDist;
            $precoFinalMax += $precoDist;
        }

        if ($precoFunc != null) {
            $reajusteFunc = $precoFunc['percentual_reajuste'] > 0
                ? $precoFunc['percentual_reajuste'] / 100
                : 1;

            $precoFinalMin += $precoFunc['preco_min'] * $reajusteFunc;
            $precoFinalMax += $precoFunc['preco_max'] * $reajusteFunc;
        }
        $retorno = [
            'valor_min_sugerido' => $precoFinalMin,
            'valor_max_sugerido' => $precoFinalMax,
        ];

        return $retorno;
    }

    /**
     * Calcula o valor de precificação com base na distância do cliente.
     *
     * Esta função busca a variável de precificação com o nome "Distancia".
     * Se existir e o cliente possuir endereço com distância definida, calcula
     * o valor multiplicando a distância pelo valor definido na variável.
     *
     * @param object $cliente Objeto do cliente que deve conter a propriedade 'endereco'
     *                        e dentro dela a propriedade 'distancia' (float).
     *
     * @return float|null Retorna o valor calculado (distância × valor da variável de precificação),
     *                    ou null se os dados forem insuficientes ou a variável não for encontrada.
     */
    public function precificaDistancia($cliente){
        $precificacao = Variaveis_Precificacao::where('nome', 'Distancia')->get();

        if($precificacao->isEmpty() || !$cliente->endereco || !$cliente->endereco->distancia){
            return null;
        }

        $distancia = $cliente->endereco->distancia;

        foreach ($precificacao as $p) {
            return $distancia * $p->valor * 2; 
        }
    }

    /**
     * Calcula o preço e o percentual de reajuste com base na quantidade de funcionários.
     *
     * Esta função busca as variáveis de precificação com o nome "Numero de Funcionarios",
     * e percorre as faixas associadas para encontrar a faixa correspondente à quantidade
     * informada de funcionários. Quando uma faixa compatível é encontrada (dentro do intervalo
     * valor_min e valor_max), retorna um array contendo o percentual de reajuste e o preço.
     *
     * @param int $num_funcionarios A quantidade de funcionários para aplicar a precificação.
     *
     * @return array|null Retorna um array com as chaves:
     *                    - 'percentual_reajuste' (float): O percentual aplicado.
     *                    - 'preco' (float): O valor definido para a faixa.
     *                    Ou null, se nenhuma faixa correspondente for encontrada.
     */
    public function precificaNumFuncionarios($num_funcionarios){
        $precificacao = Variaveis_Precificacao::where('nome', 'Numero de Funcionarios')->get();

        $retorno = [];

        foreach($precificacao as $p){
            foreach($p->faixas as $faixa){
                if($faixa->valor_min <= $num_funcionarios && $num_funcionarios <= $faixa->valor_max){
                    $retorno = [
                        'percentual_reajuste' => $faixa->percentual_reajuste,
                        'preco_min' => $faixa->preco_min,
                        'preco_max' => $faixa->preco_max
                    ];
                    return $retorno;
                }
            }
        }

        return null;
    }

}

?>