@extends('templateMain')

@section('content')
<div class="card shadow rounded p-4">
    <h3 class="mb-4"><i class="bi bi-graph-up"></i> Solicitar Relatórios</h3>
@php echo $tipoRelatorio @endphp
    <form action="#" method="POST">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tipoRelatorio" class="form-label">Tipo de Relatório</label>
                <select class="form-select" id="tipoRelatorio" name="tipoRelatorio" disabled>
                    <option selected disabled>Selecione...</option>
                    <option value="laudos" {{$tipoRelatorio == 'laudos' ? 'selected' : ''}}>Laudos</option>
                    <option value="clientes" {{$tipoRelatorio == 'clientes' ? 'selected' : ''}}>Clientes</option>
                    <option value="operadores" {{$tipoRelatorio == 'operadores' ? 'selected' : ''}}>Operadores</option>
                    <option value="status" {{$tipoRelatorio == 'status' ? 'selected' : ''}}>Status</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="dataInicio" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="dataInicio" name="dataInicio">
            </div>

            <div class="col-md-4">
                <label for="dataFim" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="dataFim" name="dataFim">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="cliente" class="form-label">Cliente (opcional)</label>
                <input type="text" class="form-control" id="cliente" name="cliente" placeholder="Nome do cliente">
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Status (opcional)</label>
                <select class="form-select" id="status" name="status">
                    <option selected disabled>Selecione...</option>
                    <option value="pendente">Pendente</option>
                    <option value="concluido">Concluído</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-printer"></i> Gerar Relatório
            </button>
        </div>
    </form>
</div>

@endsection
