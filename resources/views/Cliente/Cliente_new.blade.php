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
            <h3 class="card-title mb-4 text-center text-dark">Cadastro de Cliente</h3>
            <form action="{{ route('create.cliente') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-person"></i><label for="cliente" class="form-label">&nbspCliente</label>
                    <input type="text" class="form-control" id="cliente" name="nome" placeholder="nome do cliente" required>
                </div>

                <div class="mb-3">
                    <i class="bi bi-card-text"></i><label for="cnpj">&nbspCNPJ/CPF</label>
                    <input type="text" name="cnpj" id="cnpj" class="form-control" placeholder="CNPJ do cliente" required>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block"><i class="bi bi-arrow-repeat"></i>&nbspCliente novo ou Renovação?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="novo" value="novo" required>
                        <label class="form-check-label" for="novo">Novo</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="renovacao" value="renovacao" required>
                        <label class="form-check-label" for="renovacao">Renovação</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_cliente" id="cliente_renovacao" value="resgatado" required>
                        <label class="form-check-label" for="resgatado">Resgatado</label>
                    </div>
                </div>

                <div class="mb-3">
                    <i class="bi bi-envelope"></i><label for="email">&nbspE-mail</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email do Cliente">
                </div>

                <div class="mb-3">
                    <i class="bi bi-telephone"></i><label class="form-label">&nbspTelefone(s)</label>
                    <div id="telefones">
                        <div class="input-group mb-2">
                            <input type="text" name="telefone[]" class="form-control" placeholder="Telefone" required>
                        </div>
                    </div>
                    <button type="button" id="addPhone" class="btn" style="background-color: var(--primary-color); color: white;">+ Adicionar Telefone</button>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt"></i>&nbsp;Endereço</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="cep" name="cep" placeholder="CEP" required maxlength="9">
                        <button type="button" class="btn btn-outline-secondary" id="buscar-cep" title="Buscar endereço pelo CEP">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" id="rua" name="rua" placeholder="Rua" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" id="numero" name="numero" placeholder="Número" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Bairro" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" id="complemento" name="complemento" placeholder="Complemento">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" id="uf" name="uf" placeholder="UF" maxlength="2" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <button type="button" class="btn w-100" id="toggle-cobranca" style="background-color: var(--primary-color); color: white;">
                        <i class="bi bi-credit-card"></i> Acrescentar dados de cobrança?
                    </button>
                </div>
                <div id="cobranca-section" style="display:none; border:1px solid var(--primary-color); border-radius:8px; padding:16px; margin-bottom:16px; background:#f8f9fa;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold"><i class="bi bi-credit-card"></i> Dados de Cobrança</span>
                        <button type="button" class="btn btn-sm" id="copiar-endereco-cobranca" style="background-color: var(--primary-color); color: white;">
                            <i class="bi bi-clipboard"></i> Copiar endereço para cobrança
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="cep_cobranca" name="cep_cobranca" placeholder="CEP">
                        <button type="button" class="btn btn-outline-secondary" id="buscar-cep-cobranca" title="Buscar endereço de cobrança pelo CEP">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" id="rua_cobranca" name="rua_cobranca" placeholder="Rua">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" id="numero_cobranca" name="numero_cobranca" placeholder="Número">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" id="bairro_cobranca" name="bairro_cobranca" placeholder="Bairro">
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control" id="complemento_cobranca" name="complemento_cobranca" placeholder="Complemento">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" id="cidade_cobranca" name="cidade_cobranca" placeholder="Cidade">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" id="uf_cobranca" name="uf_cobranca" placeholder="UF" maxlength="2">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <input type="email" class="form-control" id="email_cobranca" name="email_cobranca" placeholder="E-mail de cobrança">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control" id="telefone_cobranca" name="telefone_cobranca" placeholder="Telefone de cobrança">
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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

    // Toggle dados de cobrança
    document.getElementById('toggle-cobranca').addEventListener('click', function() {
        const section = document.getElementById('cobranca-section');
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    });

    // Copiar endereço para cobrança
    document.getElementById('copiar-endereco-cobranca').addEventListener('click', function() {
        document.getElementById('cep_cobranca').value = document.getElementById('cep').value;
        document.getElementById('rua_cobranca').value = document.getElementById('rua').value;
        document.getElementById('numero_cobranca').value = document.getElementById('numero').value;
        document.getElementById('bairro_cobranca').value = document.getElementById('bairro').value;
        document.getElementById('complemento_cobranca').value = document.getElementById('complemento').value;
        document.getElementById('cidade_cobranca').value = document.getElementById('cidade').value;
        document.getElementById('uf_cobranca').value = document.getElementById('uf').value;
    });

    // Busca CEP via ViaCEP para endereço de cobrança
    document.getElementById('buscar-cep-cobranca').addEventListener('click', function() {
        const cep = document.getElementById('cep_cobranca').value.replace(/\D/g, '');
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
                document.getElementById('rua_cobranca').value = response.data.logradouro || '';
                document.getElementById('bairro_cobranca').value = response.data.bairro || '';
                document.getElementById('cidade_cobranca').value = response.data.localidade || '';
                document.getElementById('uf_cobranca').value = response.data.uf || '';
            })
            .catch(function() {
                alert('Erro ao buscar o CEP.');
            });
    });
</script>
@endsection
