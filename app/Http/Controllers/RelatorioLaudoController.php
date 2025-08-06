<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LaudosExport;
use App\Exports\ClientesExport;
use App\Exports\DocumentoExport;
use App\Exports\CrmExport;
use App\Exports\ComissaoMultiSheetExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Status;
use App\Models\Status_Crm;
use App\Models\Op_Comercial;
use App\Models\Cliente;

class RelatorioLaudoController extends Controller
{
    /**
     * retorna a view da página de selecionar o tipo de relatório
     */
    public function tipoRelatorio(){
        $status = Status::all();
        $clientes = Cliente::all();
        $vendedor = Op_Comercial::all();
        $status_crm = Status_Crm::all();
        return view('Relatorios/Relatorios', ['clientes' => $clientes, 'status' => $status, 'etapa_crm' => $status_crm, 'vendedores' => $vendedor]);
    }

    /**
     * recebe os parâmetros do relatório via POST e chama o consturtor da classe LaudoExport
     * @param Request $request
     * @return download
     */
    public function gerarRelatorio(Request $request){
        $tipo = $request->tipoRelatorio;
        if($tipo === 'laudos'){
            $filtros = [
                'dataInicio' => $request->dataInicio,
                'dataFim' => $request->dataFim,
                'dataAceiteInicio' => $request->dataAceiteInicio,
                'dataAceiteFim' => $request->dataAceiteFim,
                'cliente' => $request->cliente,
                'status' => $request->status
            ];
            return Excel::download(new LaudosExport($filtros), 'relatorio_laudos.xlsx');
        }elseif($tipo === 'clientes'){
            $filtros = [
                'nomeCliente' => $request->nomeCliente,
                'cnpjCliente' => $request->cnpj,
                'cliente_novo' => $request->cliente_novo 
            ];
            return Excel::download(new ClientesExport($filtros),'relatorio_clientes.xlsx');
        }elseif($tipo === 'documentos'){
            $filtros = [
                'dataElaboracaoInicio' => $request->dataElaboracaoInicio,
                'dataElaboracaoFim' => $request->dataElaboracaoFim,
                'dataConclusaoInicio' => $request->dataConclusaoInicio,
                'dataConclusaoFim' =>  $request->dataConclusaoFim,
                'tipoDocumento' => $request->tipoDocumento,
                'status' => $request->statusDocumento,
                'cliente' => $request->clienteDocumento
            ];
            return Excel::download(new DocumentoExport($filtros), 'relatorio_documentos.xlsx');
        }elseif($tipo === 'crm'){
            $filtros = [
                'dataInicio' => $request->dataInicioCRM,
                'dataFim' => $request->dataFimCRM,
                'vendedor' => $request->vendedorCRM, # ID
                'etapa' => $request->etapaCRM #id
            ];
            return Excel::download(new CrmExport($filtros), 'relatorio_CRM.xlsx');
        }elseif($tipo === 'comissoes'){
            $filtros = [
                'dataInicioComissoes' => $request->dataInicioComissoes,
                'dataFimComissoes' => $request->dataFimComissao,
                'statusComissao' => $request->statusComissao,
                'vendedorComissoes' => $request->vendedorComissoes
            ];
            return Excel::download(new ComissaoMultiSheetExport($filtros),'Relatorio_Comissoes.xlsx');

        }
    }

}
