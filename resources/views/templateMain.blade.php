<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Laudos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-color: #437c90;
            --secondary-color: #255957;
            --light-color: #EEEBD3;
            --gray-color: #A98743;
            --accent-color: #F7C548;
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

        .pagination {
            display: flex;
            justify-content: center;
            padding-left: 0;
            list-style: none;
            margin-top: 20px;
            }

        .page-item {
            margin: 0 5px;
        }

        .page-link {
            color: var(--secondary-color);
            background-color: var(--light-color);
            border: 1px solid var(--accent-color);
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .page-link:hover {
            background-color: var(--hover-color);
            color: #ffffff;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .page-item.disabled .page-link {
            color: var(--gray-color);
            pointer-events: none;
            background-color: #f5f5f5;
            border-color: #ddd;
        }

        .sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: var(--secondary-color) !important;
            transition: all 0.3s ease;
            z-index: 1040;
        }
        .sidebar.collapsed {
            min-width: 70px;
            max-width: 70px;
        }
        .sidebar.collapsed:hover {
            min-width: 250px;
            max-width: 250px;
        }
        .sidebar.collapsed:hover .list-group-item span {
            opacity: 1 !important;
            margin-left: 0 !important;
            display: inline !important;
        }
        .sidebar.collapsed:hover .list-group-item {
            text-align: left !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            border-radius: 0.375rem !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
        }
        .sidebar.collapsed:hover .collapse .list-group-item {
            padding-left: 1.5rem !important;
            padding-right: 1rem !important;
        }
        .sidebar.collapsed:hover .list-group-item i {
            margin-right: 0.5rem !important;
        }
        .sidebar.collapsed:hover .collapse,
        .sidebar.collapsed:hover .collapsing {
            display: none !important;
        }
        .sidebar.collapsed:hover .list-group-item[data-bs-toggle="collapse"]:after {
            display: block !important;
        }
        .sidebar.collapsed:hover ~ .flex-grow-1 .main-content {
            margin-left: 200px !important;
        }
        .sidebar.collapsed:hover + .sidebar-toggle-btn {
            left: 260px !important;
        }
        .sidebar.expanded {
            min-width: 250px;
            max-width: 250px;
        }
        .sidebar.expanded .list-group-item span {
            opacity: 1 !important;
            margin-left: 0 !important;
            display: inline !important;
        }
        .sidebar.expanded .list-group-item {
            text-align: left !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            border-radius: 0.375rem !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
        }
        .sidebar.expanded .collapse .list-group-item {
            padding-left: 1.5rem !important;
            padding-right: 1rem !important;
        }
        .sidebar.expanded .list-group-item i {
            margin-right: 0.5rem !important;
        }
        .sidebar.expanded .collapse,
        .sidebar.expanded .collapsing {
            display: none !important;
        }
        .sidebar.expanded .collapse.show,
        .sidebar.expanded .collapsing.show {
            display: block !important;
        }
        .sidebar.expanded .list-group-item[data-bs-toggle="collapse"]:after {
            display: block !important;
        }
        .sidebar.expanded ~ .flex-grow-1 .main-content {
            margin-left: 100px !important;
        }
        .sidebar.expanded + .sidebar-toggle-btn {
            left: 260px !important;
        }
        .sidebar.fixed {
            min-width: 250px !important;
            max-width: 250px !important;
        }
        .sidebar.fixed .list-group-item span {
            opacity: 1 !important;
            margin-left: 0 !important;
            display: inline !important;
        }
        .sidebar.fixed .list-group-item {
            text-align: left !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            border-radius: 0.375rem !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
        }
        .sidebar.fixed .collapse .list-group-item {
            padding-left: 1.5rem !important;
            padding-right: 1rem !important;
        }
        .sidebar.fixed .list-group-item i {
            margin-right: 0.5rem !important;
        }
        .sidebar.fixed .collapse,
        .sidebar.fixed .collapsing {
            display: none !important;
        }
        .sidebar.fixed .collapse.show,
        .sidebar.fixed .collapsing.show {
            display: block !important;
        }
        .sidebar.fixed .list-group-item[data-bs-toggle="collapse"]:after {
            display: block !important;
        }
        .sidebar.fixed ~ .flex-grow-1 .main-content {
            margin-left: 100px !important;
        }
        .sidebar.fixed + .sidebar-toggle-btn {
            left: 260px !important;
        }
        .sidebar .list-group-item {
            background: var(--secondary-color) !important;
            color: var(--light-color) !important;
            border: none;
            transition: background 0.2s, color 0.2s, padding 0.3s;
        }
        .sidebar .list-group-item:hover, .sidebar .list-group-item.active {
            background: var(--primary-color) !important;
            color: #fff !important;
        }
        .sidebar .list-group-item i {
            font-size: 1.2rem;
        }
        .sidebar .list-group-item span {
            transition: opacity 0.3s, margin 0.3s;
        }
        /* Ajuste do espaçamento dos submenus */
        .sidebar .collapse .list-group-item {
            padding-left: 1.5rem !important;
            padding-right: 1rem !important;
        }
        .sidebar.collapsed .list-group-item span {
            opacity: 0;
            margin-left: -999px;
        }
        .sidebar.collapsed .collapse,
        .sidebar.collapsed .collapsing {
            display: none !important;
        }
        .sidebar .collapse {
            transition: height 0.35s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
        }
        .sidebar .collapse:not(.show) {
            display: none !important;
        }
        .sidebar .collapsing {
            overflow: hidden;
        }
        .sidebar .list-group-item[data-bs-toggle="collapse"]:after {
            content: '\f282';
            font-family: 'bootstrap-icons';
            float: right;
            transition: transform 0.3s;
        }
        .sidebar .list-group-item[aria-expanded="true"]:after {
            transform: rotate(90deg);
        }
        .sidebar.collapsed .list-group-item[data-bs-toggle="collapse"]:after {
            display: none;
        }
        .sidebar-toggle-btn {
            position: absolute;
            top: 10px;
            left: 80px;
            z-index: 1100;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .sidebar.expanded + .sidebar-toggle-btn,
        .sidebar.fixed + .sidebar-toggle-btn {
            left: 260px;
        }
        .sidebar-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                left: -250px;
                top: 0;
                height: 100vh;
                transition: left 0.3s;
            }
            .sidebar.show {
                left: 0;
            }
            .sidebar-toggle-btn {
                left: 10px;
            }
        }
        .main-content {
            transition: margin-left 0.3s;
        }
        @media (min-width: 992px) {
            .main-content {
                margin-left: 50px;
            }
            .sidebar.collapsed ~ .flex-grow-1 .main-content {
                margin-left: 70px;
            }
        }
        .sidebar.collapsed .list-group-item {
            text-align: right;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .sidebar.collapsed .list-group-item:hover, .sidebar.collapsed .list-group-item.active {
            background: var(--primary-color) !important;
            color: #fff !important;
        }
        .sidebar.collapsed .list-group-item i {
            margin-right: 0 !important;
        }
        .sidebar.collapsed .list-group-item span {
            display: none !important;
        }
        .sidebar.collapsed .list-group-item {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin: 8px auto;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Sidebar -->
    <div class="d-flex position-relative">
        <nav id="sidebarMenu" class="sidebar d-lg-block text-white">
            <div class="position-sticky">
                <div class="list-group list-group-flush mx-3 mt-4">
                    <a href="/" class="list-group-item list-group-item-action py-2 ripple">
                        <i class="bi bi-house me-2"></i><span>Início</span>
                    </a>
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#dashboardMenu" role="button" aria-expanded="false" aria-controls="dashboardMenu">
                        <i class="bi bi-speedometer2 me-2"></i><span>Dashboard</span>
                    </a>
                    <div class="collapse" id="dashboardMenu" data-bs-parent="#sidebarMenu">
                        @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca' || Auth::user()->tipo === 'comercial')
                        <a href="/dashboard" class="list-group-item list-group-item-action ps-5">Controle de Laudos</a>
                        @endif
                        @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                        <a href="/documentos/controle" class="list-group-item list-group-item-action ps-5">Controle de Documentos técnicos</a>
                        @endif
                        @if(Auth::user()->tipo === 'admin')
                        <a href="/graphs" class="list-group-item list-group-item-action ps-5">Dashboard Gerencial</a>
                        @endif
                    </div>
                     @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'comercial')
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#crmMenu" role="button" aria-expanded="false" aria-controls="crmMenu">
                        <i class="bi-person-lines-fill me-2"></i><span>CRM</span>
                    </a>
                    <div class="collapse" id="crmMenu" data-bs-parent="#sidebarMenu">
                        <a href="/CRM" class="list-group-item list-group-item-action ps-5">Controle de Leads</a>
                        @if(Auth::user()->tipo === 'admin')
                        <a href="/CRM/comissoes" class="list-group-item list-group-item-action ps-5">Comissões</a>
                        @endif
                        <a href="/Recomendadores/" class="list-group-item list-group-item-action ps-5">Indicador Externo</a>
                        <a href="/Recomendadores/cadastro" class="list-group-item list-group-item-action ps-5">Novo Indicador Externo</a>
                    </div>
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#clientesMenu" role="button" aria-expanded="false" aria-controls="clientesMenu">
                        <i class="bi bi-person-badge me-2"></i><span>Clientes</span>
                    </a>
                    <div class="collapse" id="clientesMenu" data-bs-parent="#sidebarMenu">
                        <a href="/cliente/cadastro" class="list-group-item list-group-item-action ps-5">Novo Cliente</a>
                        <a href="/cliente" class="list-group-item list-group-item-action ps-5">Clientes Cadastrados</a>
                        <a href="/orcamento" class="list-group-item list-group-item-action ps-5">Gerar Orçamento</a>
                    </div>

                    @endif
                    @if(Auth::user()->tipo === 'admin')
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#operadoresMenu" role="button" aria-expanded="false" aria-controls="operadoresMenu">
                        <i class="bi bi-person-circle me-2"></i><span>Operadores</span>
                    </a>
                    <div class="collapse" id="operadoresMenu" data-bs-parent="#sidebarMenu">
                        <a href="/user/register" class="list-group-item list-group-item-action ps-5">Novo Operador</a>
                        <a href="/user" class="list-group-item list-group-item-action ps-5">Operadores Cadastrados</a>
                    </div>
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#variaveisMenu" role="button" aria-expanded="false" aria-controls="variaveisMenu">
                        <i class="bi bi-currency-dollar"></i><span>Variáveis de Preço</span>
                    </a>
                    <div class="collapse" id="variaveisMenu" data-bs-parent="#sidebarMenu">
                        <a href="/variaveis-preco/cadastro" class="list-group-item list-group-item-action ps-5">Nova Variável</a>
                        <a href="/variaveis-preco" class="list-group-item list-group-item-action ps-5">Variáveis Cadastradas</a>
                        <a href="/CRM/percentuais-comissao" class="list-group-item list-group-item-action ps-5">Percentuais de comissão</a>
                    </div>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#documentosMenu" role="button" aria-expanded="false" aria-controls="documentosMenu">
                        <i class="bi bi-file-earmark-text me-2"></i><span>Documentos técnicos</span>
                    </a>
                    <div class="collapse" id="documentosMenu" data-bs-parent="#sidebarMenu">
                        <a href="/documentos/cadastro" class="list-group-item list-group-item-action ps-5">Novo Documento</a>
                        <a href="/documentos" class="list-group-item list-group-item-action ps-5">Documentos cadastrados</a>
                        <a href="/documentos/excluidos-anteriormente" class="list-group-item list-group-item-action ps-5">Documentos Excluídos</a>
                    </div>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'comercial')
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#laudosMenu" role="button" aria-expanded="false" aria-controls="laudosMenu">
                        <i class="bi bi-file-earmark-text me-2"></i><span>Laudos</span>
                    </a>
                    <div class="collapse" id="laudosMenu" data-bs-parent="#sidebarMenu">
                        <a href="/laudo/cadastro" class="list-group-item list-group-item-action ps-5">Novo Laudo</a>
                        <a href="/laudo" class="list-group-item list-group-item-action ps-5">Laudos Cadastrados</a>
                        <a href="/laudo/excluidos-anteriormente" class="list-group-item list-group-item-action ps-5">Laudos Excluídos</a>
                    </div>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                    <a class="list-group-item list-group-item-action py-2 ripple" data-bs-toggle="collapse" href="#statusMenu" role="button" aria-expanded="false" aria-controls="statusMenu">
                        <i class="bi bi-list-check me-2"></i><span>Status</span>
                    </a>
                    <div class="collapse" id="statusMenu" data-bs-parent="#sidebarMenu">
                        <a href="/status/cadastro" class="list-group-item list-group-item-action ps-5">Novo Status</a>
                        <a href="/status" class="list-group-item list-group-item-action ps-5">Status Cadastrados</a>
                    </div>
                    @endif
                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca' || Auth::user()->tipo === 'comercial')
                    <a href="/relatorios" class="list-group-item list-group-item-action py-2 ripple">
                        <i class="bi bi-file-earmark me-2"></i><span>Relatórios</span>
                    </a>
                    <a href="/logout" class="list-group-item list-group-item-action py-2 ripple">
                        <i class="bi bi-box-arrow-right me-2"></i><span>Sair</span>
                    </a>
                    @endif
                </div>
        </div>
    </nav>
        <button class="sidebar-toggle-btn" id="sidebarToggle" type="button" title="Expandir/Colapsar Sidebar">
            <i class="bi bi-chevron-double-left"></i>
        </button>
        <div class="flex-grow-1">
            <!-- Conteúdo principal -->
    <div class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </div>
        </div>
    </div>
    <script>
        // Sidebar collapse/expand com hover e fixação
        const sidebar = document.getElementById('sidebarMenu');
        const toggleBtn = document.getElementById('sidebarToggle');
        let collapsed = true; // Começa recolhida por padrão
        let isFixed = false; // Começa não fixada
        
        // Aplicar estado inicial recolhido
        sidebar.classList.add('collapsed');
        toggleBtn.querySelector('i').classList.remove('bi-chevron-double-left');
        toggleBtn.querySelector('i').classList.add('bi-chevron-double-right');
        toggleBtn.title = 'Fixar Sidebar Expandida';
        
        // Função para expandir sidebar
        function expandSidebar() {
            if (!isFixed) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('expanded');
            }
        }
        
        // Função para recolher sidebar
        function collapseSidebar() {
            if (!isFixed) {
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('expanded');
            }
        }
        
        // Eventos de hover
        sidebar.addEventListener('mouseenter', expandSidebar);
        sidebar.addEventListener('mouseleave', collapseSidebar);
        
        // Botão de fixar/desfixar
        toggleBtn.addEventListener('click', function() {
            isFixed = !isFixed;
            
            if (isFixed) {
                // Fixar expandida
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('fixed');
                toggleBtn.querySelector('i').classList.remove('bi-chevron-double-right');
                toggleBtn.querySelector('i').classList.add('bi-chevron-double-left');
                toggleBtn.title = 'Desfixar Sidebar';
                toggleBtn.style.backgroundColor = 'var(--accent-color)';
            } else {
                // Desfixar (volta ao comportamento de hover)
                sidebar.classList.remove('fixed');
                sidebar.classList.add('collapsed');
                toggleBtn.querySelector('i').classList.remove('bi-chevron-double-left');
                toggleBtn.querySelector('i').classList.add('bi-chevron-double-right');
                toggleBtn.title = 'Fixar Sidebar Expandida';
                toggleBtn.style.backgroundColor = 'var(--primary-color)';
            }
        });
        
        // Dropdown fluido (abre/fecha suavemente)
        const collapseEls = document.querySelectorAll('#sidebarMenu .collapse');
        collapseEls.forEach(function(el) {
            el.addEventListener('show.bs.collapse', function (e) {
                el.style.transition = 'height 0.35s cubic-bezier(0.4,0,0.2,1)';
                // Adicionar classe show para controle CSS
                el.classList.add('show');
            });
            el.addEventListener('hide.bs.collapse', function (e) {
                el.style.transition = 'height 0.35s cubic-bezier(0.4,0,0.2,1)';
                // Remover classe show para controle CSS
                el.classList.remove('show');
            });
        });
        
        // Garantir que dropdowns fechados não apareçam ao expandir sidebar
        function resetDropdowns() {
            collapseEls.forEach(function(el) {
                if (!el.classList.contains('show')) {
                    el.style.display = 'none';
                }
            });
        }
        
        // Aplicar reset nos dropdowns quando necessário
        sidebar.addEventListener('mouseenter', function() {
            setTimeout(resetDropdowns, 50);
        });
        
        sidebar.addEventListener('mouseleave', function() {
            setTimeout(resetDropdowns, 50);
        });
    </script>
    <!-- Sidebar -->

    <!-- Footer permanece igual -->

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
    @if(session('error'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };
        toastr.error("{{ session('error') }}");
    @endif
</script>


</body>
</html> 