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
            <h3 class="card-title mb-4 text-center text-dark">Edição de Váriavel</h3>
            <form action="{{route('edit.variavel', $variavel->id)}}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-type"></i><label for="Nome" class="form-label">&nbspNome</label>
                    <input type="text" class="form-control" id="Nome" name="nome_variavel" required placeholder="Nome da variável" value="{{$variavel->nome}}">
                </div>
                <div class="mb-3">
                    <i class="bi bi-ticket-detailed"></i><label for="campo_alvo">&nbspCampo Alvo</label>
                    <input type="text" name="campo_alvo" class="form-control" id="campo_alvo" required placeholder="Descrição do campo" value="{{$variavel->campo_alvo}}">
                </div>
                <div class="mb-3">
                    <i class="bi bi-text-indent-left"></i><label for="campo_alvo" value="{{$variavel->nome}}">&nbspTipo</label>
                    <select name="tipo" id="" class="form-control" required>
                        <option value="numerico" {{$variavel->tipo == 'numerico' ? 'selected' : ''}}>numerico</option>
                        <option value="booleano" {{$variavel->tipo == 'booleano' ? 'selected' : ''}}>booleano (verdadeiro ou falso)</option>
                        <option value="string" {{$variavel->tipo == 'string' ? 'selected' : ''}}>texto</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="Status">Status</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" value="1" id="ativo" {{$variavel->ativo ? 'checked' : ''}}>
                        <label class="form-check-label" for="ativo">
                            Ativo
                        </label>
                        </div>
                        <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" value="0" id="inativo" {{!$variavel->ativo ? 'checked' : ''}}>
                        <label class="form-check-label" for="inativo">
                            Inativo
                        </label>
                    </div>
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
