@extends('templateMain')

@section('content')
<div class="kanban-container">
    <div class="kanban-header">
        <h1>Controle de Laudos</h1>
        <div class="kanban-stats">
        <div class="stat-item">
            <span class="stat-value">{{ count($laudos) }}</span>
            <span class="stat-label">Total de Laudos</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $laudos->where('status.nome', 'Em Análise')->count() }}</span>
            <span class="stat-label">Em Andamento</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $laudos->where('status.nome', 'Concluído')->count() }}</span>
            <span class="stat-label">Concluídos</span>
        </div>
        </div>
    </div>

    <div class="kanban-wrapper">
        @foreach($status as $s)
            <div class="kanban-column" style="--status-color: {{ $s->cor }}" data-status-id="{{ $s->id }}">
                <div class="kanban-column-header">
                    <span>{{$s->nome}}</span>
                    <span class="column-count">{{ $laudos->where('status.nome', $s->nome)->count() }}</span>
                </div>
                <div class="kanban-column-body" ondrop="drop(event)" ondragover="allowDrop(event)">
                    @foreach($laudos as $laudo)
                    @if($laudo->status && $laudo->status->nome == $s->nome)
                        <form action="{{ route('update.laudoIndex') }}" method="POST" class="laudo-form" data-laudo-id="{{$laudo->id}}">
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
        background: #f8fafc;
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
        background-color: rgba(0, 0, 0, 0.05);
    }

    .kanban-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: var(--card-shadow);
        cursor: grab;
        transition: var(--transition);
        border: 1px solid #e2e8f0;
        user-select: none;
    }

    .kanban-card.dragging {
        opacity: 0.5;
        transform: scale(0.95);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .kanban-card:active {
        cursor: grabbing;
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
</style>

<script>
  function allowDrop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('drag-over');
  }

  function dragLeave(ev) {
    ev.currentTarget.classList.remove('drag-over');
  }

  function drag(ev) {
    const card = ev.target.closest('.kanban-card');
    if (card) {
      ev.dataTransfer.setData("text", card.id);
      card.classList.add('dragging');
    }
  }

  function drop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('drag-over');
    
    const data = ev.dataTransfer.getData("text");
    const card = document.getElementById(data);
    const dropzone = ev.target.closest('.kanban-column-body');
    
    if (card && dropzone) {
      card.classList.remove('dragging');
      
      // Pegar o ID do status da coluna de destino
      const targetColumn = dropzone.closest('.kanban-column');
      const newStatusId = targetColumn.getAttribute('data-status-id');
      
      // Atualizar o input hidden do status no formulário do card
      const cardForm = card.closest('form');
      const statusInput = cardForm.querySelector('.status-input');
      if (statusInput) {
        statusInput.value = newStatusId;
        console.log('Status atualizado para:', newStatusId);
      }
      
      // Mover o card para a nova coluna
      dropzone.appendChild(cardForm);
      
      // Habilitar o botão de salvar
      const saveBtn = card.querySelector('.save-btn');
      if (saveBtn) {
        saveBtn.disabled = false;
      }
    }
  }

  // Adicionar eventos para monitorar mudanças nos inputs
  document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.kanban-card');
    cards.forEach(card => {
      // Monitorar mudanças no select de técnico
      const tecnicoSelect = card.querySelector('select[name="tecnicoResponsavel"]');
      if (tecnicoSelect) {
        tecnicoSelect.addEventListener('change', function() {
          const saveBtn = card.querySelector('.save-btn');
          if (saveBtn) {
            saveBtn.disabled = false;
          }
        });
      }

      // Monitorar mudanças no input de data
      const dataInput = card.querySelector('input[name="dataConclusao"]');
      if (dataInput) {
        dataInput.addEventListener('change', function() {
          const saveBtn = card.querySelector('.save-btn');
          if (saveBtn) {
            saveBtn.disabled = false;
          }
        });
      }
    });
  });

  function saveChanges(laudoId) {
    const card = document.getElementById(`laudo${laudoId}`);
    const form = card.closest('form');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Garantir que o laudo_id está correto
    formData.set('laudo_id', laudoId);

    // Log para debug
    console.log('Dados sendo enviados:');
    for (let pair of formData.entries()) {
      console.log(pair[0] + ': ' + pair[1]);
    }

    // Desabilitar o botão durante o salvamento
    const saveBtn = card.querySelector('.save-btn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';

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
      console.error('Erro:', error);
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="bi bi-save"></i> Salvar';
      alert('Erro ao salvar as alterações. Por favor, tente novamente.');
    });
  }
</script>

@endsection 