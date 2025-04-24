<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Laudos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-color: #79c5b6;
            --secondary-color: #2c645c;
            --light-color: #dfeeec;
            --gray-color: #74948c;
            --accent-color: #5c9c90;
        }

        body {
            background-color: var(--light-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: var(--secondary-color) !important;
        }

        .navbar-brand {
            color: var(--light-color) !important;
            font-weight: bold;
        }

        .nav-link {
            color: var(--light-color) !important;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
            pointer-events: auto;
            background-color: var(--light-color);
        }

        .dropdown-menu {
            display: block; /* importante para permitir transição */
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            visibility: hidden;
            pointer-events: none;
            margin-top: 0; /* garante que o dropdown não tenha atraso visual */
        }


        .dropdown-item:hover {
            background-color: var(--primary-color);
            color: var(--secondary-color);
        }

        .main-content {
            flex: 1;
            padding: 2rem 0;
        }

        .footer {
            background-color: var(--secondary-color);
            color: var(--light-color);
            padding: 1rem 0;
            margin-top: auto;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">Controle de Laudos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca' || Auth::user()->tipo === 'comercial')
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="/dashboard" role="button">Dashboard</a>

                    </li>
                    @endif
                    <li class="nav-item dropdown">
                        @if(Auth::user()->tipo === 'admin')
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Segurança
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/tecnico/cadastro">Novo Operador Segurança</a></li>
                            <li><a class="dropdown-item" href="/tecnico">Operadores Segurança Cadastrados</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Comercial
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/comercial/cadastro">Novo Operador Comercial</a></li>
                            <li><a class="dropdown-item" href="/comercial">Operadores Comercial Cadastrados</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'comercial')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Laudos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/laudo/cadastro">Novo Laudo</a></li>
                            <li><a class="dropdown-item" href="/laudo">Laudos Cadastrados</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Clientes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/cliente/cadastro">Novo Cliente</a></li>
                            <li><a class="dropdown-item" href="/cliente">Clientes Cadastrados</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Status
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/status/cadastro">Novo Status</a></li>
                            <li><a class="dropdown-item" href="/status">Status Cadastrados</a></li>
                        </ul>
                    </li>
                    @endif

                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Controle de Laudos</h5>
                    <p>Sistema de gerenciamento de laudos médicos</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>&copy; {{ date('Y') }} - Todos os direitos reservados</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 