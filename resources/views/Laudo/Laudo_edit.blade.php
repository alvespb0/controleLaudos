@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none;">
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
                    <i class="bi bi-file-earmark-text"></i><label for="nome" class="form-label">&nbspLaudo</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="nome ou breve descrição do laudo" value="{{$laudo->nome}}" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-calendar3"></i><label for="dataPrevisao">&nbspData de Previsao de Conclusao de Laudo <small class="text-muted">* Opcional</small></label>
                    <input type="date" name="dataPrevisao" id="dataPrevisao" value="{{$laudo->data_previsao}}" class="form-control">
                </div>

                <div class="mb-3">
                    <i class="bi bi-calendar3"></i><label for="dataAceiteContrato">&nbspData de Aceite do Contrato</label>
                    <input type="date" name="dataAceiteContrato" id="dataAceiteContrato" class="form-control" value="{{$laudo->data_aceite}}" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-calendar3"></i><label for="dataFimContrato">&nbspData de fim de contrato</label>
                    <input type="date" name="dataFimContrato" id="dataFimContrato" class="form-control" value="{{$laudo->data_fim_contrato}}" required>
                </div>
                <div class="mb-3">
                    <i class="bi bi-person"></i><label for="cliente">&nbspCliente</label>
                    <select name="cliente" id="cliente" class = "form-control" required>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $cliente->id == $laudo->cliente_id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block"><i class="bi bi-journal-bookmark-fill"></i>&nbspEsocial?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="esocial" id="esocial_sim" value="1" {{$laudo->esocial ? 'checked' : ''}} required>
                        <label class="form-check-label" for="esocial_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="esocial" id="esocial_nao" value="0" {{!$laudo->esocial ? 'checked' : ''}} required>
                        <label class="form-check-label" for="esocial_nao">Não</label>
                    </div>
                </div>

                <div class="mb-3">
                    <i class="bi bi-123"></i><label for="numFunc">&nbspNúmero de funcionários</label>
                    <input type="number" name="numFuncionarios" class="form-control" id="" placeholder="insira o numero de funcionários" min=1 required value="{{$laudo->numero_clientes}}">
                </div>

                <div class="mb-3">
                    <i class="bi bi-person"></i><label for="Vendedor">&nbspVendedor</label>
                    <select name="comercial" id="Vendedor" class = "form-control" required>
                        @foreach($comerciais as $comercial)
                            <option value="{{$comercial->id}}" {{ $comercial->id == $laudo->comercial->id ? 'selected' : '' }}>{{$comercial->usuario}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary col-sm-12">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
