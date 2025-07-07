@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 1000px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Documentos Excluídos</h3>
            
            <!-- Verifica se há operadores cadastrados -->
            @if($docsExcluidos->isEmpty())
                <div class="alert alert-warning text-center">
                    Nenhum documento localizado na lixeira.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Descrição</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Data Solcitação</th>
                            <th>Excluído Por</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Itera sobre os operadores (você pode adicionar o foreach aqui para gerar as linhas dinamicamente) -->
                        @foreach ($docsExcluidos as $doc)
                            <tr>
                                <td>{{ $doc->descricao }}</td>
                                <td>{{ $doc->cliente ? $doc->cliente->nome : 'não definido'}}</td>
                                <td>{{ $doc->tipo_documento }}</td>
                                <td>{{ $doc->data_elaboracao }}</td>
                                <td>{{ $doc->deletedBy ? $doc->deletedBy->name : 'Usuário já excluido'}}</td>
                                <td>
                                    <a href="{{ route('restore.deletedDoc', $doc->id) }}" class="btn btn-dark btn-sm" onclick="return confirm('Tem certeza que deseja restaurar este laudo?')">Restaurar</a>
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
                            <li class="page-item {{ $docsExcluidos->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $docsExcluidos->previousPageUrl() }}" tabindex="-1">&laquo;</a>
                            </li>

                            {{-- Links de páginas com intervalo dinâmico --}}
                            @for ($i = 1; $i <= $docsExcluidos->lastPage(); $i++)
                                <li class="page-item {{ $i == $docsExcluidos->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $docsExcluidos->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Botão "Próxima" --}}
                            <li class="page-item {{ !$docsExcluidos->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $docsExcluidos->nextPageUrl() }}">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
