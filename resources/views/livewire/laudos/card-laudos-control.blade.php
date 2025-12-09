<div>
    <div class="card h-100 position-relative">
        <div class="card-body">
            <form id="form-laudo-{{ $laudo->id }}" action="{{ route('update.laudoIndex') }}" method="POST">
                @csrf
                <input type="hidden" name="laudo_id" value="{{$laudo->id}}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">{{$laudo->nome}}</h5>
                    <div class="status-container position-relative">
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
                    @if($laudo->cliente->tipo_cliente == 'novo')
                        <span class="badge bg-success rounded-pill">Cliente Novo</span>
                    @elseif($laudo->cliente->tipo_cliente == 'renovacao')
                        <span class="badge bg-warning rounded-pill">Cliente Renovação</span>
                    @elseif($laudo->cliente->tipo_cliente == 'resgatado')
                        <span class="badge bg-warning rounded-pill">Cliente Resgatado</span>
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
                    <div class="mb-2 position-relative group">
                        <strong class="d-block mb-1 text-muted small">Observação:</strong>
                        {{-- Exibição --}}
                        <div id="obs-display-{{ $laudo->id }}" 
                            class="obs-display {{ !$laudo->observacao ? 'empty' : '' }}" 
                            onmouseover="this.classList.add('hovering')" 
                            onmouseout="this.classList.remove('hovering')">
                            
                            <div id="obs-text-{{ $laudo->id }}" class="text-truncate-obs {{ !$laudo->observacao ? 'empty-obs' : '' }}">
                                {{ $laudo->observacao ?? 'Nenhuma observação' }}
                            </div>
                            @if(strlen($laudo->observacao) > 200)
                                <a href="javascript:void(0)" 
                                id="toggle-link-{{ $laudo->id }}"
                                class="toggle-link" 
                                onclick="toggleExpandObservacao({{ $laudo->id }})">
                                    Ver mais...
                                </a>
                            @endif
                            <button type="button" class="edit-btn" 
                                    onclick="toggleObservacao({{ $laudo->id }})" 
                                    title="Editar observação">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                        {{-- Edição --}}
                        <div id="obs-edit-{{ $laudo->id }}" class="mt-2" style="display: none;">
                            <textarea name="observacao" class="form-control form-control-sm auto-expand" rows="2"
                                    placeholder="Digite uma observação..."
                                    oninput="enableSave({{ $laudo->id }})">{{ $laudo->observacao }}</textarea>
                            <div class="mt-1 d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEditObservacao({{ $laudo->id }})">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                <hr>
            <div class="d-flex justify-content-between mt-3 gap-2">
                <button type="submit" class="btn btn-modern-primary save-btn" disabled>Salvar</button>
            </form>
                    
                <div class="dropdown-acoes">
                    <button type="button" class="btn btn-light btn-acao" title="Ações" onclick="this.parentNode.classList.toggle('open')">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu-acoes">
                        <button type="button" class="btn btn-acao-menu w-100 mb-1" 
                            data-bs-toggle="modal" 
                            data-bs-target="#emailModal{{ $laudo->id }}"
                            data-email="{{ $laudo->cliente->email }}">
                            <i class="bi bi-envelope"></i> Enviar Email
                        </button>
                        <button type="button"
                            class="btn btn-acao-menu w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#whatsappModal{{ $laudo->id }}"
                            title="Iniciar atendimento via WhatsApp"
                        >
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </button>
                    </div>
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
<!-- MODAL PARA ENVIO DE WHATSAPP -->
<div class="modal fade" id="whatsappModal{{ $laudo->id }}" tabindex="-1" aria-labelledby="whatsappModalLabel{{ $laudo->id }}" aria-hidden="true">
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
                    <input type="hidden" name="numero" value="{{ $laudo->cliente->telefone[0]->telefone ?? '' }}">
                    <div class="mb-3">
                        <label class="form-label">Número do Cliente</label>
                        <input type="text" class="form-control" value="{{ $laudo->cliente->telefone[0]->telefone ?? '' }}" disabled>
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
</div>
