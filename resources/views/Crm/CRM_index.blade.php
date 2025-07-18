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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .crm-kanban-container {
        padding: 2rem;
        min-height: 100vh;
        background: #f5f7fa;
    }
    .crm-kanban-header {
        margin-bottom: 2rem;
    }
    .crm-kanban-header h1 {
        font-size: 2.2rem;
        color: #22313a;
        font-weight: 800;
        letter-spacing: 1px;
    }
    .crm-kanban-board {
        display: flex;
        gap: 2rem;
        overflow-x: auto;
    }
    .crm-kanban-col {
        background: #fff;
        border-radius: 8px;
        width: 370px;
        min-width: 370px;
        box-shadow: 0 2px 12px rgba(44,100,92,0.07);
        display: flex;
        flex-direction: column;
        max-height: 85vh;
        border: 1px solid #e3e9ed;
        transition: box-shadow 0.2s, border 0.2s;
        margin-bottom: 1rem;
    }
    .crm-kanban-col-header {
        padding: 1.1rem 1.2rem 1rem 1.2rem;
        font-weight: 700;
        color: #22313a;
        border-bottom: 1px solid #e3e9ed;
        font-size: 1.18rem;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }
    .crm-kanban-col-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .crm-kanban-col-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.18rem;
    }
    .crm-kanban-col-title .bi {
        font-size: 1.1rem;
        color: #7b8a99;
    }
    .crm-kanban-col-count {
        font-size: 0.98rem;
        color: #7b8a99;
        font-weight: 400;
        margin-left: 0.5rem;
    }
    .crm-kanban-col-header .btn-add-card {
        color: #7b8a99;
        background: transparent;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
        font-size: 1.2rem;
    }
    .crm-kanban-col-header .btn-add-card:hover {
        background: #e3e9ed;
        color: #22313a;
    }
    .crm-kanban-col-body {
        flex: 1;
        padding: 1.1rem 1.1rem 1rem 1.1rem;
        overflow-y: auto;
        min-height: 120px;
        background: #fff;
        border-radius: 0 0 8px 8px;
        transition: background 0.2s;
    }
    .crm-kanban-col-body.drag-over {
        background: #f0f4f8;
        border: 2px dashed #b0bfc7;
    }
    .crm-kanban-card {
        background: #f8fafc;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(44,100,92,0.07);
        padding: 1rem 0.9rem 0.9rem 0.9rem;
        margin-bottom: 1rem;
        cursor: grab;
        border: 1px solid #e3e9ed;
        transition: box-shadow 0.2s, transform 0.2s, border 0.2s;
        user-select: none;
        position: relative;
    }
    .crm-kanban-card.dragging {
        opacity: 0.7;
        box-shadow: 0 8px 32px rgba(44,100,92,0.13);
        transform: scale(1.03);
        z-index: 10;
        border: 2px solid #b0bfc7;
    }
    .crm-kanban-card:hover {
        box-shadow: 0 4px 16px rgba(44,100,92,0.13);
        transform: scale(1.01);
        border-color: #b0bfc7;
    }
    .crm-card-title {
        font-weight: 700;
        color: #22313a;
        margin-bottom: 0.3rem;
        font-size: 1.08rem;
        letter-spacing: 0.1px;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .crm-card-info {
        font-size: 0.99rem;
        color: #64748b;
        margin-bottom: 0.4rem;
    }
    .crm-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.93rem;
        color: #888;
        margin-top: 0.2rem;
    }
    .crm-badge {
        background: #e3e9ed;
        color: #22313a;
        border-radius: 6px;
        padding: 0.18rem 0.7rem;
        font-size: 0.91rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .crm-value {
        color: #22313a;
        font-weight: 700;
    }
    .crm-card-actions {
        display: flex;
        gap: 0.2rem;
    }
    .crm-card-actions .btn {
        padding: 0.15rem 0.4rem;
        font-size: 0.93rem;
        color: #7b8a99;
        background: transparent;
        border: none;
        border-radius: 4px;
        transition: background 0.2s;
    }
    .crm-card-actions .btn:hover {
        background: #e3e9ed;
        color: #22313a;
    }
/* Hover discreto para badges de troca de etapa */
.etapa-badge:hover {
    filter: brightness(0.92);
    box-shadow: 0 2px 8px rgba(44,100,92,0.10);
    transform: translateY(-2px) scale(1.04);
    transition: all 0.18s;
}
/* Alinhar badges à direita na modal */
#etapasBadges {
    justify-content: flex-end !important;
}

