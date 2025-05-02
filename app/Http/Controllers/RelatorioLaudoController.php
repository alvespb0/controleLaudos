<?php

namespace App\Http\Controllers;

use App\Exports\LaudosExport;
use Maatwebsite\Excel\Facades\Excel;

class RelatorioLaudoController extends Controller
{
    public function exportar()
    {
        return Excel::download(new LaudosExport, 'relatorio_laudos.xlsx');
    }
}
