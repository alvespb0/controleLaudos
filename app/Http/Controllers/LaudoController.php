<?php

namespace App\Http\Controllers;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClienteMail;

use Illuminate\Http\Request;
use App\Http\Requests\LaudoRequest; 
use App\Http\Requests\LaudoUpdateRequest; 

use App\Models\Laudo;
use App\Models\Cliente;
use App\Models\Op_Comercial;
use App\Models\Op_Tecnico;
use App\Models\Status;


class LaudoController extends Controller
{
    /**
     * Retorna a pagina de cadastro do Laudo junto dos clientes cadastrados e dos operadores comerciais cadastrados
     * @return View
     */
   public function cadastroLaudo(){
        $clientes = Cliente::all();
        $comercial = Op_Comercial::all();
        return view("Laudo/Laudo_new", ['clientes'=> $clientes, 'comerciais'=> $comercial]);
    }

    /**
     * Recebe uma Laudo Request, valida os dados, se validado salva no banco
     * Salvo apenas os dados que vieram no formulario, o restante NULL
     * @param LaudoRequest
     * @return Redirect
     */
    public function createLaudo(LaudoRequest $request){
        $request->validated();

        Laudo::create([
            'nome' => $request->nome,
            'data_previsao' => $request->dataPrevisao,
            'data_conclusao' => null,
            'data_fim_contrato' => $request->dataFimContrato,
            'data_aceite' => $request->dataAceiteContrato,
            'esocial' => $request->esocial,
            'numero_clientes' => $request->numFuncionarios,
            'tecnico_id' => null,
            'status_id' => null,
            'cliente_id' => $request->cliente,
            'comercial_id' => $request->comercial
        ]);

        session()->flash('mensagem', 'Laudo registrado com sucesso');

        return redirect()->route('readLaudo');
    }

