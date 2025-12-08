<div class="w-100 h-100">
    <div class="card h-100 position-relative">
        <div class="card-body">
            <input type="hidden" name="documento_id" value="{{$documento->id}}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span>
                    <span class="badge-tipo badge-{{$documento->tipo_documento}}">{{$documento->tipo_documento}}</span>
                </span>
                <div class="status-container position-relative">
                    <div class="status-indicator" style="background-color: {{ $documento->status ? $documento->status->cor : '#808080' }}"></div>
                    <select class="status-select" wire:model.live.debounce.300ms="statusAlterado">
                        @if(!$documento->status)
                            <option value="">Sem Status</option>
                        @endif
                        @foreach($status as $s)
                            <option value="{{ $s->id }}" data-color="{{ $s->cor }}">
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
</div>
