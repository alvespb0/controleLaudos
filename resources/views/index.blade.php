@extends('templateMain')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    /* Barra lateral discreta à direita */
    .sidebar-discreta {
        position: fixed;
        top: 80px;
        right: 0;
        height: 70vh;
        width: 20px;
        background: rgba(121,197,182,0.10);
        border-radius: 16px 0 0 16px;
        box-shadow: -2px 0 8px rgba(44,100,92,0.04);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.2rem;
        z-index: 100;
        padding: 1.2rem 0;
    }
    .sidebar-discreta .icon {
        color: var(--secondary-color);
        font-size: 1.3rem;
        opacity: 0.7;
        transition: color 0.2s, opacity 0.2s;
        cursor: pointer;
    }
    .sidebar-discreta .icon:hover {
        color: var(--primary-color);
        opacity: 1;
    }
    @media (max-width: 768px) {
        .sidebar-discreta { display: none; }
    }
    /* Filtros mais clean */
    .filtros-bloco {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(121,197,182,0.07);
        padding: 1.2rem 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
        align-items: end;
        justify-content: flex-end;
    }
    .filtros-bloco label {
        font-weight: 500;
        color: var(--secondary-color);
    }
    .filtros-bloco .form-control, .filtros-bloco .form-select {
        background: #f8fafc;
        border: 1px solid #e0f7fa;
        transition: border-color 0.2s;
    }
    .filtros-bloco .form-control:focus, .filtros-bloco .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.1rem rgba(121,197,182,0.10);
    }
    .filtros-bloco .btn-primary {
        background: var(--primary-color);
        border: none;
        box-shadow: 0 2px 8px rgba(121,197,182,0.10);
        transition: background 0.2s, transform 0.2s;
    }
    .filtros-bloco .btn-primary:hover {
        background: var(--hover-color);
        transform: translateY(-2px) scale(1.05);
    }
    /* Cards de laudo */
    .card.h-100 {
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(44,100,92,0.08);
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
        background: #fff;
        position: relative;
        overflow: visible;
    }
    .card.h-100:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 8px 32px rgba(44,100,92,0.16);
        z-index: 2;
    }
    .card-title {
        color: var(--secondary-color);
        font-weight: 700;
        font-size: 1.2rem;
    }
    .card .status-container {
        background: #f8fafc;
        border-radius: 8px;
        padding: 0.2rem 0.7rem;
        box-shadow: 0 1px 4px rgba(121,197,182,0.07);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .status-indicator {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px rgba(0,0,0,0.04);
    }
    .save-btn {
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 0.25rem 1rem;
        font-size: 0.95rem;
        border-radius: 6px;
        min-width: 80px;
        height: 36px;
        transition: background 0.2s, transform 0.2s;
        box-shadow: 0 1px 4px rgba(121,197,182,0.07);
    }
    .save-btn:enabled:hover {
        background: var(--hover-color);
        transform: scale(1.05);
    }
    .save-btn:disabled {
        opacity: 0.7;
        background: #bdbdbd;
        color: #fff;
    }
    /* Dropdown de ações: só abre ao clicar */
    .dropdown-acoes {
        position: relative;
        display: inline-block;
    }
    .btn-acao {
        background: var(--light-color);
        color: var(--secondary-color);
        border: none;
        border-radius: 4px;
        font-size: 1.1rem;
        padding: 0.25rem 0.5rem;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, color 0.2s;
    }
    .btn-acao:hover {
        background: var(--primary-color);
        color: #fff;
    }
    .dropdown-menu-acoes {
        display: none;
        position: absolute;
        right: 0;
        top: 110%;
        min-width: 160px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 4px 24px rgba(44,100,92,0.18);
        z-index: 1055;
        padding: 0.5rem;
        margin-top: 0.25rem;
    }
    .dropdown-acoes.open .dropdown-menu-acoes {
        display: block;
    }
    .dropdown-menu-acoes button {
        border-radius: 4px;
        font-size: 0.95rem;
        padding: 0.375rem 0.75rem;
        margin-bottom: 0.25rem;
        border: none;
        width: 100%;
        text-align: left;
        background: none;
        transition: background-color 0.2s;
    }
    .dropdown-menu-acoes button:last-child {
        margin-bottom: 0;
    }
    .dropdown-menu-acoes button:hover {
        background-color: #f8f9fa;
    }
    /* Select de status mais clean */
    .status-select {
        width: 100%;
        height: 32px;
        padding: 0 30px 0 10px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 0.98rem;
        color: var(--gray-color);
        outline: none;
        box-shadow: none;
        appearance: none;
    }
    .status-select:focus {
        outline: none;
        box-shadow: none;
        border: none;
    }
    .status-container::after {
        content: "▼";
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-color);
        font-size: 0.8rem;
        pointer-events: none;
    }
    /* Responsividade aprimorada */
    @media (max-width: 768px) {
        .filtros-bloco {
            flex-direction: column;
            gap: 0.7rem;
            padding: 1rem 0.7rem;
        }
    }
    .obs-display {
        position: relative;
        padding: 0;
        border: none;
        background: none;
        min-height: 1.8rem;
    }
    .obs-display .text-truncate-obs {
        border-bottom: 2px solid #b2dfdb;
        display: inline-block;
        width: 100%;
        padding: 0.1rem 0.5rem 0.1rem 0;
        font-size: 0.97rem;
        color: #495057;
        font-style: italic;
        transition: border-color 0.2s;
    }
    .obs-display.hovering .text-truncate-obs {
        border-bottom-color: var(--primary-color);
    }
    .obs-display .edit-btn {
        position: absolute;
        right: 0.2rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #7b8a8b;
        opacity: 0;
        pointer-events: none;
        font-size: 1.1rem;
        padding: 0;
        margin: 0;
        transition: opacity 0.2s, color 0.2s;
        z-index: 2;
    }
    .obs-display.hovering .edit-btn {
        opacity: 1;
        pointer-events: auto;
        color: var(--primary-color);
    }
    .filtros-bloco form {
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
        align-items: end;
        justify-content: flex-end;
        width: 100%;
    }
    .filtros-bloco form > div {
        margin-left: 0;
    }
    @media (min-width: 768px) {
        .filtros-bloco form > div:first-child {
            margin-left: auto;
        }
    }
    /* Indicadores mais destacados */
    #indicadores .card {
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(44,100,92,0.10);
        border: none;
        transition: transform 0.2s;
    }
    #indicadores .card:hover {
        transform: scale(1.04);
        box-shadow: 0 6px 24px rgba(44,100,92,0.18);
    }
