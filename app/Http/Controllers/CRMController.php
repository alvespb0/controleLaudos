<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Mail\LeadNotifyMail;

use Illuminate\Http\Request;
use App\Http\Requests\LeadRequest; 

use App\Models\Cliente;
use App\Models\Op_Comercial;
use App\Models\Status_Crm;
use App\Models\Lead;
use App\Models\Variaveis_Precificacao;

class CRMController extends Controller
{
    /**
     * Exibe a tela principal do CRM com os dados necessários.
     *
     * Essa função carrega todos os clientes, todos os usuários do setor comercial
     * e as etapas (status) do funil de vendas ordenadas pela posição, para montar
     * o Kanban ou painel CRM.
     *
     * @return \Illuminate\View\View
     * Retorna a view 'Crm/CRM_index' com os dados carregados.
     */
    public function showCRM(){
        $clientes = Cliente::all();
        $comercial = Op_Comercial::all();
        $status_crm = Status_Crm::orderBy('position', 'asc')->get();

        $leads = Lead::query();
        $periodo = request('periodo');

        if(!empty(request('periodo')) && request('periodo') !== 'all'){
            $periodo = request('periodo');
            $leads->where('created_at', '>=', now()->subDays($periodo));
        }

        if(!empty(request('vendedor'))){
            $vendedor = request('vendedor');
            $leads->where('vendedor_id', $vendedor);
        }

        if(!empty(request('busca'))){
            $busca = request('busca');
            $leads->whereHas('cliente', function ($query) use ($busca) {
                $query->where('nome', 'like', "%{$busca}%");
            });
        }

        $leads = $leads->get();

        $this->notificaVendedor();
        return view('Crm/CRM_index', ['clientes' => $clientes, 'comercial' => $comercial,
                                        'etapas' => $status_crm, 'leads' => $leads]);
    }
    
