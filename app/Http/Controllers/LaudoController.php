<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClienteMail;

use Illuminate\Http\Request;
use App\Http\Requests\LaudoRequest; 

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
        $clientes = Cliente::orderBy('nome', 'asc')->get();
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
     * retorna a pagina index levando todos os laudos, status e tecnicos de segurança
     * @return View
     */
    public function showDashboard(){
        $laudos = Laudo::orderBy('created_at', 'desc')->paginate(6);
        $status = Status::all();

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

        return view("index", ["status" => $status, "contagemPorStatus" => $contagemPorStatus]);
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

}
