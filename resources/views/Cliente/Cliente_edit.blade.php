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
            <h3 class="card-title mb-4 text-center text-dark">Edição de Cliente</h3>
            <form action="{{ route('update.cliente', $cliente->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="cliente" class="form-label">Cliente</label>
                    <input type="text" class="form-control" id="cliente" name="nome" value="{{$cliente->nome}}" placeholder="nome do cliente" required>
                </div>

                <div class="mb-3">
                    <label for="cnpj">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj" class="form-control" value = "{{$cliente->cnpj}}" placeholder="CNPJ do cliente" required>
                </div>
            
                <div class="mb-3">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email do Cliente" value ="{{$cliente->email}}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Telefone(s)</label>
                    <div id="telefones">
                        @foreach($cliente->telefone as $index => $telefone)
                            <div class="input-group mb-2">
                                <input type="text" name="telefone[]" class="form-control" placeholder="Telefone" required value="{{ $telefone->telefone }}">
                                @if($index > 0)
                                    <button type="button" class="btn btn-danger remove-phone" style="background-color: var(--accent-color); border: none;">×</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="addPhone" class="btn" style="background-color: var(--primary-color); color: white;">+ Adicionar Telefone</button>
                </div>


                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addPhoneBtn = document.getElementById('addPhone');
        const telefonesDiv = document.getElementById('telefones');

        addPhoneBtn.addEventListener('click', function() {
            const phoneGroup = document.createElement('div');
            phoneGroup.classList.add('input-group', 'mb-2');

            phoneGroup.innerHTML = `
                <input type="text" name="telefone[]" class="form-control" placeholder="Telefone" required>
                <button type="button" class="btn btn-danger remove-phone" style="background-color: var(--accent-color); border:none;">×</button>
            `;

            telefonesDiv.appendChild(phoneGroup);
        });

        telefonesDiv.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-phone')) {
                event.target.parentElement.remove();
            }
        });
    });
</script>

@endsection
