<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LaudosExport;
use Maatwebsite\Excel\Facades\Excel;

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
        return view('Relatorios/Relatorios_new', ['tipoRelatorio' => $tipoRelatorio]);
    }

    public function exportar(){
        return Excel::download(new LaudosExport, 'relatorio_laudos.xlsx');
    }
}
