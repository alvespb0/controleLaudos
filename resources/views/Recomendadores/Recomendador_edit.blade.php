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
            <h3 class="card-title mb-4 text-center text-dark">Edição de Indicador Externo</h3><h6 class="card-title mb-4 text-center text-dark"><small>Indicador Externo — quando o lead é indicado por alguém SEM ser o vendedor</small></h6>
            <form action="{{ route('edit.recomendador', $recomendador->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-patch-check"></i><label for="nome" class="form-label">&nbspNome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required placeholder="Nome do Recomendador" value="{{$recomendador->nome ? $recomendador->nome : ''}}">
                </div>

                <div class="mb-3">
                    <i class="bi bi-card-text"></i><label for="cpf" class="form-label">&nbspCPF</label>
                    <input type="text" name="cpf" class="form-control" id="cpf" value="{{$recomendador->cpf ? $recomendador->cpf : ''}}">
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Editar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </ >
        </div>
    </div>
</div>
@endsection
