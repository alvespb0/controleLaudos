@extends('templateMain')
@section('content')

<div class="container mt-4">
    <h4 class="text-center mb-4">Indicadores de Laudos</h4>

    <div class="row justify-content-center">
        {{-- Gráfico 1 --}}
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div style="max-width: 400px; width: 100%;">
                {!! $chartStatus->container() !!}
            </div>
        </div>

        {{-- Gráfico 2 --}}
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div style="max-width: 400px; width: 100%;">
                {!! $chartTecnico->container() !!}
            </div>
        </div>

        {{-- Gráfico 3 (exemplo extra, pode remover se tiver só dois) --}}
        @if(isset($chartOutro))
        <div class="col-md-4 d-flex justify-content-center mb-4">
            <div style="max-width: 400px; width: 100%;">
                {!! $chartOutro->container() !!}
            </div>
        </div>
        @endif
    </div>

    {{-- Carrega Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! $chartStatus->script() !!}
    {!! $chartTecnico->script() !!}
    @if(isset($chartOutro))
        {!! $chartOutro->script() !!}
    @endif
</div>

@endsection
