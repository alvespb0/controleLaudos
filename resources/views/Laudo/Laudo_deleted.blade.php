@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 1000px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Laudos Excluídos</h3>
            
            <!-- Verifica se há operadores cadastrados -->
            @if($laudosExcluidos->isEmpty())
                <div class="alert alert-warning text-center">
                    Nenhum Laudo localizado na lixeira.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>id</th>
                            <th>Laudo</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Excluído Por</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Itera sobre os operadores (você pode adicionar o foreach aqui para gerar as linhas dinamicamente) -->
                        @foreach ($laudosExcluidos as $laudo)
                            <tr>
                                <td>{{ $laudo->id }}</td>
                                <td>{{ $laudo->nome }}</td>
                                <td>{{ $laudo->cliente ? $laudo->cliente->nome : 'não definido'}}</td>
                                <td>{{ $laudo->comercial ? $laudo->comercial->usuario : 'Não definido' }}</td>
                                <td>{{ $laudo->deletedBy ? $laudo->deletedBy->name : 'Usuário já excluido'}}</td>
                                <td>
                                    <a href="{{ route('restore.deletedLaudo', $laudo->id) }}" class="btn btn-dark btn-sm" onclick="return confirm('Tem certeza que deseja restaurar este laudo?')">Restaurar</a>
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
                            <li class="page-item {{ $laudosExcluidos->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $laudosExcluidos->previousPageUrl() }}" tabindex="-1">&laquo;</a>
                            </li>

                            {{-- Links de páginas com intervalo dinâmico --}}
                            @for ($i = 1; $i <= $laudosExcluidos->lastPage(); $i++)
                                <li class="page-item {{ $i == $laudosExcluidos->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $laudosExcluidos->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Botão "Próxima" --}}
                            <li class="page-item {{ !$laudosExcluidos->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $laudosExcluidos->nextPageUrl() }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
