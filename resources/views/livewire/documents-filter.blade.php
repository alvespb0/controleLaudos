<div>
    <div>
        <div class="filtros-bloco">
            <div style="font-weight: 700; font-size: 1.15rem; color: var(--secondary-color); align-self: center; margin-right: 5rem; white-space: nowrap;">Controle de Documentos</div>
            <!-- Cliente -->
            <div style="width: 180px;">
                <label for="clienteFilter" class="form-label">Cliente</label>
                <input type="text" class="form-control" wire:model.live.debounce.300ms="clienteFilter" id="clienteFilter" placeholder="Buscar...">
            </div>
            <!-- Mês -->
            <div style="width: 150px;">
                <label for="dataFilterMes" class="form-label">Mês</label>
                <input type="month" class="form-control" id="dataFilterMes" wire:model.live.debounce.300ms="dataFilterMes">
            </div>
            <!-- Status -->
            <div style="width: 160px;">
                <label for="statusFilter" class="form-label">Status</label>
                <select wire:model.live.debounce.300ms="statusFilter" class="form-select" id="statusFilter">
                    <option value="">Todos</option>
                    @foreach($status as $s)
                        <option value="{{ $s->id }}" >{{ $s->nome }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Data Conclusão -->
            <div style="width: 160px;">
                <label for="dataFilterConclusao" class="form-label">Conclusão</label>
                <input type="date" class="form-control" id="dataFilterConclusao" wire:model.live.debounce.300ms="dataFilterConclusao">
            </div>
            <!-- Toggle de Ordenação -->
            <div style="width: 60px;">
                <label class="form-label d-block">Ordem</label>

                <button type="button"
                    wire:click="toggleOrdenacao"
                    class="btn btn-outline-secondary px-2 w-100"
                    title="Ordenar por {{ $ordenarPor === 'desc' ? 'mais antigos' : 'mais novos' }}">
                    
                    <i class="bi {{ $ordenarPor === 'desc' ? 'bi-arrow-down-short' : 'bi-arrow-up-short' }}"></i>
                </button>
            </div>
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

    </div>  
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

</div>  