<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Laudos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-color: #79c5b6;
            --secondary-color: #2c645c;
            --light-color: #dfeeec;
            --gray-color: #74948c;
            --accent-color: #5c9c90;
            --hover-color: #4a7a72;
        }

        body {
            background-color: var(--light-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--secondary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            color: var(--light-color) !important;
            font-weight: 600;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            color: var(--light-color) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }

        .dropdown:hover .dropdown-menu {
            display: block;
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
            pointer-events: auto;
            background-color: white;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .dropdown-menu {
            display: block;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            visibility: hidden;
            pointer-events: none;
            margin-top: 0;
            border-radius: 8px;
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            color: var(--secondary-color);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translatey(-2px);
        }

        .main-content {
            flex: 1;
            padding: 2.5rem 0;
        }

        .footer {
            background-color: var(--secondary-color);
            color: var(--light-color);
            padding: 1.5rem 0;
            margin-top: auto;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--hover-color);
            border-color: var(--hover-color);
            transform: translateY(-2px);
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: none;
            width: 1.5rem;
            height: 1.5rem;
            position: relative;
        }

        .navbar-toggler-icon::before,
        .navbar-toggler-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: var(--light-color);
            transition: all 0.3s ease;
        }

        .navbar-toggler-icon::before {
            top: 0;
        }

        .navbar-toggler-icon::after {
            bottom: 0;
        }
    </style>
</head>
<body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">
                <i class="bi bi-file-earmark-medical"></i>
                Controle de Laudos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca' || Auth::user()->tipo === 'comercial')
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->tipo === 'admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-shield-check"></i>
                            Segurança
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/tecnico/cadastro"><i class="bi bi-person-plus"></i> Novo Operador</a></li>
                            <li><a class="dropdown-item" href="/tecnico"><i class="bi bi-people"></i> Operadores Cadastrados</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-briefcase"></i>
                            Comercial
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/comercial/cadastro"><i class="bi bi-person-plus"></i> Novo Operador</a></li>
                            <li><a class="dropdown-item" href="/comercial"><i class="bi bi-people"></i> Operadores Cadastrados</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'comercial')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text"></i>
                            Laudos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/laudo/cadastro"><i class="bi bi-plus-circle"></i> Novo Laudo</a></li>
                            <li><a class="dropdown-item" href="/laudo"><i class="bi bi-files"></i> Laudos Cadastrados</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-badge"></i>
                            Clientes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/cliente/cadastro"><i class="bi bi-person-plus"></i> Novo Cliente</a></li>
                            <li><a class="dropdown-item" href="/cliente"><i class="bi bi-people"></i> Clientes Cadastrados</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-list-check"></i>
                            Status
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/status/cadastro"><i class="bi bi-plus-circle"></i> Novo Status</a></li>
                            <li><a class="dropdown-item" href="/status"><i class="bi bi-list-ul"></i> Status Cadastrados</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">
                            <i class="bi bi-box-arrow-right"></i>
                            Sair
                        </a>
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
                    <h5><i class="bi bi-file-earmark-medical"></i> Controle de Laudos</h5>
                    <p>Sistema de gerenciamento de laudos</p>
                </div>
                <div class="col-md-6 text-end">
                    <p><i class="bi bi-c-circle"></i> {{ date('Y') }} - Todos os direitos reservados</p>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    @if(session('mensagem'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };
        toastr.success("{{ session('mensagem') }}");
    @endif
</script>


</body>
</html> 