<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClienteMail;

use Illuminate\Http\Request;
use App\Http\Requests\LaudoRequest; 
use App\Http\Requests\LaudoUpdateRequest; 
use App\Http\Requests\UpdateKanbanRequest; 

use App\Models\Laudo;
use App\Models\Cliente;
use App\Models\Op_Comercial;
use App\Models\Op_Tecnico;
use App\Models\Status;
use App\Models\File;
use Illuminate\Support\Facades\DB;


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
     * recebe um Nome ou CNPJ para filtrar na tabela de laudos
     * @param Request $request 
     * @return Array
     */
    public function filterCliente(Request $request){
        $termo = $request->input('cliente');

        $laudos = Laudo::with('cliente')
            ->when($termo, function ($query, $termo) {
                $query->whereHas('cliente', function ($q) use ($termo) {
                    $q->where('nome', 'like', "%$termo%")
                    ->orWhere('cnpj', 'like', "%$termo%");
                });
            })
            ->paginate(10);

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
    
        $laudosFiltrados = clone $laudos;

        // Calcula indicadores com base na query filtrada
        $contagemPorStatus = [];

        foreach ($status as $s) {
            $contagemPorStatus[$s->id] = (clone $laudosFiltrados)->where('status_id', $s->id)->count();
        }

        $semStatusCount = (clone $laudosFiltrados)->whereNull('status_id')->count();

        $status->push((object)[
            'id' => 'sem_status',
            'nome' => 'Sem status',
            'cor' => '#6c757d'
        ]);

        $contagemPorStatus['sem_status'] = $semStatusCount;
        
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

        $laudo = Laudo::findOrFail($request->laudo_id);

        $laudo->update([
            'data_conclusao' => $request->dataConclusao,
            'status_id' => $request->status,
            'tecnico_id' => $request->tecnicoResponsavel,
            'observacao' => $request->observacao,
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

    /* PARTE DE KANBAN */
        
    /**
     * retorna a pagina index levando todos os laudos, status e tecnicos de segurança
     * @return View
     */
    public function showKanban(){
        $laudos = Laudo::orderBy('position', 'asc')->orderBy('created_at', 'desc')->get();
        $status = Status::all();
        $tecnicos = Op_Tecnico::all();

        return view("kanban", ["laudos"=> $laudos, "status" => $status, "tecnicos"=> $tecnicos]);
    }

    /**
     * Atualiza um laudo no Kanban
     * Esta função é responsável por:
     * 1. Validar os dados recebidos
     * 2. Atualizar a posição do card movido
     * 3. Ajustar as posições dos outros cards afetados
     * 4. Atualizar o status e outros dados do laudo
     * 
     * @param UpdateKanbanRequest $request - Request contendo os dados do laudo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLaudoKanban(UpdateKanbanRequest $request){
        $request->validated();

        try {
            # Inicia uma transação no banco de dados
            # Isso garante que todas as operações sejam feitas ou nenhuma seja
            DB::beginTransaction();

            $laudo = Laudo::findOrFail($request->laudo_id);
            
            # Pega as informações antigas com o select acima, e as informações novas do request, para comparar
            $oldPosition = $laudo->position;
            $oldStatus = $laudo->status_id;
            $newPosition = $request->position;
            $newStatus = $request->status;

            # Verifica se houve mudança de status (troca de colunas), ou mudança de posições (dentro da mesma coluna)
            if ($oldStatus !== $newStatus || $oldPosition !== $newPosition) {
                # A condição verifica se houve troca de posição ENTRE colunas, necessário para alterar as positions da coluna antiga
                if ($oldStatus !== $newStatus) {
                    # faz o incremento de + 1 de os cards abaixo da coluna antiga. básicamente, se card X de posição 4 é transferido para outra coluna
                    # o card 5, 6 e assim sucessivamente (da coluna antiga) vão passar a ser os cards 4, 5 etc.
                    Laudo::where('status_id', $oldStatus)
                        ->where('position', '>', $oldPosition)
                        ->increment('position');
                }

                # Ajusta as posições na nova coluna
                if ($newPosition) {
                    # faz o incremento de + 1 em todos os cards abaixo do card mexido (na coluna nova). Imagine que se o card x é colocado numa posição 6 que era 
                    # ocupado por card Y, o card Y precisa mudar para 7, o que era 7 vai para 8 e assim sucessivamente.
                    Laudo::where('status_id', $newStatus)
                        ->where('position', '>=', $newPosition)
                        ->where('id', '!=', $laudo->id)
                        ->increment('position');
                }
            }

            # da update no banco dado os novos parâmetros
            $laudo->update([
                'data_conclusao' => $request->dataConclusao,
                'status_id' => $newStatus,
                'tecnico_id' => $request->tecnicoResponsavel,
                'position' => $newPosition
            ]);

            DB::commit(); # se Tudo tiver dado certo, vai salvar no banco, se não, vai cair na exception e vai dar roll back

            return response()->json(['message' => 'Laudo Atualizado com sucesso']);
        } catch (\Exception $e) {
            # Se algo der errado, desfaz todas as operações
            DB::rollBack();
            
            # Registra o erro no log
            \Log::error('Erro ao atualizar laudo:', [
                'error' => $e->getMessage(),
                'laudo_id' => $request->laudo_id
            ]);
            
            return response()->json(['message' => 'Erro ao atualizar laudo'], 500);
        }
    }

    /**
     * Atualiza todas as posições dos laudos no Kanban
     * Esta função é chamada pelo botão "Atualizar Posições" e:
     * 1. Verifica se o usuário é admin
     * 2. Atualiza todas as posições em uma única transação
     * 3. Atualiza também o status de cada laudo
     * 
     * @param Request $request - Request contendo um array de posições
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAllPositions(Request $request){
        try {
            // Pega o array de posições do request
            $positions = $request->input('positions');
            
            // Inicia uma transação no banco de dados
            DB::beginTransaction();
            
            // Atualiza cada laudo com sua nova posição e status
            foreach ($positions as $position) {
                Laudo::where('id', $position['laudo_id'])
                    ->update([
                        'position' => $position['position'],
                        'status_id' => $position['status'] ?: null
                    ]);
            }
            
            // Confirma todas as operações
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Posições atualizadas com sucesso'
            ]);
            
        } catch (\Exception $e) {
            // Se algo der errado, desfaz todas as operações
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar posições: ' . $e->getMessage()
            ], 500);
        }
    }

}
