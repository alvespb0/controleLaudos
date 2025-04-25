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
            <h3 class="card-title mb-4 text-center text-dark">Edição de Laudo</h3>
            <form action="{{ route('update.laudo', $laudo->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nome" class="form-label">Laudo</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="nome ou breve descrição do laudo" value="{{$laudo->nome}}" required>
                </div>

                <div class="mb-3">
                    <label for="dataPrevisao">Data de Previsao de Conclusao de Laudo <small class="text-muted">* Opcional</small></label>
                    <input type="date" name="dataPrevisao" id="dataPrevisao" value="{{$laudo->data_previsao}}" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="dataFimContrato">Data de fim de contrato</label>
                    <input type="date" name="dataFimContrato" id="dataFimContrato" class="form-control" value="{{$laudo->data_fim_contrato}}" required>
                </div>
                <div class="mb-3">
                    <label for="cliente">Cliente</label>
                    <select name="cliente" id="cliente" class = "form-control" required>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $cliente->id == $laudo->cliente_id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="numFunc">Número de funcionários</label>
                    <input type="number" name="numFuncionarios" class="form-control" id="" placeholder="insira o numero de funcionários" min=1 required value="{{$laudo->numero_clientes}}">
                </div>

                <div class="mb-3">
                    <label for="Vendedor">Vendedor</label>
                    <select name="comercial" id="Vendedor" class = "form-control" required>
                        @foreach($comerciais as $comercial)
                            <option value="{{$comercial->id}}" {{ $comercial->id == $laudo->comercial->id ? 'selected' : '' }}>{{$comercial->usuario}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
