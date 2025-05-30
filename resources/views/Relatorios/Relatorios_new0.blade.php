@extends('templateMain')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 600px; border: none; background-color: var(--mid-color);">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 text-center text-dark">Tipo de Relatório Desejado</h3>
            <form action="{{ route('request.tipoRelatorio') }}" method="POST">
                @csrf
                <div class="mb-3">
                <label for="tipoRelatorio" class="form-label">Selecione o tipo de relatório desejado</label>
                    <select name="tipoRelatorio" class="form-select" id="" required>
                        <option value="" selected disabled>Tipo de Relatório</option>
                        <option value="laudos">Laudos</option>
                        <option value="clientes">Clientes</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Enviar</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
