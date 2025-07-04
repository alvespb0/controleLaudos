@extends('templateMain')

@section('content')

<strong><h3>Página ainda em desenvolvimento! Utilizem e qualquer erro avisem.</h3></strong>


<div class="kanban-container shadow-lg w-100">
    
    <div class="kanban-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Controle de Laudos</h1>
            <a href="/dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-grid-3x3"></i> Visualização Cards
            </a>
        </div>
        <div class="kanban-stats">
        <div class="stat-item">
            <span class="stat-value">{{ count($laudos) }}</span>
            <span class="stat-label">Total de Laudos</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $laudos->where('status.nome', '!=', 'Concluido')->count() }}</span>
            <span class="stat-label">Em Andamento</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $laudos->where('status.nome', 'Concluido')->count() }}</span>
            <span class="stat-label">Concluídos</span>
        </div>
        </div>
        @if(Auth::user()->tipo === 'admin')
        <div class="admin-actions">
            <button type="button" class="btn btn-primary" onclick="updateAllPositions()">
                <i class="bi bi-arrow-repeat"></i> Atualizar Posições
            </button>
        </div>
        @endif
    </div>

    <div class="kanban-wrapper">
        @foreach($status as $s)
            <div class="kanban-column" style="--status-color: {{ $s->cor }}" data-status-id="{{ $s->id }}">
                <div class="kanban-column-header" draggable="true" ondragstart="dragColumn(event)" ondragover="allowDropColumn(event)" ondrop="dropColumn(event)">
                    <span>{{$s->nome}}</span>
                    <span class="column-count">{{ $laudos->where('status.nome', $s->nome)->count() }}</span>
                </div>
                <div class="kanban-column-body" ondrop="drop(event)" ondragover="allowDrop(event)">
                    @foreach($laudos as $laudo)
                    @if($laudo->status && $laudo->status->nome == $s->nome)
                        <form action="{{ route('update.laudoKanban') }}" method="POST" class="laudo-form" data-laudo-id="{{$laudo->id}}">
                            @csrf
                            <input type="hidden" name="laudo_id" value="{{$laudo->id}}">
                            <input type="hidden" name="status" value="{{$s->id}}" class="status-input">
                            <div class="kanban-card" draggable="true" id="laudo{{$laudo->id}}" ondragstart="drag(event)">
                                <div class="column-title">
                                    @if($laudo->esocial)
                                        <span class="badge bg-primary rounded-pill">Esocial</span>
                                    @endif 
                                    @if($laudo->cliente->cliente_novo)
                                        <span class="badge bg-success rounded-pill">Cliente Novo</span>
                                    @else
                                        <span class="badge bg-warning rounded-pill">Cliente Renovação</span>
                                    @endif
                                </div>
                                <div class="card-title">{{$laudo->nome}}</div>
                                <div class="card-info">
                                    <div class="info-item">
                                        <i class="bi bi-person-badge"></i>
                                        <span><strong>Cliente:</strong>&nbsp{{$laudo->cliente->nome}}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-building"></i>
                                        <span><strong>Número de funcionários</strong>&nbsp{{$laudo->numero_clientes}}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-calendar-date"></i>
                                        <span><strong>Data de Aceite: </strong>{{$laudo->data_aceite !== null ? $laudo->data_aceite : 'Data de aceite não definido'}}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-calendar-check"></i>
                                        <input type="date" class="form-control form-control-sm" name="dataConclusao" value="{{$laudo->data_conclusao}}">
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-person-workspace"></i>
                                        <select class="form-select form-select-sm" name="tecnicoResponsavel">
                                            <option value="" selected>Selecione o técnico</option>
                                            @foreach($tecnicos as $tecnico)
                                                <option value="{{$tecnico->id}}" {{$laudo->tecnico_id == $tecnico->id ? 'selected' : ''}}>
                                                    {{$tecnico->usuario}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#infoModal{{$laudo->id}}">
                                        <i class="bi bi-info-circle"></i> Mais informações
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success save-btn" disabled onclick="saveChanges({{$laudo->id}})">
                                        <i class="bi bi-save"></i> Salvar
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Coluna para laudos sem status -->
        <div class="kanban-column" style="--status-color: #6c757d" data-status-id="sem_status">
            <div class="kanban-column-header">
                <span>Sem status</span>
                <span class="column-count">{{ $laudos->whereNull('status_id')->count() }}</span>
            </div>
            <div class="kanban-column-body" ondrop="drop(event)" ondragover="allowDrop(event)">
                @foreach($laudos as $laudo)
                @if($laudo->status_id === null)
                    <form action="{{ route('update.laudoIndex') }}" method="POST" class="laudo-form" data-laudo-id="{{$laudo->id}}">
                        @csrf
                        <input type="hidden" name="laudo_id" value="{{$laudo->id}}">
                        <input type="hidden" name="status" value="" class="status-input">
                        <div class="kanban-card" draggable="true" id="laudo{{$laudo->id}}" ondragstart="drag(event)">
                            <div class="column-title">
                                @if($laudo->esocial)
                                    <span class="badge bg-primary rounded-pill">Esocial</span>
                                @endif 
                                @if($laudo->cliente->cliente_novo)
                                    <span class="badge bg-success rounded-pill">Cliente Novo</span>
                                @else
                                    <span class="badge bg-warning rounded-pill">Cliente Renovação</span>
                                @endif
                            </div>
                            <div class="card-title">{{$laudo->nome}}</div>
                            <div class="card-info">
                                <div class="info-item">
                                    <i class="bi bi-person-badge"></i>
                                    <span><strong>Cliente:</strong>&nbsp{{$laudo->cliente->nome}}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-building"></i>
                                    <span><strong>Número de funcionários</strong>&nbsp{{$laudo->numero_clientes}}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar-date"></i>
                                    <span><strong>Data de Aceite: </strong>{{$laudo->data_aceite !== null ? $laudo->data_aceite : 'Data de aceite não definido'}}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar-check"></i>
                                    <input type="date" class="form-control form-control-sm" name="dataConclusao" value="{{$laudo->data_conclusao}}">
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-person-workspace"></i>
                                    <select class="form-select form-select-sm" name="tecnicoResponsavel">
                                        <option value="" selected>Selecione o técnico</option>
                                        @foreach($tecnicos as $tecnico)
                                            <option value="{{$tecnico->id}}" {{$laudo->tecnico_id == $tecnico->id ? 'selected' : ''}}>
                                                {{$tecnico->usuario}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#infoModal{{$laudo->id}}">
                                    <i class="bi bi-info-circle"></i> Mais informações
                                </button>
                                <button type="button" class="btn btn-sm btn-success save-btn" disabled onclick="saveChanges({{$laudo->id}})">
                                    <i class="bi bi-save"></i> Salvar
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modais fora do kanban-wrapper -->
@foreach($laudos as $laudo)
  <div class="modal fade" id="infoModal{{$laudo->id}}" tabindex="-1" aria-labelledby="infoModalLabel{{$laudo->id}}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="infoModalLabel{{$laudo->id}}">Informações do Cliente - {{$laudo->cliente->nome}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="info-group">
            <label><strong>CNPJ:</strong></label>
            <p>{{$laudo->cliente->cnpj}}</p>
          </div>
          <div class="info-group">
            <label><strong>Email:</strong></label>
            <p>{{$laudo->cliente->email}}</p>
          </div>
          <div class="info-group">
            <label><strong>Telefone:</strong></label>
            <p>                                
              @foreach($laudo->cliente->telefone as $telefone)
                {{ $telefone->telefone }} <br>
              @endforeach
            </p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
@endforeach

<style>
    :root {
        --gap: 1.5rem;
        --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --transition: all 0.3s ease;
    }

    .kanban-container {
        padding: 2rem;
        min-height: 100vh;
        background: var(--light-color);
        position: relative;
    }

    .kanban-header {
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .kanban-header h1 {
        font-size: 2rem;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .kanban-stats {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-item {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #3b82f6;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
    }

    .kanban-wrapper {
        display: flex;
        gap: var(--gap);
        overflow-x: auto;
        padding: 0.5rem;
        min-height: calc(100vh - 200px);
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }

    .kanban-column {
        background: #ffffff;
        border-radius: 16px;
        width: 360px;
        min-width: 360px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        box-shadow: var(--card-shadow);
        border-top: 4px solid var(--status-color, #ccc);
        transition: var(--transition);
        max-height: calc(100vh - 200px);
    }

    .kanban-column:hover {
        transform: translateY(-4px);
    }

    .kanban-column-header {
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .column-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #1e293b;
    }

    .column-title i {
        color: var(--status-color);
    }

    .column-count {
        background: #f1f5f9;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        color: #64748b;
    }

    .kanban-column-body {
        padding: 1rem;
        overflow-y: auto;
        flex-grow: 1;
        min-height: 100px;
        background-color: rgba(0, 0, 0, 0.02);
        border-radius: 0 0 16px 16px;
        transition: background-color 0.3s ease;
        max-height: calc(100vh - 250px);
    }

    .kanban-column-body.drag-over {
        background-color: rgba(0, 0, 0, 0.03);
        border: 2px dashed var(--status-color);
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .kanban-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: var(--card-shadow);
        cursor: grab;
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
        user-select: none;
        position: relative;
        transform-origin: center;
    }

    .kanban-card:active {
        cursor: grabbing;
    }

    .kanban-card.dragging {
        opacity: 0.9;
        transform: rotate(2deg) scale(1.03);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        z-index: 1000;
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border: 2px solid #007bff;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .kanban-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .kanban-card.dragging::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--status-color);
        border-radius: 12px 12px 0 0;
        opacity: 0.5;
    }

    .kanban-card.dragging::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--status-color);
        border-radius: 0 0 12px 12px;
        opacity: 0.5;
    }

    .kanban-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        border: 1px solid transparent;
    }

    .kanban-column-body.drag-over {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.05), rgba(0, 123, 255, 0.1));
        border: 2px dashed #007bff;
        border-radius: 8px;
        transform: scale(1.01);
    }

    .modal {
        z-index: 1050;
    }

    .modal-backdrop {
        z-index: 1040;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
        position: relative;
        z-index: 1051;
    }

    .modal-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .modal-footer {
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .modal-open .kanban-wrapper::after {
        display: none;
    }

    .modal-open .kanban-card:hover {
        transform: none;
        box-shadow: var(--card-shadow);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .card-priority {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .card-priority.high {
        background: #fee2e2;
        color: #dc2626;
    }

    .card-priority.medium {
        background: #fef3c7;
        color: #d97706;
    }

    .card-priority.low {
        background: #dcfce7;
        color: #16a34a;
    }

    .card-number {
        font-size: 0.875rem;
        color: #64748b;
    }

    .card-title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.75rem;
    }

    .card-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .info-item i {
        color: var(--status-color);
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
    }

    .card-date {
        font-size: 0.75rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .card-status {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        background: color-mix(in srgb, var(--status-color) 15%, white);
        color: var(--status-color);
    }

    /* Melhorar a visualização do scroll horizontal */
    .kanban-wrapper {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }

    .kanban-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .kanban-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .kanban-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
        border: 2px solid #f1f5f9;
    }

    .kanban-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Adicionar indicador de scroll */
    .kanban-wrapper::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 50px;
        background: linear-gradient(to right, transparent, #f8fafc);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .kanban-wrapper:hover::after {
        opacity: 0;
    }

    .info-group {
        margin-bottom: 1rem;
    }

    .info-group label {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .info-group p {
        margin: 0;
        color: #1e293b;
    }

    .form-control-sm, .form-select-sm {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        height: auto;
    }

    .btn-info {
        background-color: #0ea5e9;
        border-color: #0ea5e9;
        color: white;
    }

    .btn-info:hover {
        background-color: #0284c7;
        border-color: #0284c7;
        color: white;
    }

    .save-btn {
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .save-btn:disabled {
        cursor: not-allowed;
    }

    .save-btn:not(:disabled) {
        opacity: 1;
    }

    /* Estilização da barra de rolagem da coluna */
    .kanban-column-body::-webkit-scrollbar {
        width: 8px;
    }

    .kanban-column-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .kanban-column-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
        border: 2px solid #f1f5f9;
    }

    .kanban-column-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .admin-actions {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }

    .admin-actions .btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .admin-actions .btn i {
        font-size: 1.1rem;
    }

    .kanban-column-header.dragging {
        opacity: 0.8;
        transform: rotate(3deg) scale(1.05);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        background: linear-gradient(135deg, var(--status-color), rgba(var(--status-color-rgb), 0.8));
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .kanban-column-header.drag-over {
        border: 3px dashed var(--status-color);
        background: linear-gradient(135deg, rgba(var(--status-color-rgb), 0.1), rgba(var(--status-color-rgb), 0.2));
        border-radius: 8px;
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(var(--status-color-rgb), 0.3);
    }

    .kanban-column-header {
        cursor: grab;
        transition: all 0.3s ease;
        border-radius: 8px;
        border: 2px solid transparent;
    }

    .kanban-column-header:active {
        cursor: grabbing;
        transform: scale(0.98);
    }

    .kanban-column-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Efeito de elevação para elementos arrastáveis */
    .kanban-card.dragging {
        opacity: 0.9;
        transform: rotate(2deg) scale(1.03);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        z-index: 1000;
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border: 2px solid #007bff;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .kanban-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    /* Efeito de sombra dinâmica */
    .kanban-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        border-radius: inherit;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .kanban-card:hover:before {
        opacity: 1;
    }

    /* Animação de entrada para elementos dropados */
    @keyframes dropIn {
        0% {
            opacity: 0;
            transform: scale(0.8) translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .kanban-card.dropped,
    .kanban-column.dropped {
        animation: dropIn 0.3s ease-out;
    }

    /* Melhorias visuais para o kanban */
    .kanban-wrapper {
        gap: 1rem;
        padding: 1rem;
    }

    .kanban-column {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .kanban-column:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .kanban-column-header {
        padding: 1rem;
        font-weight: 600;
        font-size: 1.1rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .kanban-column-body {
        padding: 1rem;
        min-height: 200px;
        transition: all 0.3s ease;
    }

    /* Efeito de profundidade para cards */
    .kanban-card {
        margin-bottom: 0.75rem;
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    /* Cursor personalizado para elementos arrastáveis */
    .kanban-column-header,
    .kanban-card {
        cursor: grab;
    }

    .kanban-column-header:active,
    .kanban-card:active {
        cursor: grabbing;
    }

    /* Efeito de feedback visual durante o drag */
    .kanban-column-body.drag-over {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.05), rgba(0, 123, 255, 0.1));
        border: 2px dashed #007bff;
        border-radius: 8px;
        transform: scale(1.01);
        box-shadow: 0 4px 16px rgba(0, 123, 255, 0.2);
    }

    /* Efeito de elevação para elementos em hover */
    .kanban-column-header:hover,
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    /* Transições suaves */
    * {
        transition: all 0.2s ease;
    }
</style>

<script>
    // Adiciona event listeners para os campos de técnico e data
    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona todos os cards
        const cards = document.querySelectorAll('.kanban-card');
        
        cards.forEach(card => {
            // Encontra os campos dentro do card
            const tecnicoSelect = card.querySelector('select[name="tecnicoResponsavel"]');
            const dataInput = card.querySelector('input[name="dataConclusao"]');
            const saveBtn = card.querySelector('.save-btn');
            
            // Adiciona listener para mudança no técnico
            if (tecnicoSelect) {
                tecnicoSelect.addEventListener('change', function() {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                    }
                });
            }
            
            // Adiciona listener para mudança na data
            if (dataInput) {
                dataInput.addEventListener('change', function() {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                    }
                });
            }
        });
    });

    /**
     * Função que permite que um elemento seja solto na área
     * @param {Event} ev - O evento de drag and drop
     */
    function allowDrop(ev) {
        // Previne o comportamento padrão do navegador
        ev.preventDefault();
        // Adiciona uma classe visual para indicar que a área pode receber o card
        ev.currentTarget.classList.add('drag-over');
    }

    /**
     * Função chamada quando o elemento sai da área de drop
     * @param {Event} ev - O evento de drag and drop
     */
    function dragLeave(ev) {
        // Remove a classe visual quando o elemento sai da área
        ev.currentTarget.classList.remove('drag-over');
    }

    /**
     * Função chamada quando o elemento começa a ser arrastado
     * @param {Event} ev - O evento de drag and drop
     */
    function drag(ev) {
        const card = ev.target.closest('.kanban-card');
        if (card) {
            ev.dataTransfer.setData("text", card.id);
            card.classList.add('dragging');
            
            // Adiciona um pequeno delay para melhorar o efeito visual
            setTimeout(() => {
                card.style.opacity = '0.9';
            }, 0);
        }
    }

    /**
     * Função chamada quando o elemento é solto na área
     * @param {Event} ev - O evento de drag and drop
     */
    function drop(ev) {
        ev.preventDefault();
        ev.currentTarget.classList.remove('drag-over');
        
        const data = ev.dataTransfer.getData("text");
        const card = document.getElementById(data);
        const dropzone = ev.target.closest('.kanban-column-body');
        
        if (card && dropzone) {
            // Remove a classe de arrastando com uma transição suave
            card.style.opacity = '1';
            card.classList.remove('dragging');
            
            // Adiciona classe para animação de entrada
            card.classList.add('dropped');
            
            // Encontra a coluna de destino e seu ID de status
            const targetColumn = dropzone.closest('.kanban-column');
            const newStatusId = targetColumn.getAttribute('data-status-id');
            
            // Converte a lista de cards em array para manipulação
            const cards = Array.from(dropzone.querySelectorAll('.kanban-card'));
            // Pega a posição Y do mouse
            const mouseY = ev.clientY;
            // Inicializa a nova posição
            let newPosition = 1;
            
            // Encontra onde o card deve ser inserido
            let insertBeforeElement = null;
            for (let existingCard of cards) {
                // Pula o card que está sendo arrastado
                if (existingCard === card) continue;
                // Pega as dimensões e posição do card
                const rect = existingCard.getBoundingClientRect();
                // Calcula o ponto médio do card
                const cardMiddle = rect.top + (rect.height / 2);
                
                // Se o mouse estiver acima do ponto médio, insere antes deste card
                if (mouseY < cardMiddle) {
                    insertBeforeElement = existingCard;
                    break;
                }
                newPosition++;
            }
            
            // Atualiza o status do card no formulário
            const cardForm = card.closest('form');
            const statusInput = cardForm.querySelector('.status-input');
            if (statusInput) {
                statusInput.value = newStatusId;
            }
            
            // Move o card para a nova posição
            if (insertBeforeElement) {
                // Se encontrou um card para inserir antes, pega seu formulário
                const insertBeforeForm = insertBeforeElement.closest('form');
                // Insere o card antes do formulário de referência
                dropzone.insertBefore(cardForm, insertBeforeForm);
            } else {
                // Se não encontrou, adiciona no final
                dropzone.appendChild(cardForm);
            }
            
            // Habilita o botão de salvar apenas do card movido
            const saveBtn = card.querySelector('.save-btn');
            if (saveBtn) {
                saveBtn.disabled = false;
            }
            
            // Remove a classe de animação após a animação terminar
            setTimeout(() => {
                card.classList.remove('dropped');
            }, 300);
        }
    }

    /**
     * Função para salvar as alterações de um card
     * @param {number} laudoId - ID do laudo a ser atualizado
     */
    function saveChanges(laudoId) {
        // Encontra o card e seu formulário
        const card = document.getElementById(`laudo${laudoId}`);
        const form = card.closest('form');
        // Cria um objeto FormData com os dados do formulário
        const formData = new FormData(form);
        // Pega o token CSRF para segurança
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Calcula a posição atual do card
        const dropzone = card.closest('.kanban-column-body');
        const cards = Array.from(dropzone.querySelectorAll('.kanban-card'));
        const position = cards.indexOf(card) + 1;
        formData.append('position', position);

        // Garante que o ID do laudo está correto
        formData.set('laudo_id', laudoId);

        // Desabilita o botão durante o salvamento
        const saveBtn = card.querySelector('.save-btn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';

        // Envia a requisição para o servidor
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição');
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                // Mostra feedback de sucesso
                saveBtn.innerHTML = '<i class="bi bi-check-circle"></i> Salvo!';
                setTimeout(() => {
                    saveBtn.innerHTML = '<i class="bi bi-save"></i> Salvar';
                    saveBtn.disabled = true;
                }, 2000);
            } else {
                throw new Error('Erro ao salvar');
            }
        })
        .catch(error => {
            // Mostra feedback de erro
            console.error('Erro:', error);
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save"></i> Salvar';
            alert('Erro ao salvar as alterações. Por favor, tente novamente.');
        });
    }

    /**
     * Função para arrastar colunas
     * @param {Event} ev - O evento de drag and drop
     */
    function dragColumn(ev) {
        const columnHeader = ev.target.closest('.kanban-column-header');
        if (columnHeader) {
            ev.dataTransfer.setData("text", columnHeader.closest('.kanban-column').getAttribute('data-status-id'));
            columnHeader.classList.add('dragging');
        }
    }

    /**
     * Função para permitir soltar colunas
     * @param {Event} ev - O evento de drag and drop
     */
    function allowDropColumn(ev) {
        ev.preventDefault();
        ev.currentTarget.classList.add('drag-over');
    }

    /**
     * Função para soltar colunas
     * @param {Event} ev - O evento de drag and drop
     */
    function dropColumn(ev) {
        ev.preventDefault();
        ev.currentTarget.classList.remove('drag-over');
        
        const data = ev.dataTransfer.getData("text");
        const draggedColumn = document.querySelector(`[data-status-id="${data}"]`);
        const targetColumn = ev.target.closest('.kanban-column');
        
        if (draggedColumn && targetColumn && draggedColumn !== targetColumn) {
            const kanbanWrapper = document.querySelector('.kanban-wrapper');
            const columns = Array.from(kanbanWrapper.querySelectorAll('.kanban-column'));
            const draggedIndex = columns.indexOf(draggedColumn);
            const targetIndex = columns.indexOf(targetColumn);
            
            // Calcula a posição antiga (antes da movimentação)
            const oldPosition = draggedIndex + 1;
            
            // Move a coluna para a nova posição
            if (draggedIndex < targetIndex) {
                targetColumn.parentNode.insertBefore(draggedColumn, targetColumn.nextSibling);
            } else {
                targetColumn.parentNode.insertBefore(draggedColumn, targetColumn);
            }
            
            // Remove a classe de arrastando
            draggedColumn.querySelector('.kanban-column-header').classList.remove('dragging');
            
            // Adiciona classe para animação de entrada
            draggedColumn.classList.add('dropped');
            
            // Calcula a nova posição após a movimentação
            const newColumns = Array.from(kanbanWrapper.querySelectorAll('.kanban-column'));
            const newPosition = newColumns.indexOf(draggedColumn) + 1;
            
            // Atualiza automaticamente a posição no banco de dados
            updateColumnPosition(data, newPosition, oldPosition);
            
            // Remove a classe de animação após a animação terminar
            setTimeout(() => {
                draggedColumn.classList.remove('dropped');
            }, 300);
        }
    }

    /**
     * Função para atualizar a posição de uma coluna no banco de dados
     * @param {string} statusId - ID do status da coluna
     * @param {number} newPosition - Nova posição da coluna
     * @param {number} oldPosition - Posição antiga da coluna
     */
    function updateColumnPosition(statusId, newPosition, oldPosition) {
        // Pega o token CSRF para segurança
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Envia a requisição para o servidor
        fetch('{{ route("update.column.position") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status_id: statusId,
                position: newPosition,
                old_position: oldPosition
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Posição da coluna atualizada com sucesso');
            } else {
                throw new Error(data.message || 'Erro ao atualizar posição da coluna');
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar posição da coluna:', error);
            // Recarrega a página em caso de erro para manter a consistência
            location.reload();
        });
    }

    function updateAllPositions() {
        const columns = document.querySelectorAll('.kanban-column-body');
        let allPositions = [];
        let statusPositions = [];

        // Captura as posições das colunas (status)
        const statusColumns = document.querySelectorAll('.kanban-column');
        statusColumns.forEach((column, index) => {
            const statusId = column.getAttribute('data-status-id');
            if (statusId && statusId !== 'sem_status') {
                statusPositions.push({
                    status_id: statusId,
                    position: index + 1
                });
            }
        });

        // Captura as posições dos cards (laudos)
        columns.forEach(column => {
            const cards = column.querySelectorAll('.kanban-card');
            cards.forEach((card, index) => {
                const form = card.closest('form');
                if (form) {
                    const laudoId = form.querySelector('input[name="laudo_id"]').value;
                    const statusId = form.querySelector('.status-input').value;
                    allPositions.push({
                        laudo_id: laudoId,
                        status: statusId,
                        position: index + 1
                    });
                }
            });
        });

        // Envia todas as posições para o backend
        fetch('{{ route("update.all.positions") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                positions: allPositions,
                statusPositions: statusPositions 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Posições atualizadas com sucesso!');
                location.reload(); // Recarrega a página para refletir as mudanças
            } else {
                throw new Error(data.message || 'Erro ao atualizar posições');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atualizar posições. Por favor, tente novamente.');
        });
    }
</script>

@endsection 