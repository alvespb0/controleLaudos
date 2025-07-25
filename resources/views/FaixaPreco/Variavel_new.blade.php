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
            <h3 class="card-title mb-4 text-center text-dark">Cadastro de Variáveis Para Precificação</h3>
            <form action="{{route('create.variavel')}}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-type"></i><label for="Nome" class="form-label">&nbspNome</label>
                    <input type="text" class="form-control" id="Nome" name="nome_variavel" required placeholder="Nome da variável">
                </div>
                <div class="mb-3">
                    <i class="bi bi-ticket-detailed"></i><label for="campo_alvo">&nbspCampo Alvo</label>
                    <input type="text" name="campo_alvo" class="form-control" id="campo_alvo" required placeholder="Descrição do campo">
                </div>
                <div class="mb-3">
                    <i class="bi bi-text-indent-left"></i><label for="campo_alvo">&nbspTipo</label>
                    <select name="tipo" id="" class="form-control" required>
                        <option value="numerico">numerico</option>
                        <option value="booleano">booleano (verdadeiro ou falso)</option>
                        <option value="string">texto</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
