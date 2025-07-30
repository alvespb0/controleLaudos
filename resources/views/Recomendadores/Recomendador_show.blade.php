@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 1000px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Indicadores Externos Cadastrados</h3><h6 class="card-title mb-4 text-center text-dark"><small>Indicador Externo — quando o lead é indicado por alguém SEM ser o vendedor</small></h6>
            @if($recomendadores->isEmpty())
                <div class="alert alert-warning text-center">
                    Nenhum Recomendador cadastrado.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>id</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Itera sobre os operadores (você pode adicionar o foreach aqui para gerar as linhas dinamicamente) -->
                        @foreach ($recomendadores as $recomendador)
                            <tr>
                                <td>{{ $recomendador->id }}</td>
                                <td>{{ $recomendador->nome }}</td>
                                <td>{{ $recomendador->cpf}}</td>
                                <td>
                                    <a href="{{ route('alteracao.recomendador', $recomendador->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="{{ route('delete.recomendador', $recomendador->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este status?')">Excluir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
