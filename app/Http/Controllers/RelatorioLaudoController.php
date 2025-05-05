<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LaudosExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Status;
use App\Models\Cliente;

class RelatorioLaudoController extends Controller
{
    /**
     * retorna a view da página de selecionar o tipo de relatório
     */
    public function tipoRelatorio(){
        return view('Relatorios/Relatorios_new0');
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
        }
    }

    public function exportar(){
        return Excel::download(new LaudosExport, 'relatorio_laudos.xlsx');
    }
}
