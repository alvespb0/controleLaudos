@extends('templateMain')

@section('content')
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
@php
    $colunas = [
        'Lead' => [
            'icon' => 'bi-lightbulb',
            'desc' => 'Novos contatos',
            'cards' => [
                ['cliente' => 'Empresa Alfa', 'responsavel' => 'João', 'valor' => 12000, 'status' => 'Novo'],
                ['cliente' => 'Beta Ltda', 'responsavel' => 'Maria', 'valor' => 8000, 'status' => 'Frio'],
            ]
        ],
        'Contato' => [
            'icon' => 'bi-telephone-forward',
            'desc' => 'Contato inicial realizado',
            'cards' => [
                ['cliente' => 'Gamma S/A', 'responsavel' => 'Carlos', 'valor' => 15000, 'status' => 'Quente'],
            ]
        ],
        'Proposta' => [
            'icon' => 'bi-file-earmark-text',
            'desc' => 'Proposta enviada',
            'cards' => [
                ['cliente' => 'Delta Corp', 'responsavel' => 'Ana', 'valor' => 20000, 'status' => 'Novo'],
            ]
        ],
        'Negociação' => [
            'icon' => 'bi-handshake',
            'desc' => 'Negociação em andamento',
            'cards' => [
                ['cliente' => 'Epsilon ME', 'responsavel' => 'Pedro', 'valor' => 18000, 'status' => 'Quente'],
            ]
        ],
        'Fechado (Ganho)' => [
            'icon' => 'bi-trophy',
            'desc' => 'Oportunidade ganha',
            'cards' => [
                ['cliente' => 'Zeta Ind.', 'responsavel' => 'Lucas', 'valor' => 25000, 'status' => 'Quente'],
            ]
        ],
        'Fechado (Perdido)' => [
            'icon' => 'bi-x-octagon',
            'desc' => 'Oportunidade perdida',
            'cards' => [
                ['cliente' => 'Theta Ltda', 'responsavel' => 'Julia', 'valor' => 9000, 'status' => 'Frio'],
            ]
        ],
    ];
@endphp
<div class="crm-kanban-container">
    <div class="crm-kanban-header">
        <h1>Kanban de CRM</h1>
        <p class="text-muted mb-0">Arraste as oportunidades entre as etapas do funil.</p>
    </div>
    <div class="crm-kanban-board" id="crmKanbanBoard">
        @foreach($colunas as $coluna => $info)
            <div class="crm-kanban-col">
                <div class="crm-kanban-col-header">
                    <div class="crm-kanban-col-title-row">
                        <span class="crm-kanban-col-title">
                            <i class="bi {{ $info['icon'] }}" title="{{ $coluna }}"></i> {{ $coluna }}
                        </span>
                        <button class="btn-add-card" title="Adicionar Oportunidade"><i class="bi bi-plus"></i></button>
                    </div>
                    <span class="crm-kanban-col-count">{{ count($info['cards']) }} oportunidade(s)</span>
                    <span class="crm-kanban-col-desc">{{ $info['desc'] }}</span>
                </div>
                <div class="crm-kanban-col-body sortable-col" data-coluna="{{ $coluna }}">
                    @foreach($info['cards'] as $i => $card)
                        <div class="crm-kanban-card">
                            <div class="crm-card-title">
                                <i class="bi bi-person-circle"></i> {{ $card['cliente'] }}
                            </div>
                            <div class="crm-card-info">
                                <span><i class="bi bi-person-badge"></i> {{ $card['responsavel'] }}</span>
                            </div>
                            <div class="crm-card-info">
                                <span><i class="bi bi-cash-coin"></i> <span class="crm-value">R$ {{ number_format($card['valor'], 2, ',', '.') }}</span></span>
                            </div>
                            <div class="crm-card-footer">
                                <span class="crm-badge">{{ $card['status'] }}</span>
                                <div class="crm-card-actions">
                                    <button class="btn" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                                    <button class="btn" title="Editar"><i class="bi bi-pencil"></i></button>
                                </div>
                            </div>
                        </div>
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
