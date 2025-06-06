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
            <h3 class="card-title mb-4 text-center text-dark">Edição de Status</h3>
            <form action="{{ route('update.status', $Status->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-patch-check"></i><label for="status" class="form-label">&nbspStatus</label>
                    <input type="text" class="form-control" id="status" name="nome" required placeholder="Nome do Status" value = "{{$Status->nome}}">
                </div>

                <div class="mb-3">
                    <i class="bi bi-brilliance"></i><label for="cor">&nbspCor do Status</label>
                    <input type="color" name="cor" class="form-control" id="cor" value="{{$Status->cor}}">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary col-sm-12">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
