@extends('templateMain')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Laudos Cadastrados</h2>
        <form action="" method="GET">
            <div class="d-flex gap-3">
                <div class="input-group" style="width: 200px;">
                    <input type="text" class="form-control" name="search" id="clienteFilter" placeholder="Buscar cliente...">
                </div>
                <select name="status" class="form-select" id="statusFilter" style="width: 180px;">
                    <option value="" selected>Todos os status</option>
                    @foreach($status as $s)
                        <option value="{{$s->id}}">{{$s->nome}}</option>
                    @endforeach
                </select>
                <input type="date" class="form-control" id="dataFilter" style="width: 180px;">
                <button type="submit" class="btn btn-primary px-3 py-2 rounded-circle shadow-sm" style="background-color: var(--primary-color); border: none;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    
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
                            <strong>Cliente: </strong>{{$laudo->cliente ? $laudo->cliente->nome : 'Cliente não definido'}} <br>
                            <strong>Numero de Funcionários: </strong>{{$laudo->numero_clientes}} <br>
                            <strong>Data Previsão: </strong>{{$laudo->data_previsao !== null ? $laudo->data_previsao : 'Data de previsão não definida'}} <br> 
                            <strong>Data Conclusao: </strong><input type="date" name="dataConclusao" class="border border-light" value="{{$laudo->data_conclusao !== null ? $laudo->data_conclusao : ''}}"> <br> 
                            <strong>Vendedor: </strong>{{$laudo->comercial ? $laudo->comercial->usuario : 'Vendedor não definido'}} <br>
                            <Strong>Técnico Responsável: </Strong>

                            <select name="tecnicoResponsavel" class="form-select mt-2">
                                <option value="#" selected>Selecione um Técnico Responsável</option>
                                @foreach($tecnicos as $tecnico)
                                <option value="{{$tecnico->id}}" {{ ($laudo->tecnico && $laudo->tecnico->id == $tecnico->id) ? 'selected' : '' }}>
                                    {{$tecnico->usuario}}
                                </option>
                                @endforeach
                            </select>
                        </p>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
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
                    alert(data.message);
                    saveBtn.disabled = true;
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar o laudo:', error);
                alert('Ocorreu um erro ao atualizar o laudo. Por favor, tente novamente.');
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