@extends('templateMain')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-end flex-wrap mb-4">
        <h3 class="m-0">Indicadores de Laudos</h4>
        <form action="{{ route('dashboard.indicadores') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3">
            <div style="width: 160px;">
                <label for="dataFilterInicial" class="form-label text-muted small mb-1">Data Inicial</label>
                <input type="date" class="form-control" id="dataFilterInicial" name="dataInicial">
            </div>
            <div style="width: 160px;">
                <label for="dataFilterFinal" class="form-label text-muted small mb-1">Data Final</label>
                <input type="date" class="form-control" id="dataFilterFinal" name="dataFinal">      
            </div>
            <div>
                <label class="form-label d-block invisible">Buscar</label>
                <button type="submit" class="btn btn-primary px-3 py-2 rounded-circle shadow-sm" style="background-color: var(--primary-color); border: none;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Laudos por status</h5>
                <div style="height: 300px;">
                    {!! $chartStatus->container() !!}
                </div>
            </div>
        </div>
    
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Responsáveis por Levantamentos</h5>
                <div style="height: 300px;">
                    {!! $chartTecnicoLevantamento->container() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Engenheiros Responsáveis</h5>
                <div style="height: 300px;">
                    {!! $chartEngenheiroResponsavel->container() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Responsáveis pela Digitação</h5>
                <div style="height: 300px;">
                    {!! $chartResponsavelDigitacao->container() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Laudos por vendedor</h5>
                <div style="height: 300px;">
                    {!! $chartVendedor->container() !!}
                </div>
            </div>
        </div>
        <div class="md2"></div>
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Documentos por status</h5>
                <div style="height: 300px;">
                    {!! $chartDocsStatus->container() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Documentos por status</h5>
                <div style="height: 300px;">
                    {!! $chartDocsTecnico->container() !!}
                </div>
            </div>
        </div>
        <div class="md2"></div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Leads ganhos por mês</h5>
                <div style="height: 300px;">
                    {!! $chartLeadsGanhosMes->container() !!}
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Clientes Novos X Renovações</h5>
                <div style="height: 300px;">
                    {!! $chartClientes->container() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Orçamentos por Mês</h5>
                <div style="height: 300px;">
                    {!! $chartOrcamentos->container() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div class="border p-3 shadow-sm rounded" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Simulação de Receita Lead</h5>
                <div style="height: 300px;">
                    {!! $chartValorTotalMes->container() !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Carrega Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! $chartStatus->script() !!}
    {!! $chartTecnicoLevantamento->script() !!}
    {!! $chartEngenheiroResponsavel->script() !!}
    {!! $chartResponsavelDigitacao->script() !!}
    {!! $chartVendedor->script() !!}
    {!! $chartClientes->script() !!}
    {!! $chartOrcamentos->script() !!}
    {!! $chartDocsStatus->script() !!}
    {!! $chartDocsTecnico->script() !!}
    {!! $chartLeadsGanhosMes->script() !!}
    {!! $chartValorTotalMes->script() !!}

</div>

@endsection
