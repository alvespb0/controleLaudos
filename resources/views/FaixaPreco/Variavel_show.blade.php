@extends('templateMain')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-center">Variáveis</h3>
    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm bg-white rounded">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Nome</th>
                    <th class="text-center">Campo Alvo</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($variaveis as $variavel)
                <tr class="variavel-row" style="cursor:pointer;" onclick="window.location='#';">
                    <td class="text-center">
                        {{ $variavel->nome }}
                        @if($variavel->ativo == 0)
                            <span class="text-danger fw-bold ms-2">(Inativo)</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $variavel->campo_alvo }}</td>
                    <td class="text-end">
                        <a href="{{ route('alteracao.variavel', $variavel->id )}}" class="btn btn-sm btn-outline-primary me-2" title="Editar"><i class="bi bi-pencil"></i></a>
                        <a href="{{ route('delete.variavel', $variavel->id) }}" class="btn btn-sm btn-outline-danger" title="excluir" onclick="return confirm('Tem certeza que deseja excluir esta variável?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Nenhuma variável cadastrada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
