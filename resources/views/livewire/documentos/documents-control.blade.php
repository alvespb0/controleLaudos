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
                    <livewire:card-documents-control 
                        :documento="$documento" 
                        :wire:key="$documento->id" 
                    />
                </div>
            @endforeach
        </div>
    </div>

    @if(!$documentos->isEmpty())
    <div class="col-auto ms-auto">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                
                {{-- Página anterior --}}
                @if ($documentos->currentPage() > 1)
                <li class="page-item">
                    <button class="page-link" wire:click="previousPage">«</button>
                </li>

                <li class="page-item">
                    <button class="page-link" wire:click="gotoPage({{ $documentos->currentPage() - 1 }})">
                        {{ $documentos->currentPage() - 1 }}
                    </button>
                </li>
                @endif

                {{-- Página atual --}}
                <li class="page-item active">
                    <button class="page-link">
                        {{ $documentos->currentPage() }}
                    </button>
                </li>

                {{-- Próxima página --}}
                @if ($documentos->hasMorePages())
                <li class="page-item">
                    <button class="page-link" wire:click="gotoPage({{ $documentos->currentPage() + 1 }})">
                        {{ $documentos->currentPage() + 1 }}
                    </button>
                </li>

                <li class="page-item">
                    <button class="page-link" wire:click="nextPage">»</button>
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