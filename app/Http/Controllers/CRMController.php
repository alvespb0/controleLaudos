<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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
        $leads = Lead::all();
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
            'vendedor_id' => $user->comercial->id,
            'status_id' => $request->status_id,
            'observacoes' => $request->observacoes,
            'proximo_contato' => $request->proximo_contato
        ]);

        return redirect()->route('show.CRM');
    }
}