    /**
     * Cria um novo lead no CRM a partir dos dados validados.
     *
     * @param LeadRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createLead(LeadRequest $request){
        $request->validated();
        $user = Auth::user();

        $cliente = Cliente::findOrFail($request->cliente_id);
        $valores = $this->precificaLead($cliente, $request->num_funcionarios);
  
        Lead::create([
            'cliente_id' => $request->cliente_id,
            'vendedor_id' => $user->comercial->id ?? null,
            'status_id' => $request->status_id,
            'observacoes' => $request->observacoes,
            'nome_contato' => $request->nome_contato,
            'valor_min_sugerido' => $valores['valor_min_sugerido'] ?? null,
            'valor_max_sugerido' => $valores['valor_max_sugerido'] ?? null,
            'num_funcionarios' => $request->num_funcionarios,
            'proximo_contato' => $request->proximo_contato
        ]);

        session()->flash('mensagem', 'Lead criado com sucesso');

        return redirect()->route('show.CRM');
    }

    /**
     * Calcula o valor mínimo e máximo sugerido para um lead com base em precificação por distância e número de funcionários.
     *
     * Esta função utiliza duas outras funções auxiliares:
     * - `precificaDistancia($cliente)`: retorna o valor fixo baseado na distância.
     * - `precificaNumFuncionarios($num_funcionarios)`: retorna um array com 'preco' base e 'percentual_reajuste'.
     *
     * A fórmula do valor final considera a soma do valor da distância e do valor ajustado por número de funcionários.
     * Com base no total, são calculadas sugestões de preço mínimo (−5%) e máximo (+5%).
     *
     * @param object $cliente           Objeto do cliente, que deve conter endereço e distância válidos.
     * @param int    $num_funcionarios Quantidade de funcionários da empresa do lead.
     *
     * @return array Retorna um array com os valores sugeridos:
     *               - 'valor_min_sugerido' (float): 5% abaixo do valor final.
     *               - 'valor_max_sugerido' (float): 5% acima do valor final.
     */
    public function precificaLead($cliente, $num_funcionarios){
        $precoDist = $this->precificaDistancia($cliente); # retorna array, percentual e preço
        $precoFunc = $this->precificaNumFuncionarios($num_funcionarios); # retorna array, percentual e preco
        
        $precoFinal = 0;

        if ($precoDist != null) {
            $precoFinal += $precoDist;
        }

        if ($precoFunc != null) {
            $reajusteFunc = $precoFunc['percentual_reajuste'] > 0
                ? $precoFunc['percentual_reajuste'] / 100
                : 1;

            $precoFinal += $precoFunc['preco'] * $reajusteFunc;
        }
        $retorno = [
            'valor_min_sugerido' => $precoFinal * 0.95,
            'valor_max_sugerido' => $precoFinal * 1.05,
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
            return $distancia * $p->valor; 
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
                        'preco' => $faixa->preco
                    ];
                    return $retorno;
                }
            }
        }

        return null;
    }

    /**
     * Atualiza as informações de um lead existente no banco de dados.
     *
     * @param  \App\Http\Requests\LeadRequest  $request  Requisição validada contendo os dados do lead.
     * @return \Illuminate\Http\RedirectResponse  Redireciona de volta para a tela do CRM com uma mensagem de sucesso.
     */
    public function updateLead(LeadRequest $request){
        $request->validated();
        
        $lead = Lead::findOrFail($request->lead_id);
        $user = Auth::user();

        $cliente = Cliente::findOrFail($request->cliente_id);
        $valores = $this->precificaLead($cliente, $request->num_funcionarios);

        $lead->update([
            'vendedor_id' => $user->comercial->id ?? null,
            'observacoes' => $request->observacoes,
            'nome_contato' => $request->nome_contato,
            'investimento' => $request->investimento,
            'valor_min_sugerido' => $valores['valor_min_sugerido'] ?? null,
            'valor_max_sugerido' => $valores['valor_max_sugerido'] ?? null,
            'num_funcionarios' => $request->num_funcionarios,
            'proximo_contato' => $request->proximo_contato
        ]);

        session()->flash('mensagem', 'Lead alterado com sucesso');

        return redirect()->route('show.CRM');
    }

    /**
     * Remove um lead do banco de dados.
     *
     * @param  int  $id  ID do lead a ser removido.
     * @return \Illuminate\Http\RedirectResponse  Redireciona de volta para o CRM com mensagem de sucesso.
     */
    public function deleteLead($id){
        $lead = Lead::findOrFail($id);

        $lead->delete();
        
        session()->flash('mensagem', 'Lead Excluido com sucesso');

        return redirect()->route('show.CRM');
    }

    /**
     * Altera o status (etapa) de um lead específico.
     *
     * Busca o lead pelo ID, atualiza seu status para o novo etapa_id,
     * define uma mensagem de sucesso na sessão e redireciona para a rota 'show.CRM'.
     *
     * @param int $lead_id ID do lead que terá o status alterado.
     * @param int $etapa_id Novo ID da etapa/status para o lead.
     * @return \Illuminate\Http\RedirectResponse Redireciona para a rota 'show.CRM'.
     */
    public function alterStatusLead($lead_id, $etapa_id){
        $lead = Lead::findOrFail($lead_id);

        if($etapa_id == 5 && !$this->validaDadosCobranca($lead)){ # etapa = 5 representa a etapa Oportunidade Ganha é um valor imutável no banco também marcada como padrao_sistema
            session()->flash('error', 'Atualize os dados de cobrança antes de continuar');

            return redirect()->route('show.CRM');
        }

        $lead->update([
            'status_id' => $etapa_id
        ]);

        session()->flash('mensagem', 'Etapa alterada com sucesso');

        return redirect()->route('show.CRM');
    }

    public function validaDadosCobranca($lead){
        
        if (!$lead->cliente || !$lead->cliente->dadosCobranca) {
            return false;
        }

        $campos = [
            'cep', 'bairro', 'rua', 'numero', 'uf', 'cidade', 'email_cobranca', 'telefone_cobranca'
        ];

        foreach($campos as $campo){
            if(!$lead->cliente->dadosCobranca->$campo){
                return false; # se algum campo não passar nessa validação retorna null
            }
        }

        return true; # se nenhum campo cair na validação, retorna true
    }
    /**
     * 
     * Exibe o formulário de orçamento para um lead específico.
     *
     * Busca o lead pelo ID e retorna a view do formulário de orçamento,
     * passando o objeto lead para a view.
     *
     * @param int $lead_id ID do lead para o qual será exibido o formulário.
     * @return \Illuminate\View\View Retorna a view 'Crm.CRM_orcamento_lead' com o lead.
     */
    public function formularioOrcamento($lead_id){
        $lead = Lead::findOrFail($lead_id);
        return view('/Crm/CRM_orcamento_lead', ['lead' => $lead]);
    }

    /**
     * Notifica os vendedores sobre leads que precisam de contato no próximo dia.
     *
     * Esta função realiza a busca por leads que possuem um vendedor associado, 
     * com a data de "próximo_contato" marcada para o próximo dia e que ainda não foram notificados.
     * Para cada lead encontrado, envia um e-mail ao vendedor associado com a notificação,
     * e marca o lead como notificado.
     *
     * @return void
     * 
     * @throws \Illuminate\Mail\MailException Se houver algum erro ao enviar o e-mail.
     */
    public function notificaVendedor(){
        $leads = Lead::whereNotNull('vendedor_id')
            ->whereNotNull('proximo_contato')
            ->whereDate('proximo_contato', '=', now()->addDay()->toDateString())
            ->where('notificado', false)
            ->get();

        #dd($leads);
        foreach ($leads as $lead) {
            Mail::mailer('default')->to($lead->vendedor->user->email)->send(new LeadNotifyMail($lead));

            $lead->update([
                'notificado' => true
            ]);
        }
    }

    public function updateInvestimentoLead(Request $request){
        $lead = Lead::findOrFail($request->lead_id);

        $lead->update([
            'valor_definido' => $request->investimento
        ]);

        session()->flash('mensagem', 'Investimento definido com sucesso');

        return redirect()->route('show.CRM');
    }
}
