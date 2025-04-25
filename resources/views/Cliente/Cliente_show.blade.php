@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 1000px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Clientes Cadastrados</h3>
            
            <!-- Verifica se há operadores cadastrados -->
            @if($clientes->isEmpty())
                <div class="alert alert-warning text-center">
                    Nenhum Cliente cadastrado.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>id</th>
                            <th>Cliente</th>
                            <th>CNPJ</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Itera sobre os operadores (você pode adicionar o foreach aqui para gerar as linhas dinamicamente) -->
                        @foreach ($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->id }}</td>
                                <td>{{ $cliente->nome }}</td>
                                <td>{{ $cliente->cnpj }}</td>
                                <td>
                                    <a href="{{ route('alteracao.cliente', $cliente->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="{{ route('delete.cliente', $cliente->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
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
