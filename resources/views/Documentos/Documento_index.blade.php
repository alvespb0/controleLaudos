@extends('templateMain')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    /* Barra lateral discreta */
    .sidebar-discreta {
        position: fixed;
        top: 80px;
        left: 0;
        height: 70vh;
        width: 48px;
        background: rgba(121,197,182,0.10);
        border-radius: 0 16px 16px 0;
        box-shadow: 2px 0 8px rgba(44,100,92,0.04);
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
    /* Cards de documento mantendo cor do status */
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
        background: var(--primary-color);
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
    /* Badge tipo_documento */
    .badge-tipo {
        display: inline-block;
        padding: 0.35em 0.8em;
        font-size: 0.95em;
        font-weight: 600;
        border-radius: 12px;
        letter-spacing: 0.5px;
        color: #fff;
        margin-right: 0.5em;
        margin-bottom: 0.1em;
        text-transform: uppercase;
    }
    .badge-CAT { background:rgb(255, 51, 0); }
    .badge-ADENDO { background:rgb(17, 184, 161); }
    .badge-PPP { background:rgb(245, 241, 0);}
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
    /* Responsividade aprimorada */
    @media (max-width: 768px) {
        .filtros-bloco {
            flex-direction: column;
            gap: 0.7rem;
            padding: 1rem 0.7rem;
        }
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
</style>
<div class="sidebar-discreta">
    <span class="icon" title="Ir para o topo" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="bi bi-arrow-up"></i></span>
    <span class="icon" title="Indicadores" id="sidebarIndicadores"><i class="bi bi-bar-chart"></i></span>
</div>
<div class="container">
    <!-- Modal de Mensagem -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="messageModalBody">
                    <!-- Mensagem será inserida aqui -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="collapse mb-4" id="indicadores">
        <div class="row g-3">
            @foreach ($status as $s)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card text-white shadow-sm border-0 rounded-3" style="background-color: {{ $s->cor }};">
                        <div class="card-body p-3 text-center">
                            <h6 class="card-title mb-1 text-white">{{ $s->nome }}</h6>
                            <h4 class="fw-bold mb-0">{{ $contagemPorStatus[$s->id] ?? 0 }}</h4>
                            <small>documento</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

<div class="filtros-bloco">
    <form action="{{ route('filter.docIndex') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 w-100">
        <div style="font-weight: 700; font-size: 1.15rem; color: var(--secondary-color); align-self: center; margin-right: 5rem; white-space: nowrap;">Controle de Documentos</div>
        <!-- Cliente -->
        <div style="width: 180px;">
            <label for="clienteFilter" class="form-label">Cliente</label>
            <input type="text" class="form-control" name="search" id="clienteFilter" placeholder="Buscar...">
        </div>
        <!-- Mês -->
        <div style="width: 150px;">
            <label for="dataFilterMes" class="form-label">Mês</label>
            <input type="month" class="form-control" id="dataFilterMes" name="mesCompetencia">
        </div>
        <!-- Status -->
        <div style="width: 160px;">
            <label for="statusFilter" class="form-label">Status</label>
            <select name="status" class="form-select" id="statusFilter">
                <option value="" {{ request('status') === '' ? 'selected' : '' }}>Todos</option>
                @foreach($status as $s)
                    <option value="{{ $s->id }}" {{ request('status') == $s->id ? 'selected' : '' }}>{{ $s->nome }}</option>
                @endforeach
            </select>
        </div>
        <!-- Data Conclusão -->
        <div style="width: 160px;">
            <label for="dataFilterConclusao" class="form-label">Conclusão</label>
            <input type="date" class="form-control" id="dataFilterConclusao" name="dataConclusao">
        </div>
        <!-- Toggle de Ordenação -->
        <div style="width: 60px;">
            <label class="form-label d-block">Ordem</label>
            <button type="submit" name="ordenarPor" value="{{ request('ordenarPor') === 'mais_antigos' ? 'mais_novos' : 'mais_antigos' }}"
                class="btn btn-outline-secondary px-2 w-100" title="Ordenar {{ request('ordenarPor') === 'mais_antigos' ? 'por mais novos' : 'por mais antigos' }}">
                <i class="bi {{ request('ordenarPor') === 'mais_antigos' ? 'bi-arrow-down-short' : 'bi-arrow-up-short' }}"></i>
            </button>
        </div>
        <!-- Botão buscar -->
        <div>
            <label class="form-label d-block invisible">Buscar</label>
            <button type="submit" class="btn btn-primary px-3 py-2 rounded-circle shadow-sm">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
</div>


    @if (session('Error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('Error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if($documentos->isEmpty())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            Nenhum Documento Técnico Cadastrado no sistema!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif
    <div class="row g-4">
        @foreach($documentos as $documento)
        <div class="col-md-4">
            <div class="card h-100 position-relative">
                <div class="card-body">
                    <form id="form-documento-{{ $documento->id }}" action="{{route('update.docIndex')}}" method="POST">
                        @csrf
                        <input type="hidden" name="documento_id" value="{{$documento->id}}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>
                                <span class="badge-tipo badge-{{$documento->tipo_documento}}">{{$documento->tipo_documento}}</span>
                            </span>
                            <div class="status-container position-relative">
                                <div class="status-indicator" style="background-color: {{ $documento->status ? $documento->status->cor : '#808080' }}"></div>
                                <select class="status-select" name="status">
                                    @if(!$documento->status)
                                        <option value="" selected disabled>Sem Status</option>
                                    @endif
                                    @foreach($status as $s)
                                        <option value="{{$s->id}}" data-color="{{$s->cor}}" {{ $documento->status && $documento->status->id === $s->id ? 'selected' : '' }}>
                                            {{$s->nome}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="card-text">
                            <strong>Cliente: </strong>{{$documento->cliente ? $documento->cliente->nome : 'Cliente não definido'}}
                            <br>
                            <strong>Data de Solicitação: </strong>{{$documento->data_elaboracao !== null ? $documento->data_elaboracao : 'Data de aceite não definido'}} 
                            <br>
                            <strong>Data de Conclusao: </strong><input type="date" name="dataConclusao" class="border border-light" value="{{$documento->data_conclusao !== null ? $documento->data_conclusao : ''}}"> 
                            <br> 
                            <strong>Descrição: </strong>{{$documento->descricao !== null ? $documento->descricao : 'nenhuma descrição definida'}}
                            <br>
                            <Strong>Técnico Responsável: </Strong>
                            <select name="tecnicoResponsavel" class="form-select mt-2">
                                <option value="" selected>Selecione um Técnico Responsável</option>
                                @foreach($tecnicos as $tecnico)
                                <option value="{{$tecnico->id}}" {{ ($documento->tecnico && $documento->tecnico->id == $tecnico->id) ? 'selected' : '' }}>
                                    {{$tecnico->usuario}}
                                </option>
                                @endforeach
                            </select>
                            </p>
                        <hr>
                    <div class="d-flex justify-content-between mt-3 gap-2">
                        <button type="submit" class="btn btn-success save-btn" disabled>Salvar</button>
                    </form>
                            
                        <div class="dropdown-acoes">
                            <button type="button" class="btn btn-light btn-acao" title="Ações" onclick="this.parentNode.classList.toggle('open')">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu-acoes">
                                <button type="button" class="btn btn-acao-menu w-100 mb-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#emailModal{{ $documento->id }}"
                                    data-email="{{ $documento->cliente->email }}">
                                    <i class="bi bi-envelope"></i> Enviar Email
                                </button>
                                <button type="button"
                                    class="btn btn-acao-menu w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#whatsappModal{{ $documento->id }}"
                                    title="Iniciar atendimento via WhatsApp"
                                >
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL PARA ENVIO DE EMAIL -->
        <div class="modal fade" id="emailModal{{ $documento->id }}" tabindex="-1" aria-labelledby="emailModalLabel{{ $documento->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enviar Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('envia-email.cliente')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="email" value = "{{$documento->cliente->email}}">
                            <div class="mb-3">
                                <label class="form-label">Destinatário</label>
                                <input type="email" class="form-control recipient-email" value = "{{$documento->cliente->email}}" disabled required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">assunto</label>
                                <input type="text" name="assunto" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensagem</label>
                                <textarea class="form-control" name="body" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" multiple>Anexos</label>
                                <input type="file" class="form-control" name="anexos[]" multiple>
                            </div>
                            <button type="submit" class="btn btn-primary enviar-btn">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM DA MODAL DE ENVIO DE EMAIL -->
         <script>
            // Ao abrir a modal
            var myModal = document.getElementById('emailModal{{ $documento->id }}');
            myModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // O botão que acionou a modal
                var email = button.getAttribute('data-email'); // Pega o email do cliente

                var emailInput = myModal.querySelector('#recipientEmail{{ $documento->id }}');
                emailInput.value = email; // Preenche o campo de email
            });
         </script>
        <!-- MODAL PARA ENVIO DE WHATSAPP -->
        <div class="modal fade" id="whatsappModal{{ $documento->id }}" tabindex="-1" aria-labelledby="whatsappModalLabel{{ $documento->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Atendimento via Zappy Plataforma</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3" role="alert">
                            Esta mensagem abrirá um atendimento na plataforma de WhatsApp para o seu setor.
                        </div>
                        <form action="{{route('atendimento.zappy')}}" method="POST">
                            @csrf
                            <input type="hidden" name="numero" value="{{ $documento->cliente->telefone[0]->telefone ?? '' }}">
                            <div class="mb-3">
                                <label class="form-label">Número do Cliente</label>
                                <input type="text" class="form-control" value="{{ $documento->cliente->telefone[0]->telefone ?? '' }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensagem</label>
                                <textarea class="form-control" name="mensagem" rows="3" required placeholder="Digite sua mensagem..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Enviar pelo Zappy</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM DA MODAL DE WHATSAPP -->
        @endforeach
    </div>
</div>

@if(!$documentos->isEmpty())
<div class="col-auto ms-auto">
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            @if ($documentos->currentPage() > 1)
            <li class="page-item">
            <a class="page-link" href="{{ $documentos->previousPageUrl() }}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
            </li>
            <li class="page-item"><a class="page-link" href="{{ $documentos->previousPageUrl() }}">{{ $documentos->currentPage() - 1}}</a></li>
            @endif
            <li class="page-item active"><a class="page-link" href="{{ $documentos->nextPageUrl() }}">{{ $documentos->currentPage() }}</a></li>
            @if ($documentos->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $documentos->nextPageUrl() }}">{{ $documentos->currentPage() + 1 }}</a></li>
            <li class="page-item">
                <a class="page-link" href="{{ $documentos->nextPageUrl() }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function toggleContatos(documentoId) {
            const contatosDiv = document.getElementById('contatos' + documentoId);
            const button = document.getElementById('toggleContatosBtn' + documentoId);

            if (contatosDiv.style.display === "none") {
                contatosDiv.style.display = "block";
                button.innerHTML = '<i class="bi bi-phone"></i> Ocultar Dados';
            } else {
                contatosDiv.style.display = "none";
                button.innerHTML = '<i class="bi bi-phone"></i> Ver Dados do Cliente';
            }
        }



    // --- Modal de mensagens ---
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageModalBody = document.getElementById('messageModalBody');

    function showMessage(message, isError = false) {
        messageModalBody.innerHTML = message;
        messageModal.show();
    }

    // --- Inicializa cada formulário de documento ---
    function initializeCard(form) {
        const saveBtn = form.querySelector('.save-btn');
        const statusSelect = form.querySelector('.status-select');
        const statusIndicator = form.querySelector('.status-indicator');
        const dataConclusaoInput = form.querySelector('input[name="dataConclusao"]');

        // Define cor inicial do status
        function updateStatusColor() {
            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            const color = selectedOption.dataset.color;
            statusIndicator.style.backgroundColor = color;
        }

        updateStatusColor();

        // Habilita botão salvar se status mudar
        statusSelect.addEventListener('change', () => {
            updateStatusColor();
            saveBtn.disabled = false;
        });

        // Habilita botão salvar se data de conclusão mudar
        if (dataConclusaoInput) {
            dataConclusaoInput.addEventListener('change', () => {
                saveBtn.disabled = false;
            });
        }

        // Habilita botão salvar se algum select mudar (exceto status)
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            if (select !== statusSelect) {
                select.addEventListener('change', () => {
                    saveBtn.disabled = false;
                });
            }
        });

        // --- Listener do submit do formulário ---
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const documentoId = this.querySelector('input[name="documento_id"]').value;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na requisição');
                return response.json();
            })
            .then(data => {
                if (data.message) {
                    showMessage(data.message);
                    if (saveBtn) saveBtn.disabled = true;
                } else if (data.error) {
                    showMessage(data.error, true);
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar o documento:', error);
                showMessage('Ocorreu um erro ao atualizar o documento. Por favor, tente novamente.', true);
            });
        });
    }

    // --- Inicializa todos os forms de documento ---
    document.querySelectorAll('form[id^="form-documento-"]').forEach(form => {
        initializeCard(form);
    });

    // Fechar dropdown de ações ao clicar fora
    window.addEventListener('click', function(e) {
        document.querySelectorAll('.dropdown-acoes').forEach(function(drop) {
            if (!drop.contains(e.target)) {
                drop.classList.remove('open');
            }
        });
    });

    // Sidebar: mostrar indicadores ao clicar no ícone de gráfico
    document.getElementById('sidebarIndicadores').addEventListener('click', function() {
        const collapse = document.getElementById('indicadores');
        if (collapse.classList.contains('show')) {
            collapse.classList.remove('show');
        } else {
            collapse.classList.add('show');
        }
    });
});
</script>

@endsection 