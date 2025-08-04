@extends('templateMain')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container py-4">
    <div class="mb-4 text-center">
        <span style="font-size:2rem; font-weight:700; color:var(--primary-color); letter-spacing:1px;">
            <i class="bi bi-sliders"></i> Variáveis
        </span>
    </div>
        <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('cadastro.variavel') }}" class="btn" style="background-color: var(--primary-color); color: white; font-weight: 500;">
            <i class="bi bi-plus-circle"></i> Incluir variável
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm bg-white rounded">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Nome</th>
                    <th class="text-center">Campo Alvo</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Valor</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($variaveis as $variavel)
                <tr class="variavel-row" style="cursor:pointer;" onclick="handleRowClick(event, '{{ route('faixa.preco', $variavel->id) }}', '{{ $variavel->tipo }}');">
                    <td class="text-center">
                        {{ $variavel->nome }}
                        @if($variavel->ativo == 0)
                            <span class="text-danger fw-bold ms-2">(Inativo)</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $variavel->campo_alvo }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ ucfirst($variavel->tipo) }}</span>
                    </td>
                    <td class="text-center">
                        @if($variavel->tipo == 'faixa')
                            <span class="text-muted fst-italic">Valor definido por faixa</span>
                        @elseif($variavel->tipo == 'percentual')
                            {{ $variavel->valor ? '% ' . number_format($variavel->valor, 2, ',', '.') : '-' }}
                        @else
                            {{ $variavel->valor ? 'R$ ' . number_format($variavel->valor, 2, ',', '.') : '-' }}
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('alteracao.variavel', $variavel->id )}}" class="btn btn-sm btn-outline-primary me-2" title="Editar"><i class="bi bi-pencil"></i></a>
                        @if($variavel->id != 1 && $variavel->id != 2 && $variavel->id != 3)
                            <a href="{{ route('delete.variavel', $variavel->id) }}" class="btn btn-sm btn-outline-danger" title="excluir" onclick="return confirm('Tem certeza que deseja excluir esta variável?')"><i class="bi bi-trash"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Nenhuma variável cadastrada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function handleRowClick(event, url, tipo) {
    // Se o clique foi em um botão de ação, não redireciona
    if (event.target.closest('a') || event.target.closest('button')) {
        return;
    }
    
    // Só redireciona se for do tipo 'faixa'
    if (tipo === 'faixa') {
        window.location.href = url;
    } else {
        // Para outros tipos, mostra um alert
        alert('Só é possível configurar faixa de preço para variáveis do tipo "faixa"');
    }
}
</script>
@endsection
