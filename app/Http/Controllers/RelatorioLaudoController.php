<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LaudosExport;
use App\Exports\ClientesExport;
use App\Exports\DocumentoExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Status;
use App\Models\Cliente;

class RelatorioLaudoController extends Controller
{
    /**
     * retorna a view da página de selecionar o tipo de relatório
     */
    public function tipoRelatorio(){
        $status = Status::all();
        $clientes = Cliente::all();
        return view('Relatorios/Relatorios_unificado', ['clientes' => $clientes, 'status' => $status]);
    }

    /**
     * recebe via método POST um select tipoRelatorio retorna a view de solicitação de relatório, dado esse parâmetro
     * @param string tipoRelatorio
     * @return view
     */
    public function requestTipoRelatorio(Request $request){
        $tipoRelatorio = $request->tipoRelatorio;
        $status = Status::all();
        $clientes = Cliente::all();
        return view('Relatorios/Relatorios_new', ['tipoRelatorio' => $tipoRelatorio, 'status'=> $status, 'clientes'=> $clientes]);
    }

    /**
     * recebe os parâmetros do relatório via POST e chama o consturtor da classe LaudoExport
     * @param Request
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
        }
    }

    public function exportar(){
        return Excel::download(new LaudosExport, 'relatorio_laudos.xlsx');
    }
}
