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
