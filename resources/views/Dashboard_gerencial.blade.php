@extends('templateMain')
@section('content')

<div class="container mt-4">
    <h4 class="text-center mb-4">Indicadores de Laudos</h4>

    <div class="row justify-content-center">
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div style="max-width: 400px; width: 100%;">
                {!! $chartStatus->container() !!}
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div style="max-width: 400px; width: 100%;">
                {!! $chartTecnico->container() !!}
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div style="max-width: 400px; width: 100%;">
                {!! $chartVendedor->container() !!}
            </div>
        </div>
    </div>

    {{-- Carrega Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! $chartStatus->script() !!}
    {!! $chartTecnico->script() !!}
    {!! $chartVendedor->script() !!}
</div>

@endsection
