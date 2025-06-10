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
        $chartStatus = $this->indicadorLaudoStatus();

        $chartTecnico = $this->indicadorLaudoPorTecnico();

        $chartVendedor = $this->indicadorLaudoPorVendedor();

        $chartClientes = $this->indicadorCliente();

        $chartOrcamentos = $this->indicadorOrcamentos();

        return view('Dashboard_gerencial', ['chartStatus' => $chartStatus, 'chartTecnico' => $chartTecnico, 'chartVendedor' => $chartVendedor, 
                    'chartClientes' => $chartClientes, 'chartOrcamentos' => $chartOrcamentos]);
    }


    private function indicadorLaudoStatus(){
        /* LAUDOS POR STATUS */
        $statusList = Status::withCount('laudos')->get();

        $labels = $statusList->pluck('nome')->toArray();
        $data   = $statusList->pluck('laudos_count')->toArray(); # usa a relação para fazer uma coluna temporária e retornar a count
        $colors = $statusList->pluck('cor')->toArray();          

        $chartStatus = new Chart;
        $chartStatus->labels($labels);
        $chartStatus->dataset('Laudos por status', 'pie', $data)
                    ->backgroundColor($colors);

        return $chartStatus;
    }

    private function indicadorLaudoPorTecnico(){
        /* LAUDOS POR TÉCNICO RESPONSÁVEL */
        $tecnicosList = Op_Tecnico::withCount('laudos')->get();

        $labelsTecnico = $tecnicosList->pluck('usuario');
        $dataTecnico = $tecnicosList->pluck('laudos_count');

        $chartTecnico = new Chart;
        $chartTecnico->labels($labelsTecnico);
        $chartTecnico->dataset('Laudos por técnico', 'bar', $dataTecnico);

        return $chartTecnico;
    }

    private function indicadorLaudoPorVendedor(){
        /* LAUDOS POR VENDEDOR */
        $vendedorList = Op_Comercial::withCount('laudos')->get();

        $labelsVendedor = $vendedorList->pluck('usuario');
        $dataVendedor = $vendedorList->pluck('laudos_count');

        $chartVendedor = new Chart;
        $chartVendedor->labels($labelsVendedor);
        $chartVendedor->dataset('Laudos por vendedor', 'doughnut', $dataVendedor);

        return $chartVendedor;
    }

    private function indicadorCliente(){
        /* CLIENTES NOVOS X RENOVAÇOES */
        $clientesNovos = Cliente::where('cliente_novo', 1)->count();
        $clientesRenovacoes = Cliente::where('cliente_novo', 0)->count();

        $numClientes = [$clientesNovos, $clientesRenovacoes];

        $chartClientes = new Chart;
        $chartClientes->labels(['Clientes Novos', 'Renovações']);
        $chartClientes->dataset('Clientes', 'bar', [$clientesNovos, $clientesRenovacoes])
              ->backgroundColor(['#79c5b6', '#5c9c90']);

        return $chartClientes;
    }

    private function indicadorOrcamentos(){
        /*  ORÇAMENTOS ENVIADOS POR MES */
        $orcamentosPorMes = File::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total")
                ->where('tipo', 'orcamento')
                ->groupBy('mes')
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
