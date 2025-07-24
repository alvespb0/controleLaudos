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

        Lead::create([
            'cliente_id' => $request->cliente_id,
            'vendedor_id' => $user->comercial->id ?? null,
            'status_id' => $request->status_id,
            'observacoes' => $request->observacoes,
            'nome_contato' => $request->nome_contato,
            'investimento' => $request->investimento,
            'proximo_contato' => $request->proximo_contato
        ]);

       session()->flash('mensagem', 'Lead criado com sucesso');

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

        $lead->update([
            'cliente_id' => $request->cliente_id,
            'vendedor_id' => $user->comercial->id ?? null,
            'status_id' => $request->status_id,
            'observacoes' => $request->observacoes,
            'nome_contato' => $request->nome_contato,
            'investimento' => $request->investimento,
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
}
