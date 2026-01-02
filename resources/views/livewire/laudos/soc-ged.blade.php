<div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-download"></i> Baixar GED do SOC
            </h5>
            
            <form wire:submit.prevent="buscarGeds">
                <div class="mb-3">
                    <label for="codGed" class="form-label">
                        <strong>Qual o tipo de laudo desejado?</strong>
                    </label>
                    <select 
                        id="codGed" 
                        class="form-select" 
                        wire:model="codGed"
                        required
                    >
                        <option value="">Selecione o tipo de laudo...</option>
                        <option value="8">PCMSO/Relatório Analítico</option>
                        <option value="15">PGR/LI/LP/LTCAT</option>
                    </select>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar GEDs
                    </button>
                </div>
            </form>
                <hr>
                @if($erroGed)
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ $erroGed }}
                    </div>
                @endif

                <h6 class="mb-3">GEDs Encontrados:</h6>
                <div class="list-group">
                    @forelse($gedsEncontrados as $ged)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $ged['nome_ged'] ?? 'Nome não disponível' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Código: {{ $ged['cod_ged'] ?? 'N/A' }} | 
                                        Data Emissão: {{ $ged['data_emissao'] ?? 'N/A' }}
                                    </small>
                                </div>
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-success"
                                    wire:click="baixarGed({{ $ged['cod_ged'] }})"
                                >
                                    <i class="bi bi-download"></i> Baixar
                                </button>
                            </div>
                        </div>
                    @empty
                    <p>Nenhum item encontrado</p>
                    @endforelse
                </div>
        </div>
    </div>
</div>
