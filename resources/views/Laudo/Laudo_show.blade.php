@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 1000px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Laudos Cadastrados</h3>
            
            <!-- Verifica se há operadores cadastrados -->
            @if($laudos->isEmpty())
                <div class="alert alert-warning text-center">
                    Nenhum Laudo cadastrado.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>id</th>
                            <th>Laudo</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Itera sobre os operadores (você pode adicionar o foreach aqui para gerar as linhas dinamicamente) -->
                        @foreach ($laudos as $laudo)
                            <tr>
                                <td>{{ $laudo->id }}</td>
                                <td>{{ $laudo->nome }}</td>
                                <td>{{ $laudo->cliente ? $laudo->cliente->nome : 'não definido'}}</td>
                                <td>{{ $laudo->comercial ? $laudo->comercial->usuario : 'Não definido' }}</td>
                                <td>
                                    <a href="{{ route('alteracao.laudo', $laudo->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="{{ route('delete.laudo', $laudo->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este laudo?')">Excluir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- PAGINAÇÃO -->
                <div class="col-auto mx-auto">
                    <nav aria-label="Navegação de páginas">
                        <ul class="pagination">
                            {{-- Botão "Anterior" --}}
                            <li class="page-item {{ $laudos->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $laudos->previousPageUrl() }}" tabindex="-1">&laquo;</a>
                            </li>

                            {{-- Links de páginas com intervalo dinâmico --}}
                            @for ($i = 1; $i <= $laudos->lastPage(); $i++)
                                <li class="page-item {{ $i == $laudos->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $laudos->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Botão "Próxima" --}}
                            <li class="page-item {{ !$laudos->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $laudos->nextPageUrl() }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
