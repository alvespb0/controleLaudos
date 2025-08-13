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
            <h3 class="card-title mb-4 text-center text-dark">Edição de Documento Tecnico</h3>
            <form action="{{route('update.documento', $documento->id)}}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-file-earmark-text"></i><label for="nome" class="form-label">&nbspTipo de Documento</label>
                    <select name="tipo_documento" id="" class="form-control">
                        <option value="CAT" {{$documento->tipo_documento == 'CAT' ? 'selected' : ''}}>CAT</option>
                        <option value="ADENDO" {{$documento->tipo_documento == 'ADENDO' ? 'selected' : ''}}>ADENDO</option>
                        <option value="PPP" {{$documento->tipo_documento == 'PPP' ? 'selected' : ''}}>PPP</option>
                        <option value="REAJUSTES LAUDOS" {{$documento->tipo_documento == 'REAJUSTES LAUDOS' ? 'selected' : ''}}>REAJUSTE DE LAUDO</option>
                        <option value="ART" {{$documento->tipo_documento == 'ART' ? 'selected' : ''}}>ART</option>
                        <option value="ASSESSORIA" {{$documento->tipo_documento == 'ASSESSORIA' ? 'selected' : ''}}>ASSESSORIA</option>
                    </select>
                </div>

                <div class="mb-3">
                    <i class="bi bi-person"></i><label for="cliente">&nbspCliente</label>
                    <select name="cliente_id" id="cliente" class = "form-control" required>
                        <option selected>Selecione um cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->id}}" {{$documento->cliente->id == $cliente->id ? 'selected' : ''}}>{{$cliente->nome}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <i class="bi bi-chat-right"></i><label for="descricao">&nbspDescrição</label>
                    <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Descrição, nome do funcionário, etc." value="{{$documento->descricao}}" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-calendar-date"></i><label for="data_elaboracao">&nbspData de solicitação</label>
                    <input type="date" class="form-control" name="data_elaboracao" id="" value="{{$documento->data_elaboracao}}"required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Editar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
