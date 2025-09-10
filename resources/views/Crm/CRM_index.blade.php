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
    .etapa-badge:hover {
        background: var(--hover-color) !important;
        color: #fff !important;
        filter: brightness(1.05);
        box-shadow: 0 2px 8px rgba(44,100,92,0.13);
        transition: background 0.18s, color 0.18s;
    }
    
    /* Estilos simples para modais */
    .modal-content {
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    .crm-value {
        color: var(--secondary-color);
        font-weight: 500;
    }
    
    /* Estilos para os logs de movimentação */
    .timeline-logs {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .log-item {
        transition: all 0.2s ease;
        border-left: 3px solid var(--primary-color) !important;
    }
    
    .log-item:hover {
        background-color: #e3f2fd !important;
        transform: translateX(2px);
    }
    
    .log-description {
        font-size: 0.9rem;
        color: #374151;
        line-height: 1.4;
    }
    
    .btn-link:hover {
        background-color: transparent !important;
    }
    
    .btn-link:focus {
        box-shadow: none !important;
    }
</style>

<div class="crm-kanban-toolbar">
    <form method="GET" action="" class="d-flex align-items-center gap-2 mb-0 w-100">
        <input type="text" name="busca" class="form-control" placeholder="Busca global..." value="{{ request('busca') }}" style="max-width: 260px;" onchange="this.form.submit()">
        <select name="etapa" class="form-select" onchange="this.form.submit()">
            <option value="">Etapa</option>
            @foreach($etapas as $etapaFiltro)
                <option value="{{$etapaFiltro->id}}" {{ request('etapa') == $etapaFiltro->id ? 'selected' : '' }} >{{$etapaFiltro->nome}}</option>
            @endforeach
        </select>
        <select name="vendedor" class="form-select" onchange="this.form.submit()">
            <option value="">Todos Vendedores</option>
            @foreach($comercial as $vendedor)
                <option value="{{$vendedor->id}}" {{ request('vendedor') == $vendedor->id ? 'selected' : '' }} >{{$vendedor->usuario}}</option>
            @endforeach
        </select>
        <select name="periodo" class="form-select" onchange="this.form.submit()">
            <option value="15" {{ request('periodo') == 15 || request('periodo') === null ? 'selected' : '' }}>Últimos 15 dias</option>
            <option value="30" {{ request('periodo') == 30 ? 'selected' : '' }}>Últimos 30 dias</option>
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
                <div class="crm-kanban-col-body sortable-col" data-etapa-id="{{ $etapa->id }}">
                    @foreach($leads as $lead)
                        @if($lead->status_id == $etapa->id)
                        <div class="crm-kanban-card" data-lead-id="{{ $lead->id }}">
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
                                    <button class="btn" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarLead{{ $lead->id }}"><i class="bi bi-pencil"></i></button>
                                </div>
                            </div>
                        </div>


<!-- Modal Detalhes Lead -->
<div class="modal fade" id="modalDetalhesLead{{ $lead->id }}" tabindex="-1" aria-labelledby="modalDetalhesLeadLabel{{ $lead->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="min-height: 70vh;">
      <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
        <h5 class="modal-title" id="modalDetalhesLeadLabel{{ $lead->id }}">
          <i class="bi bi-person-circle me-2"></i>Detalhes do Lead
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-12">
            <div class="card shadow-sm border-0 mb-3" style="background: #f8fafc;">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-person-circle me-2" style="font-size:2rem;color:var(--primary-color)"></i>
                  <h4 class="mb-0">{{ $lead->cliente->nome ?? '' }}</h4>
                  @if($lead->cliente->tipo_cliente == 'novo')
                      <span class="badge rounded-pill bg-success ms-2 text-white" style="font-size: 0.85rem;">
                        <i class="bi bi-star-fill me-1"></i>Cliente Novo
                      </span>
                  @elseif($lead->cliente->tipo_cliente == 'renovacao')
                      <span class="badge rounded-pill bg-warning ms-2 text-dark" style="font-size: 0.85rem;">
                        <i class="bi bi-arrow-clockwise me-1"></i>Renovação
                      </span>
                  @elseif($lead->cliente->tipo_cliente == 'resgatado')
                      <span class="badge rounded-pill bg-info ms-2 text-white" style="font-size: 0.85rem;">
                        <i class="bi bi-recycle me-1"></i>Resgatado
                      </span>
                  @endif
                  @if($lead->orcamento_gerado)
                    <span class="badge rounded-pill bg-primary ms-2 text-white" style="font-size: 0.85rem;">
                      <i class="bi bi-file-earmark-check me-1"></i>Orçamento OK
                    </span>
                  @else
                    <span class="badge rounded-pill bg-warning ms-2 text-dark" style="font-size: 0.85rem;">
                      <i class="bi bi-file-earmark-x me-1"></i>Sem Orçamento
                    </span>
                  @endif
                  @if($lead->contrato_gerado)
                    <span class="badge rounded-pill bg-success ms-2 text-white" style="font-size: 0.85rem;">
                      <i class="bi bi-file-earmark-text me-1"></i>Contrato OK
                    </span>
                  @else
                    <span class="badge rounded-pill bg-warning ms-2 text-dark" style="font-size: 0.85rem;">
                      <i class="bi bi-file-earmark-x me-1"></i>Sem Contrato
                    </span>
                  @endif
                  <form method="GET" action="{{ route('gerar.orcamentoLead', $lead->id) }}" class="ms-2 d-inline">
                    <button type="submit" class="btn btn-orcamento align-middle">
                      {{ $lead->orcamento_gerado ? 'Gerar outro orçamento' : 'Gerar Orçamento' }}
                    </button>
                  </form>
                  <button type="button" class="btn btn-orcamento align-middle ms-2" data-bs-toggle="modal" data-bs-target="#modalGerarContrato{{ $lead->id }}">
                    <i class="bi bi-file-earmark-text"></i> Gerar Contrato
                  </button>
                  <button type="button" class="btn btn-orcamento align-middle ms-2" data-bs-toggle="modal" data-bs-target="#modalEnviarContrato{{ $lead->id }}">
                    <i class="bi bi-file-earmark-text"></i> Enviar Contrato
                  </button>
                </div>
                <div class="row mb-1">
                  <div class="col-md-6">
                    <strong>CPF/CNPJ:</strong> <span class="crm-value">{{ $lead->cliente->cnpj ?? '-' }}</span>
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
                 <div class="col-md-6">
                   <strong>Endereço:</strong>
                   <span class="crm-value">
                     {{ $lead->cliente->endereco ?
                       $lead->cliente->endereco->rua . ', ' .
                       $lead->cliente->endereco->numero .
                       ($lead->cliente->endereco->complemento ? ' - ' . $lead->cliente->endereco->complemento : '') . ', ' .
                       $lead->cliente->endereco->bairro . ', ' .
                       $lead->cliente->endereco->cidade . ' - ' .
                       $lead->cliente->endereco->uf . ' | CEP: ' .
                       $lead->cliente->endereco->cep .
                       ($lead->cliente->endereco->distancia ? ' | Distância: ' . $lead->cliente->endereco->distancia . ' km' : '')
                       : '-' }}
                   </span>
                 </div>
                </div>
                <div class="row mb-1">
                  <div class="col-md-6">
                    <strong>Número de Funcionários:</strong> <span class="crm-value">{{ $lead->num_funcionarios ?? '-' }}</span>
                  </div>
                  <div class="col-md-6">
                    <strong>Indicado por:</strong> 
                    @if($lead->indicadoPor)
                      <span class="crm-value">{{ $lead->indicadoPor->nome }}</span>
                    @else
                      <span class="crm-value">Sem indicação externa</span>
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
                <div class="mb-3 text-center">
                  <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-send"></i> Enviar
                  </button>
                </div>
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
          <form method="POST" action="{{ route('update.investimento-lead') }}" class="ms-1 d-flex align-items-center" style="display: inline-flex !important; font-size: 0.9rem;">
            @csrf
            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
            <span class="me-1">R$</span>
            <input type="number" 
                   name="investimento"
                   class="form-control form-control-sm investimento-input" 
                   style="width: 90px; display: inline-block; font-size: 0.85rem; padding: 0.2rem 0.4rem;" 
                   value="{{ $lead->valor_definido ?? '' }}" 
                   step="0.01" 
                   min="0"
                   data-valor-min="{{ $lead->valor_min_sugerido ?? 0 }}"
                   data-valor-max="{{ $lead->valor_max_sugerido ?? 0 }}"
                   onblur="validateInvestimento(this)">
            <span class="ms-2 fw-semibold">Parcelas:</span>
            <input type="number" 
                   name="num_parcelas"
                   class="form-control form-control-sm ms-1" 
                   style="width: 60px; display: inline-block; font-size: 0.85rem; padding: 0.2rem 0.4rem;" 
                   value="{{ $lead->num_parcelas ?? '' }}" 
                   min="1"
                   max="60">
            <button type="submit" class="btn btn-sm btn-outline-primary ms-1" title="Salvar investimento" style="padding: 0.15rem 0.3rem; font-size: 0.75rem;">
              <i class="bi bi-check"></i>
            </button>
            <span class="ms-1 feedback-text" style="font-size: 0.8rem; font-style: italic;"></span>
          </form>
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-currency-dollar me-2 text-info" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Valor Sugerido:</span>
          <span class="ms-1 text-dark" style="font-size: 0.9rem;">
            @if($lead->valor_min_sugerido && $lead->valor_max_sugerido)
              R$ {{ number_format($lead->valor_min_sugerido, 2, ',', '.') }} - R$ {{ number_format($lead->valor_max_sugerido, 2, ',', '.') }}
            @elseif($lead->valor_min_sugerido)
              R$ {{ number_format($lead->valor_min_sugerido, 2, ',', '.') }}
            @elseif($lead->valor_max_sugerido)
              R$ {{ number_format($lead->valor_max_sugerido, 2, ',', '.') }}
            @else
              -
            @endif
          </span>
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-calculator me-2 text-primary" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Comissão Estipulada:</span>
          @if($lead->valor_definido)
            <span class="ms-1 text-dark" style="font-size: 0.9rem;">
              R$ {{ number_format($lead->comissao_estipulada ?? 0, 2, ',', '.') }}
            </span>
          @else
            <span class="ms-1 text-muted" style="font-size: 0.9rem; font-style: italic;">
              <i class="bi bi-info-circle me-1"></i>Defina um valor de investimento para calcular a comissão
            </span>
          @endif
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-graph-up me-2 text-success" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Retorno da Empresa:</span>
          @if($lead->valor_definido)
            <span class="ms-1 text-dark" style="font-size: 0.9rem;">
              R$ {{ number_format($lead->retorno_empresa ?? 0, 2, ',', '.') }}
            </span>
            <span class="ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Cálculo estimado: valor bruto menos impostos e comissão. Não inclui custos administrativos, operacionais ou estruturais da empresa.">
              <i class="bi bi-exclamation-circle text-warning" style="font-size: 0.8rem;"></i>
            </span>
          @else
            <span class="ms-1 text-muted" style="font-size: 0.9rem; font-style: italic;">
              <i class="bi bi-info-circle me-1"></i>Defina um valor de investimento para calcular o retorno
            </span>
          @endif
        </li>
        @if(Auth::user()->tipo == 'admin')
        <li class="d-flex align-items-center mb-2">
          <i class="bi bi-cash-stack me-2 text-warning" style="font-size:1.1rem;"></i>
          <span class="fw-semibold">Comissão Personalizada:</span>
          <form method="POST" action="{{ route('update.comissao-personalizada') }}" class="ms-1 d-flex align-items-center" style="display: inline-flex !important; font-size: 0.9rem;">
            @csrf
            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
            <span class="me-1">R$</span>
            <input type="number" 
                   name="comissao_personalizada"
                   class="form-control form-control-sm comissao-personalizada-input" 
                   style="width: 120px; display: inline-block; font-size: 0.85rem; padding: 0.2rem 0.4rem;" 
                   value="{{ $lead->comissao_personalizada ?? '' }}" 
                   step="0.01" 
                   min="0"
                   placeholder="0,00">
            <button type="submit" class="btn btn-sm btn-outline-warning ms-1" title="Salvar comissão personalizada" style="padding: 0.15rem 0.3rem; font-size: 0.75rem;">
              <i class="bi bi-check"></i>
            </button>
            @if($lead->comissao_personalizada)
              <span class="ms-1 badge bg-warning text-dark" style="font-size: 0.7rem;">
                <i class="bi bi-star-fill me-1"></i>Personalizada
              </span>
            @endif
          </form>
        </li>
        @endif
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
  
  <!-- Campo colapsável para logs de movimentação -->
  <div class="card border-0 shadow-sm mb-3" style="background: #fafdff;">
    <div class="card-header bg-light border-0">
      <button class="btn btn-link text-decoration-none p-0 w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#logsMovimentacao{{ $lead->id }}" aria-expanded="false" aria-controls="logsMovimentacao{{ $lead->id }}">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <i class="bi bi-clock-history me-2 text-primary" style="font-size:1.1rem;"></i>
            <span class="fw-semibold">Histórico de Movimentações</span>
          </div>
          <i class="bi bi-chevron-down" id="iconLogs{{ $lead->id }}"></i>
        </div>
      </button>
    </div>
    <div class="collapse" id="logsMovimentacao{{ $lead->id }}">
      <div class="card-body p-3">
        @if($lead->activities()->where('log_name', 'lead_usuario')->count() > 0)
          <div class="timeline-logs">
            @foreach ($lead->activities()->where('log_name', 'lead_usuario')->latest()->get() as $log)
              <div class="log-item d-flex align-items-start mb-3 p-2 border-start border-3 border-primary bg-light rounded">
                <div class="flex-shrink-0 me-3">
                  <i class="bi bi-circle-fill text-primary" style="font-size: 0.5rem;"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center mb-1">
                    <small class="text-muted me-2">
                      <i class="bi bi-calendar3 me-1"></i>
                      {{ $log->created_at->format('d/m/Y H:i') }}
                    </small>
                    <small class="text-primary fw-semibold">
                      <i class="bi bi-person me-1"></i>
                      {{ $log->causer?->name ?? 'Sistema' }}
                    </small>
                  </div>
                  <div class="log-description">
                    {{ $log->description }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center text-muted py-3">
            <i class="bi bi-info-circle me-2"></i>
            Nenhuma movimentação registrada ainda
          </div>
        @endif
      </div>
    </div>
  </div>
  </div>
  <div class="modal-footer flex-column align-items-stretch">
    <div class="mb-2 w-100">
      <label class="form-label"><b>Trocar de etapa:</b></label>
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

<!-- Modal Gerar Contrato -->
<div class="modal fade" id="modalGerarContrato{{ $lead->id }}" tabindex="-1" aria-labelledby="modalGerarContratoLabel{{ $lead->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalGerarContratoLabel{{ $lead->id }}">Gerar Contrato (ID: {{ $lead->id }})</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('gerar.contrato-index') }}" method="POST">
          @csrf
          <input type="hidden" name="lead_id" value="{{ $lead->id }}">
          <div class="mb-3">
            <label for="num_parcelas" class="form-label">Quantas parcelas acertadas com o cliente?</label>
            <input type="number" name="num_parcelas" id="num_parcelas" class="form-control" min="1" value="{{$lead->num_parcelas ?? ''}}" required>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Gerar Contrato</button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Enviar Contrato para assinatura (simples) -->
<div class="modal fade" id="modalEnviarContrato{{ $lead->id }}" tabindex="-1" aria-labelledby="modalEnviarContratoLabel{{ $lead->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEnviarContratoLabel{{ $lead->id }}">Enviar Contrato para assinatura</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p>ID do Cliente: <strong>{{ $lead->id }}</strong></p>
          <form action="{{route('teste.autentique')}}" enctype= multipart/form-data method="POST" class="p-3 rounded shadow-sm border bg-light">
            @csrf
            <input type="hidden" name="lead_id" value="{{$lead->id}}">
            <div class="mb-3">
              <label for="Nome_Documento" class="form-label fw-semibold">Nome do Documento</label>
              <input type="text" name="nome_documento" class="form-control" id="Nome_Documento" placeholder="Ex: Contrato de Prestação de Serviços" required>
            </div>
            <div class="mb-3">
              <label for="Documento" class="form-label fw-semibold">Documento para assinatura</label>
              <input type="file" name="documento" id="Documento" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-people"></i> Signatários</label>
              <div id="signatariosList{{ $lead->id }}">
                <div class="input-group mb-2">
                  <input type="email" class="form-control" name="emails[]" placeholder="E-mail do signatário" required>
                  <button class="btn btn-danger btn-remove-signatario" type="button" style="display:none;">&times;</button>
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="adicionarSignatario({{ $lead->id }})">
                <i class="bi bi-plus"></i> Adicionar signatário
              </button>
            </div>
            <div class="">
              <button type="submit" class="btn btn-primary col-sm-12">Enviar</button>
            </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Lead -->
<div class="modal fade" id="modalEditarLead{{ $lead->id }}" tabindex="-1" aria-labelledby="modalEditarLeadLabel{{ $lead->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
        <h5 class="modal-title" id="modalEditarLeadLabel{{ $lead->id }}">
          <i class="bi bi-pencil-square me-2"></i>Editar Lead
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p>ID do Lead: <strong>{{ $lead->id }}</strong></p>
        <p>Cliente: {{$lead->cliente->nome}}</p>
        <form action="{{route('update.lead')}}" method="POST">
          @csrf
          <input type="hidden" name="status_id" value="{{$etapa->id}}">
          <input type="hidden" name="cliente_id" value="{{$lead->cliente->id}}">
          <input type="hidden" name="lead_id" value="{{$lead->id}}">
          <div class="mb-3">
              <label for="num_funcionarios" class="form-label">Numero Funcionarios</label>
              <input type="number" name="num_funcionarios" id="" class="form-control" step="0.01" min="0" value="{{$lead->num_funcionarios ? $lead->num_funcionarios : ''}}">
          </div>
          <div class="mb-3">
              <label for="contato" class="form-label">Nome do Contato</label>
              <input type="text" name="nome_contato" id="contato" class="form-control" step="0.01" min="0" value="{{$lead->nome_contato ? $lead->nome_contato : ''}}">
          </div>
          <div class="mb-3">
            <label for="observacoes" class="form-label">Próximo contato</label>
            <input type="datetime-local" name="proximo_contato" class="form-control" id="" value="{{$lead->proximo_contato ? date('Y-m-d\TH:i', strtotime($lead->proximo_contato)) : ''}}">
          </div>
          @if(auth()->user()->google_access_token)
          <div class="mb-3">
            <label class="form-label d-block">Adicionar na sua agenda?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="adicionar_agenda" id="agenda_sim_{{ $lead->id }}" value="1" {{ $lead->adicionar_agenda ? 'checked' : '' }}>
              <label class="form-check-label" for="agenda_sim_{{ $lead->id }}">Sim</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="adicionar_agenda" id="agenda_nao_{{ $lead->id }}" value="0" {{ !$lead->adicionar_agenda ? 'checked' : '' }}>
              <label class="form-check-label" for="agenda_nao_{{ $lead->id }}">Não</label>
            </div>
          </div>
          @else 
          <input type="hidden" name="adicionar_agenda" value="0">
          @endif
          <div class="mb-3">
            <label for="email" class="form-label">Observações</label>
            <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{$lead->observacoes ? $lead->observacoes : ''}}</textarea>
          </div>
          
          <div class="mb-3">
            <label class="form-label d-block">Houve indicação externa?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="indicacao_externa" id="indicacao_sim_{{ $lead->id }}" value="1" {{ $lead->recomendador_id ? 'checked' : '' }}>
              <label class="form-check-label" for="indicacao_sim_{{ $lead->id }}">Sim</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="indicacao_externa" id="indicacao_nao_{{ $lead->id }}" value="0" {{ !$lead->recomendador_id ? 'checked' : '' }}>
              <label class="form-check-label" for="indicacao_nao_{{ $lead->id }}">Não</label>
            </div>
          </div>
          
          <div class="mb-3" id="campoRecomendador_{{ $lead->id }}" style="display: {{ $lead->recomendador_id ? 'block' : 'none' }};">
            <label for="recomendador_{{ $lead->id }}" class="form-label">Recomendador</label>
            <select name="recomendador_id" id="recomendador_{{ $lead->id }}" class="form-control">
              <option value="">Selecione um recomendador</option>
              @foreach($recomendadores as $recomendador)
                <option value="{{$recomendador->id}}" {{ $lead->recomendador_id == $recomendador->id ? 'selected' : '' }}>{{$recomendador->nome}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <a href="{{ route('delete.lead', ['id' => $lead->id]) }}" class="btn btn-danger" title="Excluir Lead">
            <i class="bi bi-trash"></i>
          </a>
          <button type="button" class="btn btn-secondary ms-auto" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
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

<!-- Modal de cadastro -->
<div class="modal fade" id="modalCadastroLead" tabindex="-1" aria-labelledby="modalCadastroLeadLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{route('create.lead')}}" method="POST">
        @csrf
        <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
          <h5 class="modal-title" id="modalCadastroLeadLabel">
            <i class="bi bi-plus-circle me-2"></i>Cadastrar Nova Oportunidade
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
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
              <label for="num_funcionarios" class="form-label">Numero de funcionarios</label>
              <input type="number" name="num_funcionarios" id="" class="form-control" step="0.01" min="0">
          </div>
          <div class="mb-3">
              <label for="contato" class="form-label">Nome do Contato</label>
              <input type="text" name="nome_contato" id="contato" class="form-control" step="0.01" min="0">
          </div>
          <div class="mb-3">
            <label for="observacoes" class="form-label">Próximo contato</label>
            <input type="datetime-local" name="proximo_contato" class="form-control" id="">
          </div>
          @if(auth()->user()->google_access_token)
          <div class="mb-3">
            <label class="form-label d-block">Adicionar na sua agenda?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="adicionar_agenda" id="agenda_sim" value="1">
              <label class="form-check-label" for="agenda_sim">Sim</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="adicionar_agenda" id="agenda_nao" value="0" checked>
              <label class="form-check-label" for="agenda_nao">Não</label>
            </div>
          </div>
          @else 
          <input type="hidden" name="adicionar_agenda" value="0">
          @endif
          <div class="mb-3">
            <label for="email" class="form-label">Observações</label>
            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
          </div>
          
          <div class="mb-3">
            <label class="form-label d-block">Houve indicação externa?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="indicacao_externa" id="indicacao_sim" value="1">
              <label class="form-check-label" for="indicacao_sim">Sim</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="indicacao_externa" id="indicacao_nao" value="0" checked>
              <label class="form-check-label" for="indicacao_nao">Não</label>
            </div>
          </div>
          
          <div class="mb-3" id="campoRecomendador" style="display: none;">
            <label for="recomendador" class="form-label">Recomendador</label>
            <select name="recomendador_id" id="recomendador" class="form-control">
              <option value="">Selecione um recomendador</option>
              @foreach($recomendadores as $recomendador)
                <option value="{{$recomendador->id}}">{{$recomendador->nome}}</option>
              @endforeach
            </select>
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
    
    // Controle do campo de recomendador
    const campoRecomendador = document.getElementById('campoRecomendador');
    const indicacaoSim = document.getElementById('indicacao_sim');
    const indicacaoNao = document.getElementById('indicacao_nao');
    
    indicacaoSim.addEventListener('change', function() {
      if (this.checked) {
        campoRecomendador.style.display = 'block';
      }
    });
    
    indicacaoNao.addEventListener('change', function() {
      if (this.checked) {
        campoRecomendador.style.display = 'none';
        document.getElementById('recomendador').value = '';
      }
    });
    
    // Controle do campo de recomendador para modais de edição
    document.querySelectorAll('[id^="indicacao_sim_"]').forEach(function(radio) {
      radio.addEventListener('change', function() {
        if (this.checked) {
          const leadId = this.id.split('_')[2];
          const campoRecomendador = document.getElementById('campoRecomendador_' + leadId);
          campoRecomendador.style.display = 'block';
        }
      });
    });
    
    document.querySelectorAll('[id^="indicacao_nao_"]').forEach(function(radio) {
      radio.addEventListener('change', function() {
        if (this.checked) {
          const leadId = this.id.split('_')[2];
          const campoRecomendador = document.getElementById('campoRecomendador_' + leadId);
          const selectRecomendador = document.getElementById('recomendador_' + leadId);
          campoRecomendador.style.display = 'none';
          selectRecomendador.value = '';
        }
      });
    });
  });

  // Função para adicionar/remover signatários dinamicamente
  function adicionarSignatario(leadId) {
    const list = document.getElementById('signatariosList' + leadId);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
      <input type="email" class="form-control" name="emails[]" placeholder="E-mail do signatário" required>
      <button class="btn btn-danger btn-remove-signatario" type="button">&times;</button>
    `;
    div.querySelector('.btn-remove-signatario').addEventListener('click', function() {
      div.remove();
      atualizarBotoesRemover(list);
    });
    list.appendChild(div);
    atualizarBotoesRemover(list);
  }
  function atualizarBotoesRemover(list) {
    const grupos = list.querySelectorAll('.input-group');
    grupos.forEach((grupo, idx) => {
      const btn = grupo.querySelector('.btn-remove-signatario');
      btn.style.display = grupos.length > 1 && idx > 0 ? '' : 'none';
    });
  }
  // Inicializar botões de remover para todos os leads ao abrir a modal
  document.addEventListener('DOMContentLoaded', function () {
    @foreach($leads as $lead)
      atualizarBotoesRemover(document.getElementById('signatariosList{{ $lead->id }}'));
    @endforeach
  });
  
  // Função para validar e alterar a cor do input baseado no valor mínimo
  function validateInvestimento(input) {
    const valorMin = parseFloat(input.getAttribute('data-valor-min')) || 0;
    const valorMax = parseFloat(input.getAttribute('data-valor-max')) || Infinity;
    const valorAtual = parseFloat(input.value) || 0;
    
    const form = input.closest('form');
    const feedbackText = form.querySelector('.feedback-text');

    if (valorAtual > 0 && valorAtual < valorMin) {
      input.style.color = 'red';
      input.style.fontWeight = 'bold';
      feedbackText.textContent = '';
      feedbackText.style.color = 'red';
    } else if (valorAtual >= valorMin && valorAtual <= valorMax) {
      input.style.color = 'green';
      input.style.fontWeight = 'bold';
      feedbackText.textContent = '';
      feedbackText.style.color = 'green';
    } else if (valorAtual > valorMax) {
      input.style.color = 'green';
      input.style.fontWeight = 'bold';
      feedbackText.textContent = 'ótimo';
      feedbackText.style.color = 'green';
    } else {
      input.style.color = '';
      input.style.fontWeight = '';
      feedbackText.textContent = '';
      feedbackText.style.color = '';
    }
  }
  
  // Aplicar validação inicial para todos os inputs de investimento
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.investimento-input').forEach(function(input) {
      validateInvestimento(input);
    });
    
    // Controle da rotação do ícone dos logs de movimentação
    document.querySelectorAll('[id^="logsMovimentacao"]').forEach(function(collapseElement) {
      const leadId = collapseElement.id.replace('logsMovimentacao', '');
      const iconElement = document.getElementById('iconLogs' + leadId);
      
      if (iconElement) {
        collapseElement.addEventListener('show.bs.collapse', function() {
          iconElement.style.transform = 'rotate(180deg)';
          iconElement.style.transition = 'transform 0.3s ease';
        });
        
        collapseElement.addEventListener('hide.bs.collapse', function() {
          iconElement.style.transform = 'rotate(0deg)';
          iconElement.style.transition = 'transform 0.3s ease';
        });
      }
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
            onEnd: function(evt) {
                const leadId = evt.item.getAttribute('data-lead-id');
                const newEtapaId = evt.to.getAttribute('data-etapa-id');
                
                if (leadId && newEtapaId) {
                    // Redirecionar para a rota correta com os parâmetros
                    window.location.href = '{{ route("alterStatus.lead", ["lead_id" => ":lead_id", "etapa_id" => ":etapa_id"]) }}'
                        .replace(':lead_id', leadId)
                        .replace(':etapa_id', newEtapaId);
                }
            }
        });
    });
});
</script>

<!-- Adicionar meta CSRF para AJAX -->
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<!-- Inicializar tooltips do Bootstrap -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos os tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover',
            placement: 'top',
            html: true
        });
    });
});
</script>
@endsection
