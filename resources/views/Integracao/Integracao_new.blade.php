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
            <h3 class="card-title mb-4 text-center text-dark">Cadastro de Integração</h3>
            <form action="{{route('create.integracao')}}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-file-earmark-text"></i><label for="sistema" class="form-label">&nbspSistema</label>
                    <input type="text" class="form-control" id="sistema" name="sistema" placeholder="nome do sistema à ser integrado" required>
                </div>
                <div class="mb-3">
                    <i class="bi bi-file-earmark-text"></i><label for="descricao" class="form-label">&nbspDescrição</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" placeholder="descrição da integração" required>
                </div>
                <div class="mb-3">
                    <i class="bi bi-file-earmark-text"></i><label for="slug" class="form-label">&nbspSlug</label>
                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Slug (Único)" required>
                </div>
                <div class="mb-3">
                    <i class="bi bi-file-earmark-text"></i><label for="endpoint" class="form-label">&nbspEndpoint</label>
                    <input type="text" class="form-control" id="endpoint" name="endpoint" placeholder="endpoint da integração" required>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block"><i class="bi bi-journal-bookmark-fill"></i>&nbspTipo de Autenticação</label>
                    <select name="auth" id="" class="form-control">
                        <option value="basic">basic</option>
                        <option value="bearer">bearer</option>
                        <option value="wss">wss</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block"><i class="bi bi-journal-bookmark-fill"></i>&nbspModelo da API</label>
                    <select name="tipo" id="" class="form-control">
                        <option value="soap">Soap</option>
                        <option value="rest">Rest</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
