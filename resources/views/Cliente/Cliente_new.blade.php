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
            <h3 class="card-title mb-4 text-center text-dark">Cadastro de Cliente</h3>
            <form action="{{ route('create.cliente') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="cliente" class="form-label">Cliente</label>
                    <input type="text" class="form-control" id="cliente" name="nome" placeholder="nome do cliente" required>
                </div>

                <div class="mb-3">
                    <label for="cnpj">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj" class="form-control" placeholder="CNPJ do cliente" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
