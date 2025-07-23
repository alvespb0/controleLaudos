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
            <h3 class="card-title mb-4 text-center text-dark">Edição de Cliente</h3>
            <form action="{{ route('update.cliente', $cliente->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-person"></i><label for="cliente" class="form-label">&nbspCliente</label>
                    <input type="text" class="form-control" id="cliente" name="nome" value="{{$cliente->nome}}" placeholder="nome do cliente" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-card-text"></i><label for="cnpj">&nbspCNPJ/CPF</label>
                    <input type="text" name="cnpj" id="cnpj" class="form-control" value = "{{$cliente->cnpj}}" placeholder="CNPJ do cliente" required>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block"><i class="bi bi-arrow-repeat"></i>&nbspCliente novo ou Renovação?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="cliente_novo" id="cliente_novo" value="1" {{$cliente->cliente_novo ? 'checked' : ''}} required>
                        <label class="form-check-label" for="cliente_novo">Cliente Novo</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="cliente_novo" id="cliente_renovacao" value="0" {{!$cliente->cliente_novo ? 'checked' : ''}} required>
                        <label class="form-check-label" for="cliente_renovacao">Cliente Renovado</label>
                    </div>
                </div>

                <div class="mb-3">
                    <i class="bi bi-envelope"></i><label for="email">&nbspE-mail</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email do Cliente" value ="{{$cliente->email}}">
                </div>

                <div class="mb-3">
                    <i class="bi bi-telephone"></i><label class="form-label">&nbspTelefone(s)</label>
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

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt"></i>&nbsp;Endereço</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="cep" name="cep" placeholder="CEP" maxlength="9" value="{{ $cliente->endereco ? $cliente->endereco->cep : '' }}" required>
                        <button type="button" class="btn btn-outline-secondary" id="buscar-cep" title="Buscar endereço pelo CEP">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" id="rua" name="rua" placeholder="Rua" value="{{ $cliente->endereco ? $cliente->endereco->rua : '' }}" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" id="numero" name="numero" placeholder="Número" value="{{ $cliente->endereco ? $cliente->endereco->numero : ''}}"required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro" value="{{ $cliente->endereco ? $cliente->endereco->bairro : '' }}" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" id="complemento" name="complemento" placeholder="Complemento" value="{{ $cliente->endereco && $cliente->endereco->complemento ? $cliente->endereco->complemento : ''}}">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade" value="{{ $cliente->endereco ? $cliente->endereco->cidade : ''}}" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" id="uf" name="uf" placeholder="UF" maxlength="2" value="{{ $cliente->endereco ? $cliente->endereco->uf : ''}}" required>
                        </div>
                    </div>
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

    // Busca CEP via ViaCEP
    document.getElementById('buscar-cep').addEventListener('click', function() {
        const cep = document.getElementById('cep').value.replace(/\D/g, '');
        if (cep.length !== 8) {
            alert('Digite um CEP válido com 8 dígitos.');
            return;
        }
        axios.get(`https://viacep.com.br/ws/${cep}/json/`)
            .then(function(response) {
                if (response.data.erro) {
                    alert('CEP não encontrado.');
                    return;
                }
                document.getElementById('rua').value = response.data.logradouro || '';
                document.getElementById('bairro').value = response.data.bairro || '';
                document.getElementById('cidade').value = response.data.localidade || '';
                document.getElementById('uf').value = response.data.uf || '';
            })
            .catch(function() {
                alert('Erro ao buscar o CEP.');
            });
    });

</script>

@endsection
