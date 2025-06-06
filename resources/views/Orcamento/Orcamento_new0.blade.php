@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Escolha o Tipo de Orçamento</h3>

            <form action="{{route('gerar.orcamento')}}" method="POST">
                @csrf

                <!-- Tipo de orçamento -->
                <div class="mb-3">
                    <label class="form-label d-block">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        &nbsp Como deseja gerar o orçamento?
                    </label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_orcamento" id="tipoAvulso" value="1" required>
                        <label class="form-check-label" for="tipoAvulso">Para um novo cliente</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_orcamento" id="tipoCliente" value="2" required>
                        <label class="form-check-label" for="tipoCliente">Para um cliente já cadastrado</label>
                    </div>
                </div>

                <!-- Select de clientes -->
                <div class="mb-3" id="selectClienteContainer" style="display: none;">
                    <label for="cliente" class="form-label">Selecione o cliente:</label>
                    <select name="cliente" id="cliente" class="form-select">
                        <option value="" selected disabled>-- Escolha um cliente --</option>
                        @foreach($clientes as $cliente)
                        <option value="{{$cliente->id}}">{{$cliente->nome}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Botão -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary px-4">Continuar</button>
                    <button type="reset" class="btn btn-secondary px-4">Limpar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para mostrar/esconder o select -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoCliente = document.getElementById('tipoCliente');
        const tipoAvulso = document.getElementById('tipoAvulso');
        const selectClienteContainer = document.getElementById('selectClienteContainer');

        tipoCliente.addEventListener('change', function () {
            if (this.checked) {
                selectClienteContainer.style.display = 'block';
            }
        });

        tipoAvulso.addEventListener('change', function () {
            if (this.checked) {
                selectClienteContainer.style.display = 'none';
                document.getElementById('cliente').selectedIndex = 0;
            }
        });
    });
</script>
@endsection
