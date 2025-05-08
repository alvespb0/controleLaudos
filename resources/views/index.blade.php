@extends('templateMain')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
    <div class="mb-3">
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#indicadores" aria-expanded="false" aria-controls="indicadores">
            Mostrar indicadores
        </button>
    </div>

    <div class="collapse mb-4" id="indicadores">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Laudos Cadastrados</h2>
        <form action="{{ route('dashboard.filter') }}" method="GET">
            <div class="d-flex gap-3 align-items-end">
                <div style="width: 200px;">
                    <label for="clienteFilter" class="form-label text-muted small mb-1">Buscar Cliente</label>
                    <input type="text" class="form-control" name="search" id="clienteFilter" placeholder="Buscar cliente...">
                </div>
                <div style="width: 180px;">
                    <label for="dataFilterMes" class="form-label text-muted small mb-1">Mês</label>
                    <input type="month" class="form-control" id="dataFilterMes" name="mesCompetencia">
                </div>
                <div style="width: 180px;">
                    <label for="statusFilter" class="form-label text-muted small mb-1">Status</label>
                    <select name="status" class="form-select" id="statusFilter">
                        <option value="" selected>Todos os status</option>
                        @foreach($status as $s)
                            <option value="{{ $s->id }}">{{ $s->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 180px;">
                    <label for="dataFilterConclusao" class="form-label text-muted small mb-1">Data de Conclusão</label>
                    <input type="date" class="form-control" id="dataFilterConclusao" name="dataConclusao">
                </div>
                <div>
                    <label class="form-label d-block invisible">Buscar</label>
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-circle shadow-sm" style="background-color: var(--primary-color); border: none;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>


    @if (session('Error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('Error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if($laudos->isEmpty())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            Nenhum Laudo Cadastrado no sistema!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif
    <div class="row g-4">
        @foreach($laudos as $laudo)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <form id="form-laudo-{{ $laudo->id }}" action="{{ route('update.laudoIndex') }}" method="POST">
                        @csrf
                        <input type="hidden" name="laudo_id" value="{{$laudo->id}}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">{{$laudo->nome}}</h5>
                            <div class="status-container">
                                <div class="status-indicator" style="background-color: {{ $laudo->status ? $laudo->status->cor : '#808080' }}"></div>
                                <select class="status-select" name="status">
                                    @if(!$laudo->status)
                                        <option value="" selected disabled>Sem Status</option>
                                    @endif
                                    @foreach($status as $s)
                                        <option value="{{$s->id}}" data-color="{{$s->cor}}" {{ $laudo->status && $laudo->status->id === $s->id ? 'selected' : '' }}>
                                            {{$s->nome}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="card-text">
                            <strong>Cliente: </strong>{{$laudo->cliente ? $laudo->cliente->nome : 'Cliente não definido'}} {{$laudo->esocial ? '(Esocial)' : ''}}<br>
                            <strong>Numero de Funcionários: </strong>{{$laudo->numero_clientes}} <br>
                            <strong>Data Previsão: </strong>{{$laudo->data_previsao !== null ? $laudo->data_previsao : 'Data de previsão não definida'}} <br> 
                            <strong>Data de Aceite: </strong>{{$laudo->data_aceite !== null ? $laudo->data_aceite : 'Data de aceite não definido'}} <br>
                            <strong>Data Conclusao: </strong><input type="date" name="dataConclusao" class="border border-light" value="{{$laudo->data_conclusao !== null ? $laudo->data_conclusao : ''}}"> <br> 
                            <strong>Vendedor: </strong>{{$laudo->comercial ? $laudo->comercial->usuario : 'Vendedor não definido'}} <br><br>
                            <button type="button" class="btn btn-info" id="toggleContatosBtn{{$laudo->id}}">
                                <i class="bi bi-phone"></i> Ver Contatos do cliente
                            </button>
                            <div id="contatos{{$laudo->id}}" class="mt-2" style="display: none;">
                                <strong>Email: </strong>{{ $laudo->cliente->email }} <br>
                                <strong>Telefone(s): </strong>
                                @foreach($laudo->cliente->telefone as $telefone)
                                    {{ $telefone->telefone }} <br>
                                @endforeach
                                <br>

                            </div>
                            <Strong>Técnico Responsável: </Strong>
                            <select name="tecnicoResponsavel" class="form-select mt-2">
                                <option value="" selected>Selecione um Técnico Responsável</option>
                                @foreach($tecnicos as $tecnico)
                                <option value="{{$tecnico->id}}" {{ ($laudo->tecnico && $laudo->tecnico->id == $tecnico->id) ? 'selected' : '' }}>
                                    {{$tecnico->usuario}}
                                </option>
                                @endforeach
                            </select>

                            </p>
                        <hr>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success save-btn" disabled>Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@if(!$laudos->isEmpty())
<div class="col-auto ms-auto">
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            @if ($laudos->currentPage() > 1)
            <li class="page-item">
            <a class="page-link" href="{{ $laudos->previousPageUrl() }}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
            </li>
            <li class="page-item"><a class="page-link" href="{{ $laudos->previousPageUrl() }}">{{ $laudos->currentPage() - 1}}</a></li>
            @endif
            <li class="page-item active"><a class="page-link" href="{{ $laudos->nextPageUrl() }}">{{ $laudos->currentPage() }}</a></li>
            @if ($laudos->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $laudos->nextPageUrl() }}">{{ $laudos->currentPage() + 1 }}</a></li>
            <li class="page-item">
                <a class="page-link" href="{{ $laudos->nextPageUrl() }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>
</div>
@endif

<style>
    .card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        color: var(--secondary-color);
        font-weight: bold;
    }

    /* Estilos para o select de status */
    .status-container {
        position: relative;
        width: 150px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-select {
        width: 100%;
        height: 32px;
        padding: 0 30px 0 10px;
        border: none;
        background-color: transparent;
        cursor: pointer;
        font-size: 0.9rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        color: var(--gray-color);
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

    .status-select option {
        padding: 8px;
        background-color: white;
        color: black;
    }

    /* Estilos para os filtros */
    .form-control, .form-select {
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(121, 197, 182, 0.25);
    }

    /* Estilos para o botão de salvar */
    .save-btn {
        padding: 0.375rem 1.5rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .save-btn:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
    }

    .save-btn:not(:disabled):hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    /* Estilo para o input de data */
    .border-light {
        border-color: #ced4da !important;
        border-radius: 4px;
    }

    .border-light:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.2rem rgba(121, 197, 182, 0.25);
    }

    .btn-info {
        background-color: var(--primary-color);
        transition: background-color 0.3s ease;
        border: none;
    }

    .btn-info:hover {
        background-color: var(--hover-color)
    }

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Função para alternar a visibilidade dos contatos
    function toggleContatos(laudoId) {
        const contatosDiv = document.getElementById('contatos' + laudoId);
        const button = document.getElementById('toggleContatosBtn' + laudoId);

        if (contatosDiv.style.display === "none") {
            contatosDiv.style.display = "block";
            button.innerHTML = '<i class="bi bi-phone"></i> Ocultar Contatos';
        } else {
            contatosDiv.style.display = "none";
            button.innerHTML = '<i class="bi bi-phone"></i> Ver Contatos do Cliente';
        }
    }

    // Adiciona o evento de clique ao botão de cada laudo
    const buttons = document.querySelectorAll('.btn-info');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const laudoId = this.id.replace('toggleContatosBtn', '');
            toggleContatos(laudoId);
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    // Inicializa a modal
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageModalBody = document.getElementById('messageModalBody');

    // Função para mostrar mensagem na modal
    function showMessage(message, isError = false) {
        messageModalBody.innerHTML = message;
        messageModal.show();
    }

    // Função para inicializar um card específico
    function initializeCard(form) {
        const saveBtn = form.querySelector('.save-btn');
        const statusSelect = form.querySelector('.status-select');
        const statusIndicator = form.querySelector('.status-indicator');
        const dataConclusaoInput = form.querySelector('input[name="dataConclusao"]');

        // Atualiza a cor do indicador baseado na opção selecionada
        function updateStatusColor() {
            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            const color = selectedOption.dataset.color;
            statusIndicator.style.backgroundColor = color;
        }

        // Inicializa a cor do status
        updateStatusColor();

        // Atualiza a cor quando o status muda
        statusSelect.addEventListener('change', () => {
            updateStatusColor();
            saveBtn.disabled = false;
        });

        // Adiciona evento para o input de data de conclusão
        dataConclusaoInput.addEventListener('change', () => {
            saveBtn.disabled = false;
        });

        const inputs = form.querySelectorAll('select');
        inputs.forEach(input => {
            if (input !== statusSelect) {
                input.addEventListener('change', () => {
                    saveBtn.disabled = false;
                });
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const laudoId = this.querySelector('input[name="laudo_id"]').value;

            fetch(this.action, {
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
                    showMessage(data.message);
                    saveBtn.disabled = true;
                } else if (data.error) {
                    showMessage(data.error, true);
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar o laudo:', error);
                showMessage('Ocorreu um erro ao atualizar o laudo. Por favor, tente novamente.', true);
            });
        });
    }

    // Inicializa todos os cards
    const forms = document.querySelectorAll('form[id^="form-laudo-"]');
    forms.forEach(form => {
        initializeCard(form);
    });
});
</script>
@endsection 