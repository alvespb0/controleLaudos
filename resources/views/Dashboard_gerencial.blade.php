@extends('templateMain')
@section('content')

<div class="container mt-4">
    <h4 class="text-center mb-4">Indicadores de Laudos</h4>

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
                <h5 class="text-center mb-3">Laudos por técnico</h5>
                <div style="height: 300px;">
                    {!! $chartTecnico->container() !!}
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

    </div>

    {{-- Carrega Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! $chartStatus->script() !!}
    {!! $chartTecnico->script() !!}
    {!! $chartVendedor->script() !!}
    {!! $chartClientes->script() !!}
    {!! $chartOrcamentos->script() !!}
</div>

@endsection
