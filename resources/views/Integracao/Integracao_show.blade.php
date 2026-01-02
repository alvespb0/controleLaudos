@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 1500px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Integracoes Cadastradas</h3>
            
            @if($integracoes->isEmpty())
                <div class="alert alert-warning text-center">
                    Nenhum Cliente cadastrado.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Sistema</th>
                            <th>descricao</th>
                            <th>Endpoint</th>
                            <th>Slug</th>
                            <th>Modelo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($integracoes as $integracao)
                            <tr>
                                <td>{{ $integracao->sistema }}</td>
                                <td>{{ $integracao->descricao ?? '-'}}</td>
                                <td>{{ $integracao->endpoint }}</td>
                                <td>{{ $integracao->slug }}</td>
                                <td>{{ $integracao->tipo }}</td>
                                <td class="text-center">
                                    @if($integracao->deleted_at == null)
                                        <a href="{{route('alteracao.integracao', $integracao->id)}}" class="btn btn-warning btn-sm text-light">Editar</a>
                                        <a href="{{route('auth.integracao', $integracao->id)}}" class="btn btn-info btn-sm text-light">{{ $integracao->username != null || $integracao->password_enc != null ? 'Editar Autenticação' : 'Adicionar Autenticação'}}</a>
                                        <a href="{{route('inativa.integracao', $integracao->id)}}" class="btn btn-danger btn-sm text-light">Inativar</a>
                                    @else
                                        <a href="{{route('reativa.integracao', $integracao->id)}}" class="btn btn-success btn-sm text-light">Reativar</a>
                                    @endif
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
