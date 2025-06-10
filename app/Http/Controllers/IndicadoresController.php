<?php

namespace App\Http\Controllers;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;

use App\Models\Laudo;
use App\Models\Cliente;
use App\Models\Op_Comercial;
use App\Models\Op_Tecnico;
use App\Models\Status;
use App\Models\File;

class IndicadoresController extends Controller
{
    /**
     * recebe uma url via método get, que retorna os gráficos de indicadores com chart JS
     * @return view
     */
    public function dashboardGerencial(Request $request){
        $chartStatus = $this->indicadorLaudoStatus($request->dataInicial, $request->dataFinal);

        $chartTecnico = $this->indicadorLaudoPorTecnico($request->dataInicial, $request->dataFinal);

        $chartVendedor = $this->indicadorLaudoPorVendedor($request->dataInicial, $request->dataFinal);

        $chartClientes = $this->indicadorCliente($request->dataInicial, $request->dataFinal);

        $chartOrcamentos = $this->indicadorOrcamentos($request->dataInicial, $request->dataFinal);

        return view('Dashboard_gerencial', ['chartStatus' => $chartStatus, 'chartTecnico' => $chartTecnico, 'chartVendedor' => $chartVendedor, 
                    'chartClientes' => $chartClientes, 'chartOrcamentos' => $chartOrcamentos]);
    }


    private function indicadorLaudoStatus($dataInicio = null, $dataFim = null){
        /* LAUDOS POR STATUS */
        $statusList = Status::withCount(['laudos' => function ($query) use ($dataInicio, $dataFim) {
            if ($dataInicio) {
                $query->whereDate('data_aceite', '>=', $dataInicio);
            }
            if ($dataFim) {
                $query->whereDate('data_aceite', '<=', $dataFim);
            }
        }])->get();

        $labels = $statusList->pluck('nome')->toArray();
        $data   = $statusList->pluck('laudos_count')->toArray();
        $colors = $statusList->pluck('cor')->toArray();          

        $chartStatus = new Chart;
        $chartStatus->labels($labels);
        $chartStatus->dataset('Laudos por status', 'pie', $data)
                    ->backgroundColor($colors);

        return $chartStatus;
    }

    private function indicadorLaudoPorTecnico($dataInicio = null, $dataFim = null){
        /* LAUDOS POR TÉCNICO RESPONSÁVEL */
        $tecnicosList = Op_Tecnico::withCount(['laudos' => function ($query) use ($dataInicio, $dataFim) {
            if ($dataInicio) {
                $query->whereDate('data_aceite', '>=', $dataInicio);
            }
            if ($dataFim) {
                $query->whereDate('data_aceite', '<=', $dataFim);
            }
        }])->get();

        $labelsTecnico = $tecnicosList->pluck('usuario');
        $dataTecnico = $tecnicosList->pluck('laudos_count');

        $chartTecnico = new Chart;
        $chartTecnico->labels($labelsTecnico);
        $chartTecnico->dataset('Laudos por técnico', 'bar', $dataTecnico);

        return $chartTecnico;
    }

    private function indicadorLaudoPorVendedor($dataInicio = null, $dataFim = null){
        /* LAUDOS POR VENDEDOR */
        $vendedorList = Op_Comercial::withCount(['laudos' => function ($query) use ($dataInicio, $dataFim) {
            if ($dataInicio) {
                $query->whereDate('data_aceite', '>=', $dataInicio);
            }
            if ($dataFim) {
                $query->whereDate('data_aceite', '<=', $dataFim);
            }
        }])->get();

        $labelsVendedor = $vendedorList->pluck('usuario');
        $dataVendedor = $vendedorList->pluck('laudos_count');

        $chartVendedor = new Chart;
        $chartVendedor->labels($labelsVendedor);
        $chartVendedor->dataset('Laudos por vendedor', 'doughnut', $dataVendedor);

        return $chartVendedor;
    }

    private function indicadorCliente($dataInicio = null, $dataFim = null){
        /* CLIENTES NOVOS X RENOVAÇOES */
        $query = Cliente::query();
        if ($dataInicio) {
            $query->whereDate('updated_at', '>=', $dataInicio);
        }
        if ($dataFim) {
            $query->whereDate('updated_at', '<=', $dataFim);
        }

        $clientes = $query->selectRaw("DATE_FORMAT(updated_at, '%m/%y') as mes, cliente_novo, COUNT(*) as total")
                        ->groupBy('mes', 'cliente_novo')
                        ->orderByRaw("STR_TO_DATE(mes, '%m/%y') ASC")
                        ->get();

        $labels = [];
        $dadosNovos = [];
        $dadosRenovados = [];

        $mesesUnicos = $clientes->pluck('mes')->unique();

        foreach ($mesesUnicos as $mes) {
            $labels[] = $mes;
            $novos = $clientes->where('mes', $mes)->where('cliente_novo', 1)->first();
            $renov = $clientes->where('mes', $mes)->where('cliente_novo', 0)->first();

            $dadosNovos[] = $novos ? $novos->total : 0;
            $dadosRenovados[] = $renov ? $renov->total : 0;
        }
        $chartClientes = new Chart;
        $chartClientes->labels($labels);
        $chartClientes->dataset('Clientes Novos', 'bar', $dadosNovos)
            ->backgroundColor('#79c5b6');

        $chartClientes->dataset('Renovações', 'bar', $dadosRenovados)
            ->backgroundColor('#5c9c90');

        return $chartClientes;
    }

    private function indicadorOrcamentos($dataInicio = null, $dataFim = null){
        /* ORÇAMENTOS ENVIADOS POR MÊS */
        $query = File::selectRaw("DATE_FORMAT(data_referencia, '%Y-%m') as mes, COUNT(*) as total")
            ->where('tipo', 'orcamento');

        // Aplica filtro por data de referência, se houver
        if ($dataInicio && $dataFim) {
            $query->whereBetween('data_referencia', [$dataInicio, $dataFim]);
        }

        $orcamentosPorMes = $query->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $labelsOrcamento = [];
        $valoresOrcamento = [];

        foreach ($orcamentosPorMes as $registro) {
            $labelsOrcamento[] = Carbon::createFromFormat('Y-m', $registro->mes)->translatedFormat('F Y');
            $valoresOrcamento[] = $registro->total;
        }

        $chartOrcamentos = new Chart;
        $chartOrcamentos->labels($labelsOrcamento);
        $chartOrcamentos->dataset('Orçamentos por Mês', 'bar', $valoresOrcamento)
            ->backgroundColor('rgba(54, 162, 235, 0.7)');

        return $chartOrcamentos;
    }
}
