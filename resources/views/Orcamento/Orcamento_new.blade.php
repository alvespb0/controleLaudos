@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none;">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Gerar orçamento</h3>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{route('baixar.orcamento')}}" id="gerar" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="numProposta" class="form-label">Número da Proposta</label>
                    <input type="number" min=1 class="form-control" name="numProposta" id="numProposta">
                </div>
                <div class="mb-3">
                    <label for="razaoSocialCliente" class="form-label">Razão Social</label>
                    <input type="text" class="form-control" name="razaoSocialCliente" id="razaoSocialCliente" value="{{$cliente != null ? $cliente->nome : ''}}" {{$cliente != null ? 'readonly' : ''}}>
                </div>
                <div class="mb-3">
                    <label for="cnpjCliente" class="form-label">CNPJ</label>
                    <input type="text" class="form-control" name="cnpjCliente" id="cnpjCliente" value="{{$cliente != null ? $cliente->cnpj : ''}}" {{$cliente != null ? 'readonly' : ''}}>
                </div>
                <div class="mb-3">
                    <label for="telefoneCliente" class="form-label">Telefone Cliente</label>
                    <input type="text" class="form-control" name="telefoneCliente" id="telefoneCliente" value="{{$cliente && $cliente->telefone->first() ? $cliente->telefone->first()->telefone : '' }}" {{$cliente != null ? 'readonly' : ''}}>
                </div>
                <div class="mb-3">
                    <label for="emailCliente" class="form-label">Email Cliente</label>
                    <input type="text" class="form-control" name="emailCliente" id="emailCliente" value="{{$cliente != null ? $cliente->email : ''}}" {{$cliente != null ? 'readonly' : ''}}>
                </div>
                <div class="mb-3">
                    <label for="nomeContato" class="form-label">Nome do Contato</label>
                    <input type="text" name="nomeContato" id="nomeContato" class = "form-control" placeholder="Insira o nome do contato responsável">
                </div>
                <div class="mb-3">
                    <label for="numFuncionarios" class="form-label">Numero de Funcionários</label>
                    <input type="number" min=1 class="form-control" name="numFuncionarios" id="numFuncionarios">
                </div>
                <div class="mb-3">
                    <label for="investimento" class="form-label">Investimento</label>
                    <input type="number" min=1 step="0.01" class="form-control" name="investimento" id="investimento" placeholder="500.50">
                </div>
                <div class="mb-3">
                    <label for="parcelasTexto" class="form-label">Número de Parcelas</label>
                    <input type="number" min=1 class="form-control" name="parcelasTexto" id="parcelasTexto">
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Gerar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
