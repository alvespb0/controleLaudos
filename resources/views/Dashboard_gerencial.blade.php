  @extends('templateMain')
@section('content')

<div class="container mt-4">
    <h4 class="text-center mb-4">Laudos por Status</h4>

    <div class="d-flex justify-content-center">
        <div style="max-width: 500px; width: 100%;">
            {!! $chartStatus->container() !!}
        </div>
    </div>
</div>
    {{-- Carrega Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! $chartStatus->script() !!}
@endsection