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
            <h3 class="card-title mb-4 text-center text-dark">Editar Operador</h3>
            <form action="{{ route('update.user', $user->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-person"></i><label for="usuario" class="form-label">&nbspUsuário</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required value = "{{ $user->name }}">
                </div>

                <div class="mb-3">
                    <i class="bi bi-envelope"></i><label for="email" class="form-label">&nbspE-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required value="{{ $user->email }}">
                </div>

                <div class="mb-3">
                    <i class="bi bi-key"></i><label for="senha" class="form-label">&nbspSenha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-lightbulb"></i><label for="tipo">&nbspTipo de Usuario</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="" selected>Selecione um tipo de usuário</option>
                        <option value="admin" {{$user->tipo == 'admin' ? 'selected' : ''}}>admin</option>
                        <option value="comercial" {{$user->tipo == 'comercial' ? 'selected' : ''}}>comercial</option>
                        <option value="seguranca" {{$user->tipo == 'seguranca' ? 'selected' : ''}}>seguranca</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary col-sm-12">Editar</button>
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

    // Verificar se o tipo atual é comercial para mostrar o campo
    if (tipoSelect.value === 'comercial') {
        comissaoField.style.display = 'block';
        comissaoInput.required = true;
    }

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
