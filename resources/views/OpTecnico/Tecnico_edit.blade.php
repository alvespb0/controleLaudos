@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none; background-color: var(--mid-color);">
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
            <form action="{{ route('update.tecnico', ['id' => $tecnico->id]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="usuario" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" value = "{{ $tecnico->usuario }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value = "{{ $tecnico->email }}" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
