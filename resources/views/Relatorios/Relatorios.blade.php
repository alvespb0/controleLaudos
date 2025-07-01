@extends('templateMain')

@section('content')
<div class="card shadow rounded p-4">
    <h3 class="mb-4"><i class="bi bi-graph-up"></i> Solicitar Relatórios</h3>
    <form id="formTipoRelatorio" class="mb-4">
        <div class="row mb-3">
            <div class="col-12">
                <label for="tipoRelatorio" class="form-label">Tipo de Relatório</label>
                <select class="form-select" id="tipoRelatorioSelect" required>
                    <option value="" selected disabled>Selecione...</option>
                    <option value="laudos">Laudos</option>
                    <option value="clientes">Clientes</option>
                    <option value="documentos">Documentos</option>
                </select>
            </div>
        </div>
    </form>
    <form id="formLaudos" action="{{route('gerar.relatorio')}}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="tipoRelatorio" value="laudos">
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
                <label for="dataAceiteInicio" class="form-label">Data de Aceite de Contrato (inicial)</label>
                <input type="date" class="form-control" id="dataAceiteInicio" name="dataAceiteInicio">
            </div>
            <div class="col-md-3">
                <label for="dataAceiteFim" class="form-label">Data de Aceite de Contrato (final)</label>
                <input type="date" class="form-control" id="dataAceiteFim" name="dataAceiteFim">
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="cliente" class="form-label">Cliente (opcional)</label>
                <select name="cliente" class="form-select">
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
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" onclick="limparCampos(this.form)">
                <i class="bi bi-eraser"></i> Limpar
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-printer"></i> Gerar Relatório
            </button>
        </div>
    </form>
    <form id="formClientes" action="{{route('gerar.relatorio')}}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="tipoRelatorio" value="clientes">
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
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" onclick="limparCampos(this.form)">
                <i class="bi bi-eraser"></i> Limpar
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-printer"></i> Gerar Relatório
            </button>
        </div>
    </form>
    <form id="formDocumentos" action="{{route('gerar.relatorio')}}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="tipoRelatorio" value="documentos">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="dataElaboracaoInicio" class="form-label">Data de Solicitação (inicial)</label>
                <input type="date" class="form-control" id="dataElaboracaoInicio" name="dataElaboracaoInicio">
            </div>
            <div class="col-md-3">
                <label for="dataElaboracaoFim" class="form-label">Data de Solicitação (final)</label>
                <input type="date" class="form-control" id="dataElaboracaoFim" name="dataElaboracaoFim">
            </div>
            <div class="col-md-3">
                <label for="dataConclusaoInicio" class="form-label">Data de Conclusão (inicial)</label>
                <input type="date" class="form-control" id="dataConclusaoInicio" name="dataConclusaoInicio">
            </div>
            <div class="col-md-3">
                <label for="dataConclusaoFim" class="form-label">Data de Conclusão (final)</label>
                <input type="date" class="form-control" id="dataConclusaoFim" name="dataConclusaoFim">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="tipoDocumento" class="form-label">Tipo de Documento</label>
                <select class="form-select" id="tipoDocumento" name="tipoDocumento">
                    <option value="" selected disabled>Selecione o tipo</option>
                    <option value="ADENDO">ADENDO</option>
                    <option value="CAT">CAT</option>
                    <option value="PPP">PPP</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="statusDocumento" class="form-label">Status</label>
                <select class="form-select" id="statusDocumento" name="statusDocumento">
                    <option value="" selected disabled>Selecione um status</option>
                    @foreach($status as $s)
                        <option value="{{$s->id}}">{{$s->nome}}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="row mb-12">
            <div class="col-md-12">
                <label for="clienteDocumento" class="form-label">Cliente</label>
                <select name="clienteDocumento" class="form-select" id="clienteDocumento">
                    <option value="" selected disabled>Selecione um cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{$cliente->id}}">{{$cliente->nome}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <br>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" onclick="limparCampos(this.form)">
                <i class="bi bi-eraser"></i> Limpar
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-printer"></i> Gerar Relatório
            </button>
        </div>
    </form>   
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('tipoRelatorioSelect');
        const formLaudos = document.getElementById('formLaudos');
        const formClientes = document.getElementById('formClientes');
        const formDocumentos = document.getElementById('formDocumentos');

        select.addEventListener('change', function() {
            formLaudos.style.display = 'none';
            formClientes.style.display = 'none';
            formDocumentos.style.display = 'none';
            if (this.value === 'laudos') {
                formLaudos.style.display = 'block';
            } else if (this.value === 'clientes') {
                formClientes.style.display = 'block';
            } else if (this.value === 'documentos') {
                formDocumentos.style.display = 'block';
            }
        });
    });

    function limparCampos(form) {
        Array.from(form.elements).forEach(function(element) {
            if (element.type === 'text' || element.type === 'date' || element.tagName === 'SELECT') {
                element.value = '';
            }
            if (element.type === 'radio' || element.type === 'checkbox') {
                element.checked = false;
            }
        });
    }
</script>
@endsection 