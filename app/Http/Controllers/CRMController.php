<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\LeadRequest; 

use App\Models\Cliente;
use App\Models\Op_Comercial;
use App\Models\Status_Crm;
use App\Models\Lead;
use App\Models\Variaveis_Precificacao;
use App\Models\Comissoes;
use App\Models\Parcelas_Comissao;
use App\Models\Percentuais_Comissao;
use App\Models\Recomendadores;

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
        $clientes = Cliente::orderBy('nome', 'asc')->get();
        $comercial = Op_Comercial::all();
        $status_crm = Status_Crm::orderBy('position', 'asc')->get();
        $recomendadores = Recomendadores::all();

        $leads = Lead::query();

        $periodo = request('periodo') ?? 15;

        if ($periodo !== 'all') {
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

        if(!empty(request('etapa'))){
            $etapa = request('etapa');
            $leads->where('status_id', $etapa);
        }

        $leads = $leads->get();

        return view('Crm/CRM_index', ['clientes' => $clientes, 'comercial' => $comercial,
                                        'etapas' => $status_crm, 'leads' => $leads, 'recomendadores' => $recomendadores]);
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
        $valores = (new \App\Services\PrecificacaoLeadService())
                    ->precificaLead($cliente, $request->num_funcionarios);

  
        Lead::create([
            'cliente_id' => $request->cliente_id,
            'vendedor_id' => $user->comercial->id ?? null,
            'status_id' => $request->status_id,
            'observacoes' => $request->observacoes,
            'nome_contato' => $request->nome_contato,
            'valor_min_sugerido' => $valores['valor_min_sugerido'] ?? null,
            'valor_max_sugerido' => $valores['valor_max_sugerido'] ?? null,
            'num_funcionarios' => $request->num_funcionarios,
            'proximo_contato' => $request->proximo_contato,
            'recomendador_id' => $request->recomendador_id
        ]);

        $evento = null;
        if($request->adicionar_agenda){
            $evento = \App\Http\Controllers\GoogleController::createEvent([
                'summary' => 'Reunião com o cliente:'.$cliente->nome,
                'start'   => \Carbon\Carbon::parse($request->proximo_contato)->toRfc3339String(),
                'end'     => \Carbon\Carbon::parse($request->proximo_contato)->copy()->addHour()->toRfc3339String(),
            ]);
        }

        if (!$evento && $request->adicionar_agenda) {
            session()->flash('error', 'Lead criado, mas houve erro ao salvar no Google Agenda');
        } else {
            session()->flash('mensagem', 'Lead criado com sucesso!');
        }

        return redirect()->route('show.CRM');
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
        $valores = (new \App\Services\PrecificacaoLeadService())
                    ->precificaLead($cliente, $request->num_funcionarios);
        
        $lead->update([
            'vendedor_id' => $user->comercial->id ?? null,
            'observacoes' => $request->observacoes,
            'nome_contato' => $request->nome_contato,
            'investimento' => $request->investimento,
            'valor_min_sugerido' => $valores['valor_min_sugerido'] ?? null,
            'valor_max_sugerido' => $valores['valor_max_sugerido'] ?? null,
            'num_funcionarios' => $request->num_funcionarios,
            'proximo_contato' => $request->proximo_contato,
            'recomendador_id' => $request->recomendador_id
        ]);

        activity('lead_usuario')
            ->performedOn($lead)
            ->causedBy(auth()->user())
            ->log("Atualizou dados do lead");

        $evento = null;
        if($request->adicionar_agenda){
            $evento = \App\Http\Controllers\GoogleController::createEvent([
                'summary' => 'Reunião com o cliente:'.$cliente->nome,
                'start'   => \Carbon\Carbon::parse($request->proximo_contato)->toRfc3339String(),
                'end'     => \Carbon\Carbon::parse($request->proximo_contato)->copy()->addHour()->toRfc3339String(),
            ]);
        }

        if (!$evento && $request->adicionar_agenda) {
            session()->flash('error', 'Lead criado, mas houve erro ao salvar no Google Agenda');
        } else {
            session()->flash('mensagem', 'Lead criado com sucesso!');
        }

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

        $newEtapa = Status_Crm::findOrFail($etapa_id);

        $slug = $newEtapa->slug;
        
        $oldEtapa_id = $lead->status->id;
        $oldEtapa_nome = $lead->status->nome;

        if ($slug == 'ganho') {
            if (!$this->validaDadosCobranca($lead)) {
                session()->flash('error', 'Atualize os dados de cobrança antes de continuar');
                return redirect()->route('show.CRM');
            }

            if (!$lead->vendedor) {
                session()->flash('error', 'Esse lead não possui vendedor vinculado');
                return redirect()->route('show.CRM');
            }

            if (!$lead->valor_definido) {
                session()->flash('error', 'Atualize o investimento definido nesse lead');
                return redirect()->route('show.CRM');
            }
        }

        if($lead->status->slug == 'ganho' && $slug != 'ganho' && $lead->comissao){
            $lead->comissao()->delete();
        }

        $lead->update([
            'status_id' => $etapa_id
        ]);

        $lead->refresh();
        
        activity('lead_usuario')
                ->performedOn($lead)
                ->causedBy(auth()->user())
                ->withProperties([
                    'de' => Status_Crm::find($oldEtapa_id)->nome,
                    'para' => $lead->status->nome
                ])
                ->log("Mudou o status do lead de '{$oldEtapa_nome}' para '{$lead->status->nome}'");

        if($lead->status->slug == 'ganho'){
            (new \App\Services\ComissaoVendedorService())->createComissao($lead);
            session()->flash('showInsertVendaModal', true);
            session()->flash('lead_id_venda', ['lead_nome' => $lead->cliente->nome, 'lead_id' => $lead->id]);
        }

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

    /* PARTE DE COMISSÕES */
    /* ================== */
    /**
     * Retorna a view de percentuais de comissão
     */
    public function readPercentuaisComissao(){
        $percentuais = Percentuais_Comissao::all();
        return view('Crm/CRM_percentuais-comissao', ['percentuais' => $percentuais]);
    }

    public function updateComissaoPersonalizada(Request $request){
        $lead = Lead::findOrFail($request->lead_id);

        $lead->update([
            'comissao_personalizada' => $request->comissao_personalizada
        ]);

        if($lead->valor_definido !== null){
            $this->setComissaoEstipulada($lead);
            $this->setRetornoEmpresa($lead);
        }

        return redirect()->route('show.CRM');
    }

    /**
     * Dá update no percentual de comissão dado o percentual_id
     * @param Request $request
     */
    public function updatePercentualComissao(Request $request){
        $percentual = Percentuais_Comissao::findOrFail($request->percentual_id);
        $percentual->update([
            'percentual' => $request->percentual
        ]);
        return redirect()->route('read.percentuais-comissao');
    }

    public function showParcelasComissao($comissao_id){
        $comissao = Comissoes::findOrFail($comissao_id);
        return view('/Crm/CRM_parcelas-comissao', ['comissao' => $comissao]);
    }

    public function updateParcelaComissao(Request $request, $id){
        $parcela = Parcelas_Comissao::findOrFail($id);

        $parcela->update([
            'status' => $request->status
        ]);

        return redirect()->route('read.parcelas', $parcela->comissao->id);
    }

    /**
     * Exibe uma lista paginada de comissões com filtros opcionais.
     *
     * Filtros disponíveis via query string:
     * - periodo (formato: "YYYY-MM"): filtra comissões pelo ano e mês de criação.
     * - status: filtra comissões pelo status (ex: "pendente", "pago").
     * - cliente: filtra comissões com base no nome do cliente relacionado ao lead.
     *
     * Aplica paginação com 10 resultados por página e mantém os filtros na URL com `appends`.
     *
     * @return \Illuminate\View\View A view 'Crm/CRM_comissoes' com as comissões filtradas.
     */
    public function readComissoes(){
        $comissoes = Comissoes::query();

        if(!empty(request('periodo'))){
            $periodo = request('periodo');
            [$year, $month] = explode('-', $periodo);
            $comissoes->whereYear('created_at', $year)
                                    ->whereMonth('created_at', $month);
        }

        if(!empty(request('status'))){
            $status = request('status');
            $comissoes->where('status', $status);
        }

        if(!empty(request('cliente'))){
            $cliente = request('cliente');
            $comissoes->whereHas('lead.cliente', function ($query) use ($cliente) {
                $query->where('nome', 'like', "%{$cliente}%");
            });
        }

        $comissoes = $comissoes->paginate(10)->appends(request()->query());
        return view('Crm/CRM_comissoes', ['comissoes' => $comissoes]);
    }

    /**
     * Atualiza o status de uma comissão específica.
     *
     * @param int $comissao_id O ID da comissão a ser atualizada.
     * @param string $status O novo status a ser atribuído (ex: "pago", "pendente").
     *
     * @return \Illuminate\Http\RedirectResponse Redireciona de volta para a rota de listagem das comissões.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se a comissão não for encontrada.
     */
    public function updateStatusComissao($comissao_id, $status){
        $comissao = Comissoes::findOrFail($comissao_id);

        $comissao->update([
            'status' => $status
        ]);

        return redirect()->route('read.comissoes');
    }
    /* FIM DE COMISSÕES */
    /* ================== */

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

    public function updateInvestimentoLead(Request $request){
        $lead = Lead::findOrFail($request->lead_id);

        $oldLeadInvestimento = $lead->valor_definido;
        $lead->update([
            'valor_definido' => $request->investimento,
            'num_parcelas' => $request->num_parcelas
        ]);

        $lead->refresh();

        activity('lead_usuario')
                ->performedOn($lead)
                ->causedBy(auth()->user())
                ->withProperties([
                    'de' => $oldLeadInvestimento,
                    'para' => $lead->valor_definido
                ])
        ->log("Alterado valor de investimento de R$ {$oldLeadInvestimento} para R$ {$lead->valor_definido}");

        $this->setComissaoEstipulada($lead);
        $this->setRetornoEmpresa($lead);

        session()->flash('mensagem', 'Investimento definido com sucesso');

        return redirect()->route('show.CRM');
    }

    private function setComissaoEstipulada($lead){
        $percentuais = Percentuais_Comissao::all();
        
        if($lead->comissao_personalizada !== null){
            $porcentagemTotal = $lead->comissao_personalizada;
            return $lead->update([
                'comissao_estipulada' => $lead->valor_definido * ($porcentagemTotal/100),
            ]);
        }

        foreach($percentuais as $value){
            $porcentagemTotal = $value->percentual;
            if($lead->cliente->tipo_cliente === $value->tipo_cliente){
                return $lead->update([
                    'comissao_estipulada' => $lead->valor_definido * ($porcentagemTotal/100),
                ]);
            }
        }
    }
    
    private function setRetornoEmpresa($lead){
        $precificacao = Variaveis_Precificacao::where('nome', 'Imposto')->first();

        if(!$precificacao){
            return null;
        }

        $total = ($lead->valor_definido ?? 0) * ((100 - $precificacao->valor) / 100) - ($lead->comissao_estipulada ?? 0);

        return $lead->update(['retorno_empresa' => $total]);
    }
}
