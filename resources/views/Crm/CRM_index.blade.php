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
                                    <button class="btn" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                                    <button class="btn" title="Editar"><i class="bi bi-pencil"></i></button>
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
