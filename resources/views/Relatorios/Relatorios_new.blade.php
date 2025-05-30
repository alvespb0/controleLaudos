@extends('templateMain')

@section('content')
<div class="card shadow rounded p-4">
    <h3 class="mb-4"><i class="bi bi-graph-up"></i> Solicitar Relatórios</h3>
@php echo $tipoRelatorio @endphp
    <form action="{{route('gerar.relatorio')}}" method="POST">
        @csrf
        <input type="hidden" name="tipoRelatorio" value="{{ $tipoRelatorio }}">

        <div class="row mb-3">
            <div class="col-12">
                <label for="tipoRelatorio" class="form-label">Tipo de Relatório</label>
                <select class="form-select" id="tipoRelatorio" disabled>
                    <option selected disabled>Selecione...</option>
                    <option value="laudos" {{$tipoRelatorio == 'laudos' ? 'selected' : ''}}>Laudos</option>
                    <option value="clientes" {{$tipoRelatorio == 'clientes' ? 'selected' : ''}}>Clientes</option>
                </select>
            </div>


        </div>

        @if($tipoRelatorio == 'laudos')

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="dataInicio" class="form-label">Data de Conclusão (inicial)</label>
                <input type="date" class="form-control" id="dataInicio" name="dataInicio">
            </div>
            <div class="col-md-3">
                <label for="dataFim" class="form-label">Data de Conclusão (final)</label>
                <input type="date" class="form-control" id="dataFim" name="dataFim">
            </div>
            <div class="col-md-3">
                <label for="dataFim" class="form-label">Data de Aceite de Contrato (inicial)</label>
                <input type="date" class="form-control" id="dataAceiteInicio" name="dataAceiteInicio">
            </div>
            <div class="col-md-3">
                <label for="dataFim" class="form-label">Data de Aceite de Contrato (final)</label>
                <input type="date" class="form-control" id="dataAceiteFim" name="dataAceiteFim">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="cliente" class="form-label">Cliente (opcional)</label>
                <select name="cliente" class = "form-select" id="">
                    <option selected disabled>Selecione um cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{$cliente->id}}">{{$cliente->nome}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Status (opcional)</label>
                <select class="form-select" id="status" name="status">
                    <option selected disabled>Selecione um status</option>
                    @foreach($status as $s)
                        <option value="{{$s->id}}">{{$s->nome}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        @if($tipoRelatorio == 'clientes')

        <div class="mb-3">
            <div class="mb-3">
                <label for="nomeCliente" class="form-label">Nome do Cliente</label>
                <input type="text" name="nomeCliente" id="nomeCliente" class="form-control">
            </div>

            <div class="mb-3">
                <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" name="cnpj" id="cnpj" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Cliente é novo ou renovado?</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cliente_novo" id="cliente_novo" value="1">
                    <label class="form-check-label" for="cliente_novo">Cliente Novo</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cliente_novo" id="cliente_renovacao" value="0">
                    <label class="form-check-label" for="cliente_renovacao">Cliente Renovado</label>
                </div>
            </div>
        </div>
        @endif
        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-printer"></i> Gerar Relatório
            </button>
        </div>
    </form>
</div>

@endsection
