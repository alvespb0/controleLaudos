@extends('templateMain')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container py-4">
    <div class="mb-4 text-center">
        <span style="font-size:2rem; font-weight:700; color:var(--primary-color); letter-spacing:1px;">
            <i class="bi bi-percent"></i> Percentuais de Comissão
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm bg-white rounded">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Tipo de Cliente</th>
                    <th class="text-center">Percentual (%)</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($percentuais as $percentual)
                <tr data-id="{{ $percentual->id }}">
                    <td class="text-center">
                        <strong>{{ ucfirst($percentual->tipo_cliente ?? 'N/A') }}</strong>
                    </td>
                    <td class="text-center">
                        <!-- Modo visualização -->
                        <span class="view-mode">
                            <strong class="text-success">{{ number_format($percentual->percentual ?? 0, 2) }}%</strong>
                        </span>
                        <!-- Modo edição (oculto por padrão) -->
                        <div class="edit-mode d-none d-flex justify-content-center">
                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" 
                                   value="{{ $percentual->percentual ?? 0 }}" style="width: 80px;">
                        </div>
                    </td>
                    <td class="text-center">
                        <!-- Botões modo visualização -->
                        <div class="view-mode">
                            <button class="btn btn-sm btn-outline-primary editBtn" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                        <!-- Botões modo edição -->
                        <div class="edit-mode d-none">
                            <button type="submit" class="btn btn-success btn-sm saveBtn" title="salvar">Salvar</button>
                            <button type="button" class="btn btn-secondary btn-sm cancelBtn" title="cancelar">Cancelar</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alternar para modo de edição
    document.querySelectorAll('.editBtn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const tr = btn.closest('tr');
            tr.querySelectorAll('.view-mode').forEach(td => td.classList.add('d-none'));
            tr.querySelectorAll('.edit-mode').forEach(td => td.classList.remove('d-none'));
        });
    });

    // Cancelar edição
    document.querySelectorAll('.cancelBtn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const tr = btn.closest('tr');
            tr.querySelectorAll('.view-mode').forEach(td => td.classList.remove('d-none'));
            tr.querySelectorAll('.edit-mode').forEach(td => td.classList.add('d-none'));
        });
    });

    // Salvar edição
    document.querySelectorAll('.saveBtn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const tr = btn.closest('tr');
            const percentualId = tr.getAttribute('data-id');
            const input = tr.querySelector('.edit-mode input');
            const novoPercentual = input.value;

            if (confirm(`Tem certeza que deseja alterar o percentual para ${novoPercentual}%?`)) {
                // Criar formulário para enviar dados
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{route('update.percentuais-comissao')}}';
                
                // Adicionar CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Adicionar ID do percentual
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'percentual_id';
                idInput.value = percentualId;
                form.appendChild(idInput);
                
                // Adicionar novo percentual
                const percentualInput = document.createElement('input');
                percentualInput.type = 'hidden';
                percentualInput.name = 'percentual';
                percentualInput.value = novoPercentual;
                form.appendChild(percentualInput);
                
                // Submeter formulário
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
@endsection