.modal .card,
.modal .card-body,
.modal .crm-value,
.modal .badge,
.modal .form-label,
.modal .form-control,
.modal .row,
.modal .col-12,
.modal .col-md-6 {
    user-select: text !important;
}

.modal i {
    user-select: none !important;
}
/* CSS extra para alinhar os botões de ação na modal */
.btn-enviar-whatsapp {
    background: var(--primary-color) !important;
    color: #fff !important;
    border: none !important;
    padding: 0.65rem 1.7rem !important;
    font-size: 1.08rem !important;
    display: flex;
    align-items: center;
    gap: 0.7rem;
    height: 46px;
}
.btn-enviar-whatsapp:hover {
    background: var(--hover-color) !important;
    color: #fff !important;
}
</style>

<div class="crm-kanban-container">
    <div class="crm-kanban-header">
        <h1>Kanban de CRM</h1>
        <p class="text-muted mb-0">Arraste as oportunidades entre as etapas do funil.</p>
    </div>
    <div class="crm-kanban-board" id="crmKanbanBoard">
        @foreach($etapas as $etapa)
            <div class="crm-kanban-col">
                <div class="crm-kanban-col-header">
                    <div class="crm-kanban-col-title-row">
                        <span class="crm-kanban-col-title">
                             {{ $etapa->nome }}
                        </span>
                        <button class="btn-add-card" title="Adicionar Oportunidade" data-etapa-id="{{ $etapa->id }}" data-bs-toggle="modal" data-bs-target="#modalCadastroLead">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <span class="crm-kanban-col-count">colocar numero de leads</span>
                    <span class="crm-kanban-col-desc">{{ $etapa->descricao }}</span>
                </div>
                <div class="crm-kanban-col-body sortable-col" data-coluna="{{ $etapa->id }}">
                    @foreach($leads as $lead)
                        @if($lead->status_id == $etapa->id)
                        <div class="crm-kanban-card">
                            <div class="crm-card-title">
                                <i class="bi bi-person-circle"></i> {{ $lead->cliente->nome }}
                            </div>

                            <div class="crm-card-info">
                                <span><i class="bi bi-person-badge"></i>Vendedor: {{ $lead->vendedor ? $lead->vendedor->usuario : 'Sem vendedor responsável'}}</span>
                            </div>
                            @if($lead->proximo_contato != null)
                            <div class="crm-card-info">
                                <span><i class="bi bi-calendar-date"></i>Próximo Contato: {{$lead->proximo_contato}}</span>
                            </div>
                            @endif
                            <div class="crm-card-footer">
                                {{$lead->observacoes ? $lead->observacoes : 'nenhuma observação adicionada'}}
                                <div class="crm-card-actions">
                                    <button class="btn" title="Ver detalhes" data-bs-toggle="modal" data-bs-target="#modalDetalhesLead{{ $lead->id }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn" title="Editar"><i class="bi bi-pencil"></i></button>
                                </div>
                            </div>
                        </div>

<!-- Modal -->
<div class="modal fade" id="modalCadastroLead" tabindex="-1" aria-labelledby="modalCadastroLeadLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{route('create.lead')}}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroLeadLabel">Cadastrar Lead</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <!-- Input hidden com etapa_id -->
          <input type="hidden" name="status_id" id="inputEtapaId">
            <div class="mb-3">
            <label for="cliente" class="form-label">Cliente</label>
                <select name="cliente_id" id="cliente" class = "form-control" required>
                    <option selected>Selecione um cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{$cliente->id}}">{{$cliente->nome}}</option>
                    @endforeach
                </select>          
            </div>
            <div class="mb-3">
                <label for="investimento" class="form-label">Investimento</label>
                <input type="number" name="investimento" id="" class="form-control" step="0.01" min="0">
            </div>
            <div class="mb-3">
                <label for="contato" class="form-label">Nome do Contato</label>
                <input type="text" name="nome_contato" id="contato" class="form-control" step="0.01" min="0">
            </div>
            <div class="mb-3">
              <label for="observacoes" class="form-label">Próximo contato</label>
              <input type="date" name="proximo_contato" class="form-control" id="">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Observações</label>
              <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Modal de cadastro
    const modal = document.getElementById('modalCadastroLead');
    const inputEtapaId = document.getElementById('inputEtapaId');
    document.querySelectorAll('.btn-add-card').forEach(button => {
      button.addEventListener('click', () => {
        const etapaId = button.getAttribute('data-etapa-id');
        inputEtapaId.value = etapaId;
      });
    });
  });
</script>

                        <!-- Modal Detalhes Lead -->
