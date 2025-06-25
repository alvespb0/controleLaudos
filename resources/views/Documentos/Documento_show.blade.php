@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 1000px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Documentos Técnicos Cadastrados</h3>
            
            <!-- Verifica se há operadores cadastrados -->
            @if($documentos->isEmpty())
                <div class="alert alert-warning text-center">
                    Nenhum Cliente cadastrado.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Descrição</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Data da Solicitação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentos as $doc)
                            <tr>
                                <td>{{ $doc->descricao }}</td>
                                <td>{{ $doc->cliente->nome }}</td>
                                <td>{{ $doc->tipo_documento }}</td>
                                <td>{{ \Carbon\Carbon::parse($doc->data_elaboracao)->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{route('alteracao.documento', $doc->id)}}" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="{{route('delete.documento', $doc->id)}}" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
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