    /**
    * retorna os laudos salvos no banco
    * @return Array
    */
    public function readLaudo(){
        $laudos = Laudo::orderBy('nome', 'asc')->paginate(10);
        return view('Laudo/Laudo_show', ['laudos'=> $laudos]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição do Laudo 
     * @param int $id
     * @return array
     */
    public function alteracaoLaudo($id){
        $laudo = Laudo::findOrFail($id);
        $clientes = Cliente::all();
        $comercial = Op_Comercial::all();
        return view('Laudo/Laudo_edit', ['laudo' => $laudo, 'clientes'=> $clientes, 'comerciais'=> $comercial]);
    }

    /**
     * Recebe uma request faz a validação dos dados e faz o update dado o id
     * @param LaudoRequest
     * @param int $id
     * @return Redirect
     */
    public function updateLaudo(LaudoRequest $request, $id){
        $request->validated();

        $laudo = Laudo::findOrFail($id);

        $laudo->update([
            'nome' => $request->nome,
            'data_previsao' => $request->dataPrevisao,
            'data_fim_contrato' => $request->dataFimContrato,
            'data_aceite' => $request->dataAceiteContrato,
            'esocial' => $request->esocial,
            'numero_clientes' => $request->numFuncionarios,
            'cliente_id' => $request->cliente,
            'comercial_id' => $request->comercial
        ]);

        session()->flash('mensagem', 'Laudo Alterado com sucesso');

        return redirect()->route('readLaudo');
    }

    /**
    * recebe o id e deleta o Laudo vinculado nesse ID
    * @param int $id
    * @return view
    */
    public function deleteLaudo($id){
        $laudo = Laudo::findOrFail($id);

        $laudo->update([
            'deleted_by' => Auth::user()->id
        ]);

        $laudo->delete();

        session()->flash('mensagem', 'Laudo excluido com sucesso');

        return redirect()->route('readLaudo');
    }


    /**
     * Retorna a view da 'lixeira' contendo os laudos deletados com softdelete
     * @return View
     */
    public function laudosExcluidos(){
        $laudosExcluidos = Laudo::onlyTrashed()->with('deletedBy')->orderByDesc('deleted_at')->paginate(10);

        return view('/Laudo/Laudo_deleted', ['laudosExcluidos' => $laudosExcluidos]);
    }
    
    /**
     * recebe um ID via get e restaura esse laudo excluído
     * @param int
     * @return view
     */
    public function restoreLaudo($id){
        $laudo = Laudo::withTrashed()->findOrFail($id);

        $laudo->restore();

        session()->flash('mensagem', 'Laudo restaurado com sucesso');

        return redirect()->route('readLaudo');
    }

    /**
     * Recebe uma solicitação GET com uma request de filtro
     * @param Request
     * @return View
     */
    public function filterDashboard(Request $request){

        $laudos = Laudo::query();
        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 

        /* Parte dos Indicadores */
        $contagemPorStatus = [];
        foreach ($status as $s) {
            $contagemPorStatus[$s->id] = Laudo::where('status_id', $s->id)->count();
        }
        $semStatusCount = Laudo::whereNull('status_id')->count();
        $status->push((object)[
            'id' => 'sem_status',
            'nome' => 'Sem status',
            'cor' => '#6c757d'
        ]);
        $contagemPorStatus['sem_status'] = $semStatusCount;
    
        /* Filtro de Search Cliente */
        if($request->filled('search')){
            $clientes = Cliente::where('nome', 'like', "%{$request->input('search')}%")->pluck('id');
            if($clientes->isNotEmpty()){
                $laudos = $laudos->whereIn('cliente_id', $clientes);
            }else{
                session()->flash('Error', 'Nenhum cliente localizado');
                return view("index", [
                    "laudos" => collect(),
                    "status" => $status,
                    "tecnicos" => $tecnicos
                ]);
            }
        }
    
        /* Filtro de Search Mes de competencia (pela data de aceite) */
        if($request->filled('mesCompetencia')){
            [$ano, $mes] = explode('-', $request->mesCompetencia);

            $laudos = $laudos->whereYear('data_aceite', $ano)
                             ->whereMonth('data_aceite', $mes);
        }

        /* Filtro de Search pelo status do laudo */
        if($request->filled('status')){
            if($request->status == "sem_status"){
                $laudos = $laudos->where('status_id', null);
            }else{
                $laudos = $laudos->where('status_id', $request->status);
            }
        }
    
        /* Filtro de Search pela data de conclusão (específica) */
        if($request->filled('dataConclusao')){
            $laudos = $laudos->where('data_conclusao', $request->dataConclusao);
        }
    
        /* Filtro pela ordenação */
        $ordem = $request->input('ordenarPor', 'mais_novos'); 

        if ($ordem === 'mais_antigos') {
            $laudos = $laudos->orderBy('created_at', 'asc');
        } else {
            $laudos = $laudos->orderBy('created_at', 'desc');
        }
    
        $laudos = $laudos->paginate(6)->appends($request->query());

        return view("index", [
            "laudos" => $laudos, 
            "status" => $status,
            "tecnicos" => $tecnicos,
            "contagemPorStatus" => $contagemPorStatus
        ]);
    }
    
    /**
     * retorna a pagina index levando todos os laudos, status e tecnicos de segurança
     * @return View
     */
    public function showDashboard(){
        $laudos = Laudo::orderBy('created_at', 'desc')->paginate(6);
        $status = Status::all();
        $tecnicos = Op_Tecnico::all();

        $contagemPorStatus = [];
        foreach ($status as $s) {
            $contagemPorStatus[$s->id] = Laudo::where('status_id', $s->id)->count();
        }
        $semStatusCount = Laudo::whereNull('status_id')->count();
        $status->push((object)[
            'id' => 'sem_status',
            'nome' => 'Sem status',
            'cor' => '#6c757d'
        ]);
        $contagemPorStatus['sem_status'] = $semStatusCount;

        return view("index", ["laudos"=> $laudos, "status" => $status, "tecnicos"=> $tecnicos, "contagemPorStatus" => $contagemPorStatus]);
    }

    /**
     * Recebe uma request válida os dados através do LaudoUpdateRequest e retorna um json
     * @param LaudoUpdateRequest
     * @return Json
     */
    public function updateLaudoIndex(LaudoUpdateRequest $request){
        $request->validated();

        \Log::info('Dados recebidos no updateLaudoIndex:', [
            'laudo_id' => $request->laudo_id,
            'status' => $request->status,
            'dataConclusao' => $request->dataConclusao,
            'tecnicoResponsavel' => $request->tecnicoResponsavel
        ]);

        $laudo = Laudo::findOrFail($request->laudo_id);

        $laudo->update([
            'data_conclusao' => $request->dataConclusao,
            'status_id' => $request->status,
            'tecnico_id' => $request->tecnicoResponsavel
        ]);

        \Log::info('Laudo atualizado com sucesso:', [
            'laudo_id' => $laudo->id,
            'novo_status' => $laudo->status_id
        ]);

        return response()->json(['message' => 'Laudo Atualizado com sucesso']);
    }

    /**
     * recebe uma request da index, contendo destinatario, subject, body e (non-required) files[] e envia o emailCli
     * @param Request $request
     * @return view
     */
    public function enviaEmailCli(Request $request){
        $files = [];
        $destinatario = $request->email;
        $subject = $request->assunto;
        $body = $request->body;
        
        if ($request->hasFile('anexos')) {
            foreach($request->file('anexos') as $file){
                $files[] = [
                    'content' => file_get_contents($file->getRealPath()),
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }
        $user = Auth::user(); 
        Mail::mailer('laudos')
            ->to($destinatario)
            ->send(new ClienteMail($body, $subject, $files, $user->email, $user->name));

        session()->flash('mensagem','Email Enviado com sucesso!');
        return redirect(route('dashboard.show'));
    }

    /**
     * recebe uma url via método get, que retorna os gráficos de indicadores com chart JS
     * @return view
     */
    public function dashboardGerencial(){
        /* LAUDOS POR STATUS */
        $statusList = Status::withCount('laudos')->get();

        $labels = $statusList->pluck('nome')->toArray();
        $data   = $statusList->pluck('laudos_count')->toArray(); # usa a relação para fazer uma coluna temporária e retornar a count
        $colors = $statusList->pluck('cor')->toArray();          

        $chartStatus = new Chart;
        $chartStatus->labels($labels);
        $chartStatus->dataset('Laudos por status', 'pie', $data)
                    ->backgroundColor($colors);

        /* lAUDOS POR TÉCNICO RESPONSÁVEL */
        $tecnicosList = Op_Tecnico::withCount('laudos')->get();

        $labelsTecnico = $tecnicosList->pluck('usuario');
        $dataTecnico = $tecnicosList->pluck('laudos_count');

        $chartTecnico = new Chart;
        $chartTecnico->labels($labelsTecnico);
        $chartTecnico->dataset('Laudos por técnico', 'bar', $dataTecnico);

        /* LAUDOS POR VENDEDOR */
        $vendedorList = Op_Comercial::withCount('laudos')->get();

        $labelsVendedor = $vendedorList->pluck('usuario');
        $dataVendedor = $vendedorList->pluck('laudos_count');

        $chartVendedor = new Chart;
        $chartVendedor->labels($labelsVendedor);
        $chartVendedor->dataset('Laudos por vendedor', 'doughnut', $dataVendedor);

        /* CLIENTES NOVOS X RENOVAÇOES */
        $clientesNovos = Cliente::where('cliente_novo', 1)->count();
        $clientesRenovacoes = Cliente::where('cliente_novo', 0)->count();

        $numClientes = [$clientesNovos, $clientesRenovacoes];

        $chartClientes = new Chart;
        $chartClientes->labels(['Clientes Novos', 'Renovações']);
        $chartClientes->dataset('Clientes', 'bar', [$clientesNovos, $clientesRenovacoes])
              ->backgroundColor(['#79c5b6', '#5c9c90']);

        return view('Dashboard_gerencial', ['chartStatus' => $chartStatus, 'chartTecnico' => $chartTecnico, 'chartVendedor' => $chartVendedor, 
                    'chartClientes' => $chartClientes]);
    }

        
    /**
     * retorna a pagina index levando todos os laudos, status e tecnicos de segurança
     * @return View
     */
    public function showKanban(){
        $laudos = Laudo::orderBy('created_at', 'desc')->get();
        $status = Status::all();
        $tecnicos = Op_Tecnico::all();

        return view("kanban", ["laudos"=> $laudos, "status" => $status, "tecnicos"=> $tecnicos]);
    }


}
