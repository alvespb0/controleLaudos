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
            <h3 class="card-title mb-4 text-center text-dark">Cadastro de Variáveis Para Precificação</h3>
            <form action="{{route('create.variavel')}}" method="POST">
                @csrf
                <div class="mb-3">
                    <i class="bi bi-text-indent-left"></i><label for="tipo" class="form-label">&nbspTipo</label>
                    <select name="tipo" id="tipo" class="form-control" required onchange="toggleFields()">
                        <option value="">Selecione o tipo</option>
                        <option value="bool">Bool</option>
                        <option value="valor">Valor</option>
                        <option value="faixa">Faixa</option>
                    </select>
                </div>
                <div class="mb-3" id="div_nome_variavel">
                    <i class="bi bi-type"></i><label for="nome_variavel" class="form-label">&nbspNome</label>
                    <input type="text" class="form-control" id="nome_variavel" name="nome_variavel" placeholder="Nome da variável">
                </div>
                <div class="mb-3" id="div_campo_alvo">
                    <i class="bi bi-ticket-detailed"></i><label for="campo_alvo">&nbspCampo Alvo</label>
                    <input type="text" name="campo_alvo" class="form-control" id="campo_alvo" placeholder="Descrição do campo">
                </div>
                <div class="mb-3" id="div_valor">
                    <i class="bi bi-currency-dollar"></i><label for="valor">&nbspValor</label>
                    <input type="number" step="0.01" name="valor" class="form-control" id="valor" placeholder="0.00">
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
function toggleFields() {
    const tipo = document.getElementById('tipo').value;
    document.getElementById('div_nome_variavel').style.display = 'block';
    document.getElementById('div_campo_alvo').style.display = 'block';
    document.getElementById('div_valor').style.display = 'block';
    if (tipo === 'faixa') {
        document.getElementById('div_valor').style.display = 'none';
    }
}
</script>
@endsection
