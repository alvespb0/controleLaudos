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
            <i class="bi bi-cash-coin"></i> Comissões
        </span>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ request()->url() }}" class="row g-3">
                <div class="col-md-3">
                    <label for="periodo" class="form-label">Período</label>
                    <input type="month" class="form-control" id="periodo" name="periodo" value="{{ request('periodo') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="paga" {{ request('status') == 'paga' ? 'selected' : '' }}>Paga</option>
                        <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="Cliente" class="form-label">Cliente</label>
                    <input type="text" name="cliente" id="cliente" class="form-control" value="{{request('cliente') ? request('cliente') : ''}}" placeholder="Nome do cliente">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Limpar
                        </a>
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm bg-white rounded">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Lead</th>
                    <th class="text-center">Vendedor</th>
                    <th class="text-center">Valor Comissão</th>
                    <th class="text-center">Percentual</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Data</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comissoes as $comissao)
                <tr>
                    <td class="text-center">
                        <strong>{{ $comissao->lead->cliente->nome ?? 'N/A' }}</strong>
                        <br>
                        <small class="text-muted">Lead #{{ $comissao->lead_id ?? 'N/A' }}</small>
                    </td>
                    <td class="text-center">
                        <strong>{{ $comissao->vendedor->usuario ?? 'N/A' }}</strong>
                        <br>
                        <small class="text-muted">{{ $comissao->vendedor->user->email ?? 'N/A' }}</small>
                    </td>
                    <td class="text-center">
                        <strong class="text-success">R$ {{ number_format($comissao->valor_comissao ?? 0, 2, ',', '.') }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ number_format($comissao->vendedor->percentual_comissao ?? 0, 2) }}%</span>
                    </td>
                    <td class="text-center">
                        @if($comissao->status == 'pendente')
                            <span class="badge bg-warning">Pendente</span>
                        @elseif($comissao->status == 'paga')
                            <span class="badge bg-primary">Paga</span>
                        @elseif($comissao->status == 'cancelada')
                            <span class="badge bg-danger">Cancelada</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($comissao->status ?? 'N/A') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $comissao->created_at ? $comissao->created_at->format('d/m/Y H:i') : 'N/A' }}
                    </td>
                    <td class="text-center">
                        <select class="form-select form-select-sm" onchange="alterarStatus({{ $comissao->id }}, this.value)">
                            <option value="pendente" {{ $comissao->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="paga" {{ $comissao->status == 'paga' ? 'selected' : '' }}>Paga</option>
                            <option value="cancelada" {{ $comissao->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-4">
        {{ $comissoes->links() }}
    </div>
    <div class="col-auto mx-auto">
        <nav aria-label="Navegação de páginas">
            <ul class="pagination">
                @if ($comissoes->currentPage() > 1)
                <li class="page-item">
                <a class="page-link" href="{{ $comissoes->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
                </li>
                <li class="page-item"><a class="page-link" href="{{ $comissoes->previousPageUrl() }}">{{ $comissoes->currentPage() - 1}}</a></li>
                @endif
                <li class="page-item active"><a class="page-link" href="{{ $comissoes->nextPageUrl() }}">{{ $comissoes->currentPage() }}</a></li>
                @if ($comissoes->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $comissoes->nextPageUrl() }}">{{ $comissoes->currentPage() + 1 }}</a></li>
                <li class="page-item">
                    <a class="page-link" href="{{ $comissoes->nextPageUrl() }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    </div>
</div>

<script>
function alterarStatus(comissaoId, novoStatus) {
    if (confirm(`Tem certeza que deseja alterar o status da comissão para "${novoStatus}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("update-status.comissao", ["comissao_id" => ":comissao_id", "status" => ":status"]) }}'
            .replace(':comissao_id', comissaoId)
            .replace(':status', novoStatus);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection