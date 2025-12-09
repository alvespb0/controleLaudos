<div> 
    <div>
        <div class="filtros-bloco">
            <div style="font-weight: 700; font-size: 1.15rem; color: var(--secondary-color); align-self: center; margin-right: 5rem; white-space: nowrap;">Controle de Laudos</div>

            <!-- Cliente -->
            <div style="width: 180px;">
                <label for="clienteFilter" class="form-label">Cliente</label>
                <input type="text" class="form-control" wire:model.live.debounce.300ms="clienteFilter" id="clienteFilter" id="clienteFilter" placeholder="Buscar...">
            </div>
            <!-- Mês -->
            <div style="width: 150px;">
                <label for="dataFilterMes" class="form-label">Mês</label>
                <input type="month" class="form-control" id="dataFilterMes" wire:model.live.debounce.300ms="dataFilterMes" >
            </div>
            <!-- Status -->
            <div style="width: 160px;">
                <label for="statusFilter" class="form-label">Status</label>
                <select wire:model.live.debounce.300ms="statusFilter" class="form-select" id="statusFilter">
                    <option value="" {{ request('status') === '' ? 'selected' : '' }}>Todos</option>
                    @foreach($status as $s)
                        <option value="{{ $s->id }}" {{ request('status') == $s->id ? 'selected' : '' }}>{{ $s->nome }}</option>
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

            @if($laudos->isEmpty())
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Nenhum Laudo Cadastrado no sistema!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif
            <div class="row g-4">
                @foreach($laudos as $laudo)
                    <div class="col-md-4">
                        <livewire:card-laudos-control 
                            :laudo="$laudo" 
                            :wire:key="$laudo->id" 
                        />
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
    </div>
</div>