</style>
<div 
    class="container" 
    x-data="{ showIndicadores: false }"
>
    <!-- Sidebar discreta -->
    <div class="sidebar-discreta">
        <span class="icon" title="Ir para o topo" onclick="window.scrollTo({top:0,behavior:'smooth'})">
            <i class="bi bi-arrow-up"></i>
        </span>

        <!-- Agora controlado via Alpine -->
        <span class="icon" 
            title="Indicadores"
            @click="showIndicadores = !showIndicadores"
        >
            <i class="bi bi-bar-chart"></i>
        </span>

        <span class="icon" title="Kanban" onclick="window.location.href='/dashboard/kanban'">
            <i class="bi bi-kanban"></i>
        </span>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="messageModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- INDICADORES — controlado 100% pelo Alpine -->
    <div 
        id="indicadores"
        x-show="showIndicadores"
        x-transition
        class="mb-4"
        style="display: none;"
    >
        <div class="row g-3">
            @foreach ($status as $s)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card text-white shadow-sm border-0 rounded-3" style="background-color: {{ $s->cor }};">
                        <div class="card-body p-3 text-center">
                            <h6 class="card-title mb-1 text-white">{{ $s->nome }}</h6>
                            <h4 class="fw-bold mb-0">{{ $contagemPorStatus[$s->id] ?? 0 }}</h4>
                            <small>laudos</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <livewire:LaudosControl :initialPage="request()->get('page', 1)" key="documents"/>


<script>
    // PARTE DAS OBSERVAÇÕES DO LAUDO
    function toggleObservacao(id) {
        document.getElementById('obs-display-' + id).style.display = 'none';
        document.getElementById('obs-edit-' + id).style.display = 'block';
    }

    function cancelEditObservacao(id) {
        document.getElementById('obs-edit-' + id).style.display = 'none';
        document.getElementById('obs-display-' + id).style.display = 'block';
        
        const textarea = document.querySelector(`#obs-edit-${id} textarea[name="observacao"]`);
        const originalText = document.querySelector(`#obs-text-${id}`).textContent.trim();
        textarea.value = originalText === 'Nenhuma observação' ? '' : originalText;
    }

    document.addEventListener('input', function (e) {
        if (e.target.tagName.toLowerCase() !== 'textarea') return;
        autoResizeTextarea(e.target);
    });

    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // --- Modal ---
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageModalBody = document.getElementById('messageModalBody');

    // Fechar dropdown ao clicar fora
    window.addEventListener('click', function(e) {
        document.querySelectorAll('.dropdown-acoes').forEach(function(drop) {
            if (!drop.contains(e.target)) {
                drop.classList.remove('open');
            }
        });
    });
</script>

@endsection