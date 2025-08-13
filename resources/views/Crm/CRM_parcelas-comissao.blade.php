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
            <i class="bi bi-cash-coin"></i> Parcelas de Comissão
        </span>
    </div>

    <!-- Informações da Comissão -->
    @if(isset($comissao))
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 text-center">
                <i class="bi bi-info-circle text-primary"></i> Informações da Comissão
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                        <div class="flex-shrink-0 me-3">
                            <i class="bi bi-person-circle text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted small">Cliente</h6>
                            <p class="mb-0 fw-bold">{{ $comissao->lead->cliente->nome ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                        <div class="flex-shrink-0 me-3">
                            <i class="bi bi-person-badge text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted small">Vendedor</h6>
                            <p class="mb-0 fw-bold">{{ $comissao->vendedor && $comissao->vendedor->usuario ? $comissao->vendedor->usuario : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                        <div class="flex-shrink-0 me-3">
                            <i class="bi bi-currency-dollar text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted small">Valor Total</h6>
                            <p class="mb-0 fw-bold text-success">R$ {{ number_format($comissao->valor_comissao ?? 0, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                        <div class="flex-shrink-0 me-3">
                            <i class="bi bi-percent text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted small">Percentual</h6>
                            <p class="mb-0 fw-bold text-info">{{ number_format($comissao->percentual_aplicado ?? 0, 2) }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                        <div class="flex-shrink-0 me-3">
                            <i class="bi bi-calendar-event text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-muted small">Data de Criação</h6>
                            <p class="mb-0 fw-bold">{{ $comissao->created_at ? $comissao->created_at->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm bg-white rounded">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Parcela</th>
                    <th class="text-center">Valor da Parcela</th>
                    <th class="text-center">Data Prevista</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @if($comissao->parcelas && $comissao->parcelas->count() > 0)
                    @foreach($comissao->parcelas as $parcela)
                    <tr>
                        <td class="text-center">
                            <strong>{{ $parcela->numero_parcela ?? 'N/A' }}</strong>
                        </td>
                        <td class="text-center">
                            <strong class="text-success">R$ {{ number_format($parcela->valor_parcela ?? 0, 2, ',', '.') }}</strong>
                        </td>
                        <td class="text-center">
                            {{ $parcela->data_prevista ? \Carbon\Carbon::parse($parcela->data_prevista)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="text-center">
                            <select class="form-select form-select-sm" onchange="alterarStatusParcela({{ $parcela->id }}, this.value)">
                                <option value="pendente" {{ $parcela->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="paga" {{ $parcela->status == 'paga' ? 'selected' : '' }}>Paga</option>
                                <option value="cancelada" {{ $parcela->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </td>
                        <td class="text-center">
                            @if($parcela->status == 'pendente')
                                <span class="badge bg-warning">Pendente</span>
                            @elseif($parcela->status == 'paga')
                                <span class="badge bg-success">Paga</span>
                            @elseif($parcela->status == 'cancelada')
                                <span class="badge bg-secondary">Cancelada</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> Nenhuma parcela encontrada
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Botão Voltar -->
    <div class="text-center mt-4">
        <a href="{{ route('read.comissoes') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar para Comissões
        </a>
    </div>
</div>

<script>
function alterarStatusParcela(parcelaId, novoStatus) {
    if (confirm(`Tem certeza que deseja alterar o status da parcela para "${novoStatus}"?`)) {
        // Criar formulário para enviar dados via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("update.parcela-comissao", ["id" => ":id"]) }}'.replace(':id', parcelaId);
        
        // Adicionar CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Adicionar status
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = novoStatus;
        form.appendChild(statusInput);
        
        // Submeter formulário
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection 