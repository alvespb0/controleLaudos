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
    body, .crm-kanban-container {
        background: #fff !important;
    }
    .crm-kanban-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
        min-height: 100vh;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    .crm-kanban-toolbar {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #f7fafd;
        border-bottom: 1.5px solid #e3e9ed;
        padding: 1.2rem 2rem 1.2rem 2rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 2px 8px rgba(44,100,92,0.07);
        max-width: 100vw;
        overflow-x: auto;
    }
    .crm-kanban-toolbar .form-control, .crm-kanban-toolbar .form-select {
        min-width: 180px;
        max-width: 260px;
        font-size: 1rem;
    }
    .crm-kanban-toolbar .btn-add-lead {
        background: #1e88e5;
        color: #fff;
        font-weight: 600;
        border-radius: 6px;
        padding: 0.6rem 1.5rem;
        font-size: 1.08rem;
        box-shadow: 0 2px 8px rgba(44,100,92,0.10);
        border: none;
        transition: background 0.2s;
    }
    .crm-kanban-toolbar .btn-add-lead:hover {
        background: #1565c0;
    }
    .crm-kanban-board {
        display: flex;
        gap: 2.5rem;
        overflow-x: auto;
        max-width: 100vw;
        padding: 2rem 2rem 2rem 2rem;
        box-sizing: border-box;
    }
    @media (max-width: 1200px) {
        .crm-kanban-board {
            padding: 1.2rem 0.5rem 1.2rem 0.5rem;
            gap: 1.2rem;
        }
        .crm-kanban-toolbar {
            padding: 1rem 0.7rem 1rem 0.7rem;
        }
    }
    @media (max-width: 700px) {
        .crm-kanban-board {
            padding: 0.5rem 0.2rem 0.5rem 0.2rem;
            gap: 0.5rem;
        }
        .crm-kanban-toolbar {
            padding: 0.7rem 0.2rem 0.7rem 0.2rem;
        }
    }
    .crm-kanban-col {
        background: #fff;
        border-radius: 14px;
        width: 370px;
        min-width: 370px;
        box-shadow: 0 6px 24px rgba(44,100,92,0.13);
        display: flex;
        flex-direction: column;
        max-height: 85vh;
        border: 2.5px solid #e3e9ed;
        border-top: 8px solid var(--primary-color);
        margin-bottom: 1rem;
        transition: box-shadow 0.2s, border 0.2s;
    }
    .crm-kanban-col-header {
        background: #fff;
        color: #22313a;
        border-radius: 12px 12px 0px 0px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(44,100,92,0.07);
        padding: 1.1rem 1.2rem 0.7rem 1.2rem;
        border-bottom: 1.5px solid #e3e9ed;
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
        font-weight: 700;
    }
    .crm-kanban-col-count {
        font-size: 1rem;
        color: #1e88e5;
        font-weight: 600;
        margin-left: 0.5rem;
    }
    .crm-kanban-col-header .btn-add-card {
        color: #fff;
        background: #1e88e5;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        box-shadow: 0 2px 8px rgba(44,100,92,0.10);
        transition: background 0.2s;
    }
    .crm-kanban-col-header .btn-add-card:hover {
        background: #1565c0;
        color: #fff;
    }
    .crm-kanban-col-body {
        flex: 1;
        padding: 1.1rem 1.1rem 1rem 1.1rem;
        overflow-y: auto;
        min-height: 120px;
        background: #fff;
        border-radius: 0 0 12px 12px;
        transition: background 0.2s;
    }
    .crm-kanban-card {
        background: #f7fafd;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(44,100,92,0.10);
        border-top: 4px solid var(--primary-color);
        margin-bottom: 1.2rem;
        transition: box-shadow 0.2s, border 0.2s;
        padding: 1.1rem 1rem 0.7rem 1rem;
    }
    .crm-kanban-card:hover {
        box-shadow: 0 4px 16px rgba(44,100,92,0.13);
        border-color: #1e88e5;
    }
    .crm-card-title {
        font-weight: 700;
        color: #22313a;
        margin-bottom: 0.3rem;
        font-size: 1.08rem;
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
    .crm-kanban-col-desc {
        display: block;
        margin-top: 0.2rem;
        color: #7b8a99;
        font-size: 0.97rem;
    }
    .btn-orcamento {
        background: #f5f7fa;
        color: var(--primary-color);
        border: 1.5px solid var(--primary-color);
        border-radius: 6px;
        padding: 0.45rem 1.2rem;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.18s;
        box-shadow: 0 1px 4px rgba(44,100,92,0.07);
        line-height: 1.1;
        margin-left: 0.5rem;
    }
    .btn-orcamento:hover, .btn-orcamento:focus {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }
</style>

<div class="crm-kanban-toolbar">
    <form method="GET" action="" class="d-flex align-items-center gap-2 mb-0 w-100">
        <input type="text" name="busca" class="form-control" placeholder="Busca global..." value="{{ request('busca') }}" style="max-width: 260px;" onchange="this.form.submit()">
        <select name="funil" class="form-select">
            <option value="">Funil Vendas</option>
            <option value="1">Funil 1</option>
            <option value="2">Funil 2</option>
        </select>
        <select name="vendedor" class="form-select" onchange="this.form.submit()">
            <option value="">Todos Vendedores</option>
            @foreach($comercial as $vendedor)
                <option value="{{$vendedor->id}}" {{ request('vendedor') == $vendedor->id ? 'selected' : '' }} >{{$vendedor->usuario}}</option>
            @endforeach
        </select>
        <select name="periodo" class="form-select" onchange="this.form.submit()">
            <option value="15" {{ request('periodo') == 15 ? 'selected' : '' }}>Últimos 15 dias</option>
            <option value="30" {{ request('periodo') == 30 || request('periodo') === null ? 'selected' : '' }}>Últimos 30 dias</option>
            <option value="45" {{ request('periodo') == 45 ? 'selected' : '' }}>Últimos 45 dias</option>
            <option value="60" {{ request('periodo') == 60 ? 'selected' : '' }}>Últimos 60 dias</option>
            <option value="all" {{ request('periodo') == 'all' ? 'selected' : '' }}>Todos</option>
        </select>
        <button type="button" class="btn btn-add-lead ms-auto" data-bs-toggle="modal" data-bs-target="#modalCadastroLead">
            <i class="bi bi-plus-lg"></i> Oportunidade
        </button>
    </form>
</div>
<div class="crm-kanban-container">
    <div class="crm-kanban-board" id="crmKanbanBoard">
        @foreach($etapas as $etapa)
            <div class="crm-kanban-col">
                <div class="crm-kanban-col-header">
                    <div class="crm-kanban-col-title-row">
                        <span class="crm-kanban-col-title">{{ $etapa->nome }}</span>
                        <button class="btn-add-card" title="Adicionar Oportunidade" data-etapa-id="{{ $etapa->id }}" data-bs-toggle="modal" data-bs-target="#modalCadastroLead">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <span class="crm-kanban-col-count">
                        <span class="crm-kanban-col-count-number">
                            {{ $leads->where('status_id', $etapa->id)->count() }}
                        </span>
                        <span class="crm-kanban-col-count-label">Leads</span>
                    </span>
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
                  @endif
                  <form method="GET" action="{{ route('gerar.orcamentoLead', $lead->id) }}" class="ms-2 d-inline">
                    <button type="submit" class="btn btn-orcamento align-middle">
                      {{ $lead->orcamento_gerado ? 'Gerar outro orçamento' : 'Gerar Orçamento' }}
                    </button>
                  </form>
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
        <div class="card border-0 shadow-sm mb-2" style="background: #fafdff;">
    <div class="card-body p-3">
      <ul class="list-unstyled mb-0">
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-person-badge me-2 text-primary" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Vendedor:</span>
          <span class="ms-1 text-dark">{{ $lead->vendedor->usuario ?? 'Sem vendedor responsável' }}</span>
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-cash-coin me-2 text-success" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Investimento:</span>
          <span class="ms-1 text-dark">R$ {{ $lead->investimento ?? '-' }}</span>
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-person-lines-fill me-2 text-secondary" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Nome do Contato:</span>
          <span class="ms-1 text-dark">{{ $lead->nome_contato ?? '-' }}</span>
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-calendar-event me-2 text-warning" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Próximo Contato:</span>
          <span class="ms-1 text-dark">{{ $lead->proximo_contato ?? '-' }}</span>
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-kanban me-2 text-info" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Status:</span>
          <span class="ms-1 text-dark">{{ $lead->status_id ? $lead->status->nome : '' }}</span>
        </li>
        <li class="d-flex align-items-center">
          <i class="bi bi-chat-left-text me-2 text-muted" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Observações:</span>
          <span class="ms-1 text-dark">{{ $lead->observacoes ?? '-' }}</span>
        </li>
      </ul>
    </div>
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
          <!-- Campo de etapa dinâmico -->
          <div id="campoEtapaHidden">
            <input type="hidden" name="status_id" id="inputEtapaId">
          </div>
          <div id="campoEtapaSelect" style="display:none;">
            <label for="selectEtapaId" class="form-label">Etapa</label>
            <select name="status_id" id="selectEtapaId" class="form-control">
              @foreach($etapas as $etapa)
                <option value="{{$etapa->id}}">{{$etapa->nome}}</option>
              @endforeach
            </select>
          </div>
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
    const campoEtapaHidden = document.getElementById('campoEtapaHidden');
    const campoEtapaSelect = document.getElementById('campoEtapaSelect');
    const selectEtapaId = document.getElementById('selectEtapaId');

    // Botão da navbar
    const btnAddLeadNavbar = document.querySelector('.btn-add-lead');

    // Ao clicar no botão da navbar
    if(btnAddLeadNavbar) {
      btnAddLeadNavbar.addEventListener('click', function() {
        campoEtapaHidden.style.display = 'none';
        campoEtapaSelect.style.display = 'block';
        // Desabilita o hidden, habilita o select
        inputEtapaId.disabled = true;
        if(selectEtapaId) selectEtapaId.disabled = false;
        // Seleciona a primeira etapa por padrão
        if(selectEtapaId) selectEtapaId.selectedIndex = 0;
      });
    }

    // Ao clicar no botão de cada etapa
    document.querySelectorAll('.btn-add-card').forEach(button => {
      button.addEventListener('click', () => {
        const etapaId = button.getAttribute('data-etapa-id');
        inputEtapaId.value = etapaId;
        campoEtapaHidden.style.display = 'block';
        campoEtapaSelect.style.display = 'none';
        // Habilita o hidden, desabilita o select
        inputEtapaId.disabled = false;
        if(selectEtapaId) selectEtapaId.disabled = true;
      });
    });
  });
</script>

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

<!-- Adicionar meta CSRF para AJAX -->
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@endsection
