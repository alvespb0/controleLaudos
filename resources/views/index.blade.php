@extends('templateMain')

@section('content')
@php
var_dump($status);
@endphp
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Laudos Cadastrados</h2>
        <form action="" method="GET">
            <div class="d-flex gap-3">
                <div class="input-group" style="width: 200px;">
                    <input type="text" class="form-control" name="search" id="clienteFilter" placeholder="Buscar cliente...">
                </div>
                <select name="status" class="form-select" id="statusFilter" style="width: 180px;">
                    <option value="" selected>Todos os status</option>

                    @foreach($status as $s)
                        <option value="{{$s->id}}">{{$s->nome}}<div class="status-indicator" style="background-color: {{$s->cor}}"></div></option>
                    @endforeach
                </select>
                <input type="date" class="form-control" id="dataFilter" style="width: 180px;">
                <button type="submit" class="btn btn-primary px-3 py-2 rounded-circle shadow-sm" style="background-color: var(--primary-color); border: none;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    
    <div class="row">
        <!-- Card 1 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">Laudo #001</h5>
                        <div class="status-dropdown">
                            <div class="status-indicator" style="background-color: #FFA500"></div>
                            <span class="status-text">Pendente</span>
                            <select class="form-select status-select">
                                <option value="pendente" data-color="#FFA500">Pendente</option>
                                <option value="em_andamento" data-color="#4169E1">Em Andamento</option>
                                <option value="concluido" data-color="#32CD32">Concluído</option>
                            </select>
                        </div>
                    </div>
                    <p class="card-text">
                        <strong>Nome:</strong> João Silva<br>
                        <strong>Cliente: </strong> Adami <br>
                        <strong>Data Previsão:</strong> 15/03/2024<br>
                        <strong>Vendedor:</strong> Maria Oliveira<br>
                        <strong>Técnico Responsável:</strong>
                        <select class="form-select mt-2">
                            <option value="1">Carlos Santos</option>
                            <option value="2">Ana Pereira</option>
                            <option value="3">Pedro Costa</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">Laudo #002</h5>
                        <div class="status-dropdown">
                            <div class="status-indicator" style="background-color: #4169E1"></div>
                            <span class="status-text">Em Andamento</span>
                            <select class="form-select status-select">
                                <option value="pendente" data-color="#FFA500">Pendente</option>
                                <option value="em_andamento" data-color="#4169E1">Em Andamento</option>
                                <option value="concluido" data-color="#32CD32">Concluído</option>
                            </select>
                        </div>
                    </div>
                    <p class="card-text">
                        <strong>Nome:</strong> Laudo Técnico <br>
                        <strong>Cliente: </strong> Rumobrás <br>
                        <strong>Data Previsão:</strong> 20/03/2024<br>
                        <strong>Vendedor:</strong> José Almeida<br>
                        <strong>Técnico Responsável:</strong>
                        <select class="form-select mt-2">
                            <option value="1">Carlos Santos</option>
                            <option value="2">Ana Pereira</option>
                            <option value="3">Pedro Costa</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">Laudo #003</h5>
                        <div class="status-dropdown">
                            <div class="status-indicator" style="background-color: #32CD32"></div>
                            <span class="status-text">Concluído</span>
                            <select class="form-select status-select">
                                <option value="pendente" data-color="#FFA500">Pendente</option>
                                <option value="em_andamento" data-color="#4169E1">Em Andamento</option>
                                <option value="concluido" data-color="#32CD32">Concluído</option>
                            </select>
                        </div>
                    </div>
                    <p class="card-text">
                        <strong>Nome:</strong> Laudo Técnico<br>
                        <strong>Cliente:</strong> Kewer <br>
                        <strong>Data Previsão:</strong> 25/03/2024<br>
                        <strong>Vendedor:</strong> Luiza Mendes<br>
                        <strong>Técnico Responsável:</strong>
                        <select class="form-select mt-2">
                            <option value="1">Carlos Santos</option>
                            <option value="2">Ana Pereira</option>
                            <option value="3">Pedro Costa</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        color: var(--secondary-color);
        font-weight: bold;
    }

    .form-select {
        border-color: var(--primary-color);
    }

    .form-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.25rem rgba(121, 197, 182, 0.25);
    }

    .status-dropdown {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-text {
        font-size: 0.9rem;
        color: var(--gray-color);
    }

    .status-select {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .status-dropdown::after {
        content: "▼";
        font-size: 0.7rem;
        color: var(--gray-color);
        margin-left: 4px;
    }

    /* Estilos para os filtros */
    .form-control, .form-select {
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(121, 197, 182, 0.25);
    }
</style>
</style>

<script>

</script>
@endsection 