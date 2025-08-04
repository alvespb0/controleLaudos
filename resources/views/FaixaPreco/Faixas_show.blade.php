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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0" style="font-weight:700; color:var(--primary-color)"><i class="bi bi-list-ol"></i> Faixas de Precificação  — {{$variavel->nome}}</h4>
        <button class="btn" id="addRowBtn" style="background-color: var(--primary-color); color: white; font-weight: 500;">
            <i class="bi bi-plus-circle"></i> Acrescentar uma linha
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm bg-white rounded" id="faixasTable">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Valor Mínimo</th>
                    <th class="text-center">Valor Máximo</th>
                    <th class="text-center">% Reajuste</th>
                    <th class="text-center">Preço Mínimo</th>
                    <th class="text-center">Preço Máximo</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faixas as $faixa)
                <tr data-id="{{ $faixa->id }}">
                    <td class="text-center view-mode">{{ $faixa->valor_min }}</td>
                    <td class="text-center view-mode">{{ $faixa->valor_max }}</td>
                    <td class="text-center view-mode">{{ $faixa->percentual_reajuste }}</td>
                    <td class="text-center view-mode">{{ $faixa->preco_min }}</td>
                    <td class="text-center view-mode">{{ $faixa->preco_max }}</td>
                    <td class="text-center view-mode">
                        <button class="btn btn-sm btn-outline-primary editBtn" title="Editar"><i class="bi bi-pencil"></i></button>
                        <a href="{{ route('delete.faixa', $faixa->id) }}" class="btn btn-sm btn-outline-danger ms-1" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta faixa?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                    <!-- Edit mode (hidden by default) -->
                    <td class="edit-mode d-none" colspan="6">
                        <form class="d-flex gap-2 align-items-center" method="POST" action="{{route('edit.faixa', $faixa->id)}}">
                            @csrf
                            <input type="hidden" name="variavel_id" value="{{$faixa->variavel->id}}">
                            <input type="number" min=0 step="0.01" name="valor_min" class="form-control form-control-sm text-center" value="{{ $faixa->valor_min }}" required>
                            <input type="number" min=0 step="0.01" name="valor_max" class="form-control form-control-sm text-center" value="{{ $faixa->valor_max }}" required>
                            <input type="number" min=0 step="0.01" name="percentual_reajuste" class="form-control form-control-sm text-center" value="{{ $faixa->percentual_reajuste }}">
                            <input type="number" min=0 step="0.01" name="preco_min" class="form-control form-control-sm text-center" value="{{ $faixa->preco_min }}">
                            <input type="number" min=0 step="0.01" name="preco_max" class="form-control form-control-sm text-center" value="{{ $faixa->preco_max }}">
                            <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                            <button type="button" class="btn btn-secondary btn-sm cancelEditBtn">Cancelar</button>
                        </form>
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
            tr.querySelector('.edit-mode').classList.remove('d-none');
        });
    });
    // Cancelar edição
    document.querySelectorAll('.cancelEditBtn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const tr = btn.closest('tr');
            tr.querySelectorAll('.view-mode').forEach(td => td.classList.remove('d-none'));
            tr.querySelector('.edit-mode').classList.add('d-none');
        });
    });
    // Acrescentar nova linha
    document.getElementById('addRowBtn').addEventListener('click', function() {
        const tbody = document.querySelector('#faixasTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center"><input type="number" min=0 step="0.01" name="valor_min" class="form-control form-control-sm" required></td>
            <td class="text-center"><input type="number" min=0 step="0.01" name="valor_max" class="form-control form-control-sm" required></td>
            <td class="text-center"><input type="number" min=0 step="0.01" name="percentual_reajuste" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="number" min=0 step="0.01" name="preco_min" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="number" min=0 step="0.01" name="preco_max" class="form-control form-control-sm"></td>

            <td class="text-center">
                <form method="POST" action="{{ route('create.faixa') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="variavel_id" value="{{ $variavel->id }}">
                    <input type="hidden" name="valor_min">
                    <input type="hidden" name="valor_max">
                    <input type="hidden" name="percentual_reajuste">
                    <input type="hidden" name="preco_min">
                    <input type="hidden" name="preco_max">
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i> Salvar</button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm removeRowBtn">Cancelar</button>
            </td>
        `;
        tbody.appendChild(tr);
        // Preencher os inputs hidden do form antes de submeter
        const form = tr.querySelector('form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const row = form.closest('tr');
            form.querySelector('input[name="valor_min"]').value = row.querySelector('input[name="valor_min"]').value;
            form.querySelector('input[name="valor_max"]').value = row.querySelector('input[name="valor_max"]').value;
            form.querySelector('input[name="percentual_reajuste"]').value = row.querySelector('input[name="percentual_reajuste"]').value;
            form.querySelector('input[name="preco_min"]').value = row.querySelector('input[name="preco_min"]').value;
            form.querySelector('input[name="preco_max"]').value = row.querySelector('input[name="preco_max"]').value;
            form.submit();
        });
        tr.querySelector('.removeRowBtn').addEventListener('click', function() {
            tr.remove();
        });
    });
});
</script>
@endsection