<div class="modal fade" id="modalDetalhesLead{{ $lead->id }}" tabindex="-1" aria-labelledby="modalDetalhesLeadLabel{{ $lead->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="min-height: 70vh;">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalhesLeadLabel{{ $lead->id }}">Detalhes do Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-12">
            <div class="card shadow-sm border-0 mb-3" style="background: #f8fafc;">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-person-circle me-2" style="font-size:2rem;color:var(--primary-color)"></i>
                  <h4 class="mb-0">{{ $lead->cliente->nome ?? '' }}</h4>
                  @if($lead->cliente->cliente_novo)
                    <span class="badge bg-success ms-3">Novo Cliente</span>
                  @endif
                  @if($lead->orcamento_gerado)
                    <span class="badge bg-info ms-2">Orçamento Gerado</span>
                  @else
                  <span class="badge bg-warning text-dark ms-2">Sem Orçamento</span>
                  <form method="POST" action="{{ url('/crm/lead/'.$lead->id.'/gerar-orcamento') }}" class="ms-2 d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary align-middle">Gerar Orçamento</button>
                  </form>
                  @endif
                </div>
                <div class="row mb-1">
                  <div class="col-md-6">
                    <strong>CNPJ:</strong> <span class="crm-value">{{ $lead->cliente->cnpj ?? '-' }}</span>
                  </div>
                  <div class="col-md-6">
                    <strong>E-mail:</strong> <span class="crm-value">{{ $lead->cliente->email ?? '-' }}</span>
                  </div>
                </div>
                <div class="row mb-1">
                  <div class="col-md-6">
                    <strong>Telefones:</strong>
                    @if($lead->cliente->telefone && $lead->cliente->telefone->count())
                      @foreach($lead->cliente->telefone as $tel)
                        <span class="badge bg-light text-dark border me-1">{{ $tel->telefone }}</span>
                      @endforeach
                    @else
                      <span class="text-muted">Nenhum telefone cadastrado</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-12 d-flex align-items-center gap-2">
            <button class="btn btn-primary btn-enviar-whatsapp" type="button" data-bs-toggle="collapse" data-bs-target="#whatsappForm{{ $lead->id }}" aria-expanded="false" aria-controls="whatsappForm{{ $lead->id }}">
              <i class="bi bi-whatsapp"></i> Enviar WhatsApp
            </button>
            <button class="btn btn-primary btn-enviar-whatsapp" type="button" data-bs-toggle="collapse" data-bs-target="#emailForm{{ $lead->id }}" aria-expanded="false" aria-controls="emailForm{{ $lead->id }}">
              <i class="bi bi-envelope-fill"></i> Enviar Email
            </button>
            <div class="collapse w-100" id="whatsappForm{{ $lead->id }}">
              <form action="{{route('orcamento.zappy')}}" method="POST" enctype="multipart/form-data" class="mt-3 p-3 border rounded bg-light">
                @csrf
                <div class="mb-2">
                  <label class="form-label">Número do Cliente</label>
                  <input type="text" class="form-control" value="{{ optional($lead->cliente->telefone->first())->telefone ?? '' }}" disabled>
                  <input type="hidden" name="numero" value="{{ optional($lead->cliente->telefone->first())->telefone ?? '' }}">
                </div>
                <div class="mb-2">
                  <label class="form-label">Mensagem</label>
                  <textarea class="form-control" name="mensagem" rows="2" required></textarea>
                </div>
                <div class="mb-2">
                  <label class="form-label">Arquivo (opcional)</label>
                  <input type="file" class="form-control" name="file">
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-whatsapp"></i> Enviar</button>
              </form>
            </div>
            <div class="collapse w-100" id="emailForm{{ $lead->id }}">
              <form action="#" method="POST" enctype="multipart/form-data" class="mt-3 p-3 border rounded bg-light">
                @csrf
                <div class="mb-2">
                  <label class="form-label">E-mail do Cliente</label>
                  <input type="text" class="form-control" value="{{ $lead->cliente->email ?? '' }}" disabled>
                  <input type="hidden" name="email" value="{{ $lead->cliente->email ?? '' }}">
                </div>
                <div class="mb-2">
                  <label class="form-label">Assunto</label>
                  <input type="text" class="form-control" name="assunto" required>
                </div>
                <div class="mb-2">
                  <label class="form-label">Mensagem</label>
                  <textarea class="form-control" name="mensagem" rows="2" required></textarea>
                </div>
                <div class="mb-2">
                  <label class="form-label">Arquivo (opcional)</label>
                  <input type="file" class="form-control" name="arquivo">
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-envelope-fill"></i> Enviar</button>
              </form>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6"><strong>Vendedor:</strong> {{ $lead->vendedor->usuario ?? 'Sem vendedor responsável' }}</div>
          <div class="col-md-6"><strong>Investimento:</strong> R$ {{ $lead->investimento ?? '-' }}</div>
        </div>
        <div class="row mt-2">
          <div class="col-md-6"><strong>Nome do Contato:</strong> {{ $lead->nome_contato ?? '-' }}</div>
          <div class="col-md-6"><strong>Próximo Contato:</strong> {{ $lead->proximo_contato ?? '-' }}</div>
        </div>
        <div class="row mt-2">
          <div class="col-md-6"><strong>Status:</strong> {{ $etapas->firstWhere('id', $lead->status_id)->nome ?? '-' }}</div>
          <div class="col-md-6"><strong>Observações:</strong> {{ $lead->observacoes ?? '-' }}</div>
        </div>
        </div>
        <div class="modal-footer flex-column align-items-stretch">
          <div class="mb-2 w-100">
            <label class="form-label">Trocar de etapa:</label>
            <div class="d-flex flex-wrap gap-2 w-100 justify-content-end">
              @foreach($etapas as $idx => $etapaTroca)
                <form method="GET" action="{{ route('alterStatus.lead', ['lead_id' => $lead->id, 'etapa_id' => $etapaTroca->id]) }}" style="display:inline;">
                  @csrf
                  <input type="hidden" name="etapa_id" value="{{ $etapaTroca->id }}">
                  <button type="submit" class="badge rounded-pill etapa-badge {{ $lead->status_id == $etapaTroca->id ? 'bg-primary' : '' }}"
                    style="cursor:pointer; font-size:1.1rem; padding:0.7em 1.2em; background:var(--primary-color); color:#fff; border:none; outline:none;">
                    {{ $etapaTroca->nome }}
                  </button>
                </form>
              @endforeach
            </div>
          </div>
          <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
      </div>
      </div>
      @endif
    @endforeach
  </div>
  </div>
  @endforeach
</div>
</div>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
window.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sortable-col').forEach(function(col) {
        new Sortable(col, {
            group: 'crm-kanban',
            animation: 180,
            ghostClass: 'dragging',
            dragClass: 'drag-active',
            chosenClass: 'drag-chosen',
        });
    });
});
</script>
@endsection

<!-- Adicionar meta CSRF para AJAX -->
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
