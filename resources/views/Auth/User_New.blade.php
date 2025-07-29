@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3 class="card-title mb-4 text-center text-dark">Cadastro de Operador</h3>
            <form action="{{ route('create.user') }}" method="POST">
                @csrf
            <div class="mb-3">
                <i class="bi bi-person"></i><label for="usuario" class="form-label">&nbspUsuário</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>

                <div class="mb-3">
                    <i class="bi bi-envelope"></i><label for="email" class="form-label">&nbspE-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-key"></i><label for="senha" class="form-label">&nbspSenha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-lightbulb"></i><label for="tipo">&nbspTipo de Usuario</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="" selected>Selecione um tipo de usuário</option>
                        <option value="admin">admin</option>
                        <option value="comercial">comercial</option>
                        <option value="seguranca">seguranca</option>
                    </select>
                </div>

                <div class="mb-3" id="comissaoField" style="display: none;">
                    <i class="bi bi-percent"></i><label for="percentual_comissao" class="form-label">&nbspPercentual de Comissão (%)</label>
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="percentual_comissao" name="percentual_comissao" placeholder="Ex: 5.50">
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    const comissaoField = document.getElementById('comissaoField');
    const comissaoInput = document.getElementById('percentual_comissao');

    tipoSelect.addEventListener('change', function() {
        if (this.value === 'comercial') {
            comissaoField.style.display = 'block';
            comissaoInput.required = true;
        } else {
            comissaoField.style.display = 'none';
            comissaoInput.required = false;
            comissaoInput.value = '';
        }
    });
});
</script>
@endsection
