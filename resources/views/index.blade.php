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

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <h2 class="mb-0">Laudos Cadastrados</h2>
    
    <form action="{{ route('dashboard.filter') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3">
        <!-- Cliente -->
        <div style="width: 180px;">
            <label for="clienteFilter" class="form-label text-muted small mb-1">Cliente</label>
            <input type="text" class="form-control" name="search" id="clienteFilter" placeholder="Buscar...">
        </div>

        <!-- Mês -->
        <div style="width: 150px;">
            <label for="dataFilterMes" class="form-label text-muted small mb-1">Mês</label>
            <input type="month" class="form-control" id="dataFilterMes" name="mesCompetencia">
        </div>

        <!-- Status -->
        <div style="width: 160px;">
            <label for="statusFilter" class="form-label text-muted small mb-1">Status</label>
            <select name="status" class="form-select" id="statusFilter">
                <option value="" {{ request('status') === '' ? 'selected' : '' }}>Todos</option>
                @foreach($status as $s)
                    <option value="{{ $s->id }}" {{ request('status') == $s->id ? 'selected' : '' }}>{{ $s->nome }}</option>
                @endforeach
            </select>
        </div>

        <!-- Data Conclusão -->
        <div style="width: 160px;">
            <label for="dataFilterConclusao" class="form-label text-muted small mb-1">Conclusão</label>
            <input type="date" class="form-control" id="dataFilterConclusao" name="dataConclusao">
        </div>

        <!-- Toggle de Ordenação -->
        <div style="width: 60px;">
            <label class="form-label text-muted small mb-1 d-block">Ordem</label>
            <button type="submit" name="ordenarPor" value="{{ request('ordenarPor') === 'mais_antigos' ? 'mais_novos' : 'mais_antigos' }}"
                class="btn btn-outline-secondary px-2 w-100" title="Ordenar {{ request('ordenarPor') === 'mais_antigos' ? 'por mais novos' : 'por mais antigos' }}">
                <i class="bi {{ request('ordenarPor') === 'mais_antigos' ? 'bi-arrow-down-short' : 'bi-arrow-up-short' }}"></i>
            </button>
        </div>

        <!-- Botão buscar -->
        <div>
            <label class="form-label d-block invisible">Buscar</label>
            <button type="submit" class="btn btn-primary px-3 py-2 rounded-circle shadow-sm" style="background-color: var(--primary-color); border: none;">
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
                            <strong>Cliente: </strong>{{$laudo->cliente ? $laudo->cliente->nome : 'Cliente não definido'}}
                            @if($laudo->esocial)
                                <span class="badge bg-primary rounded-pill">Esocial</span>
                            @endif 
                            @if($laudo->cliente->cliente_novo)
                                <span class="badge bg-success rounded-pill">Cliente Novo</span>
                            @else
                                <span class="badge bg-warning rounded-pill">Cliente Renovação</span>
                            @endif
                            <br>
                            <strong>Numero de Funcionários: </strong>{{$laudo->numero_clientes}} <br>
                            <strong>Data Previsão: </strong>{{$laudo->data_previsao !== null ? $laudo->data_previsao : 'Data de previsão não definida'}} <br> 
                            <strong>Data de Aceite: </strong>{{$laudo->data_aceite !== null ? $laudo->data_aceite : 'Data de aceite não definido'}} <br>
                            <strong>Data Conclusao: </strong><input type="date" name="dataConclusao" class="border border-light" value="{{$laudo->data_conclusao !== null ? $laudo->data_conclusao : ''}}"> <br> 
                            <strong>Vendedor: </strong>{{$laudo->comercial ? $laudo->comercial->usuario : 'Vendedor não definido'}} <br><br>
                            <button type="button" class="btn btn-info" id="toggleContatosBtn{{$laudo->id}}">
                                <i class="bi bi-phone"></i> Ver Dados do cliente
                            </button>
                            <div id="contatos{{$laudo->id}}" class="mt-2" style="display: none;">
                                <strong>Email: </strong>{{ $laudo->cliente->email }} <br>
                                <strong>Telefone(s): </strong>
                                @foreach($laudo->cliente->telefone as $telefone)
                                    {{ $telefone->telefone }} <br>
                                @endforeach
                                <strong>CNPJ:</strong> {{$laudo->cliente->cnpj}} <br>
                                <strong>Endereco: </strong>{{($laudo->cliente->endereco ? $laudo->cliente->endereco : 'Falha ao buscar na receita')}}
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
                    <div class="d-flex justify-content-between mt-3 gap-2">
                        <button type="submit" class="btn btn-success save-btn" disabled>Salvar</button>
                    </form>
                            
                        <button type="button" class="btn btn-secondary enviar-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#emailModal{{ $laudo->id }}"
                            data-email="{{ $laudo->cliente->email }}">
                            Enviar Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL PARA ENVIO DE EMAIL -->
        <div class="modal fade" id="emailModal{{ $laudo->id }}" tabindex="-1" aria-labelledby="emailModalLabel{{ $laudo->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enviar Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('envia-email.cliente')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="email" value = "{{$laudo->cliente->email}}">
                            <div class="mb-3">
                                <label class="form-label">Destinatário</label>
                                <input type="email" class="form-control recipient-email" value = "{{$laudo->cliente->email}}" disabled required>
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
            var myModal = document.getElementById('emailModal{{ $laudo->id }}');
            myModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // O botão que acionou a modal
                var email = button.getAttribute('data-email'); // Pega o email do cliente

                var emailInput = myModal.querySelector('#recipientEmail{{ $laudo->id }}');
                emailInput.value = email; // Preenche o campo de email
            });
         </script>
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
    .modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5); /* Opacidade do fundo */
}

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

    .enviar-btn{
        padding: 0.375rem 1.5rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;  
        border: none;
        background-color: var(--primary-color);      
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
    // --- Alternância de dados de contato ---
    function toggleContatos(laudoId) {
        const contatosDiv = document.getElementById('contatos' + laudoId);
        const button = document.getElementById('toggleContatosBtn' + laudoId);

        if (contatosDiv.style.display === "none") {
            contatosDiv.style.display = "block";
            button.innerHTML = '<i class="bi bi-phone"></i> Ocultar Dados';
        } else {
            contatosDiv.style.display = "none";
            button.innerHTML = '<i class="bi bi-phone"></i> Ver Dados do Cliente';
        }
    }

    document.querySelectorAll('.btn-info').forEach(button => {
        button.addEventListener('click', () => {
            const laudoId = button.id.replace('toggleContatosBtn', '');
            toggleContatos(laudoId);
        });
    });

    // --- Modal de mensagens ---
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageModalBody = document.getElementById('messageModalBody');

    function showMessage(message, isError = false) {
        messageModalBody.innerHTML = message;
        messageModal.show();
    }

    // --- Inicializa cada formulário de laudo ---
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
                console.error('Erro ao atualizar o laudo:', error);
                showMessage('Ocorreu um erro ao atualizar o laudo. Por favor, tente novamente.', true);
            });
        });
    }

    // --- Inicializa todos os forms de laudos ---
    document.querySelectorAll('form[id^="form-laudo-"]').forEach(form => {
        initializeCard(form);
    });
});
</script>
@endsection 