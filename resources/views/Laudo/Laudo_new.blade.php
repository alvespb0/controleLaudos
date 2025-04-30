@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3 class="card-title mb-4 text-center text-dark">Cadastro de Laudo</h3>
            <form action="{{ route('create.laudo') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nome" class="form-label">Laudo</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="nome ou breve descrição do laudo" required>
                </div>

                <div class="mb-3">
                    <label for="dataPrevisao">Data de Previsao de Conclusao de Laudo <small class="text-muted">* Opcional</small></label>
                    <input type="date" name="dataPrevisao" id="dataPrevisao" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="dataAceiteContrato">Data de Aceite do Contrato</label>
                    <input type="date" name="dataAceiteContrato" id="dataAceiteContrato" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="dataFimContrato">Data de fim de contrato</label>
                    <input type="date" name="dataFimContrato" id="dataFimContrato" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cliente">Cliente</label>
                    <select name="cliente" id="cliente" class = "form-control" required>
                        <option selected>Selecione um cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nome}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block">Esocial?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="esocial" id="esocial_sim" value="1" required>
                        <label class="form-check-label" for="esocial_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="esocial" id="esocial_nao" value="0" required>
                        <label class="form-check-label" for="esocial_nao">Não</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="numFunc">Número de funcionários</label>
                    <input type="number" name="numFuncionarios" class="form-control" id="" placeholder="insira o numero de funcionários" min=1 required>
                </div>
                <div class="mb-3">
                    <label for="Vendedor">Vendedor</label>
                    <select name="comercial" id="Vendedor" class = "form-control" required>
                        
                        <option selected>Selecione um Vendedor</option>
                        @foreach($comerciais as $comercial)
                            <option value="{{$comercial->id}}">{{$comercial->usuario}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
