<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Controle de Laudos')</title>
    
    {{-- Bootstrap 5.3 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Google Fonts - Fonte Moderna --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Toastr --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    
    {{-- Axios --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    @yield('head')

    {{-- CSS global do app (template + paleta + componentes) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    {{-- Estilos adicionais da página --}}
    @stack('styles')
    @yield('styles')
    
</head>
<body>
    <div class="app-wrapper">
        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="bi bi-file-earmark-medical"></i>
                </div>
                <div class="logo-text">Laudos System</div>
            </div>
            
            <nav class="sidebar-nav">
                {{-- Início --}}
                <div class="nav-section">
                    <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>
                        <span class="nav-text">Início</span>
                    </a>
                </div>
                
                {{-- Dashboard --}}
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <div class="nav-item">
                        <a href="#dashboardMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-speedometer2"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                        <div class="collapse nav-submenu" id="dashboardMenu">
                            @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca' || Auth::user()->tipo === 'comercial')
                            <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Controle de Laudos</span>
                            </a>
                            @endif
                            @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                            <a href="/documentos/controle" class="nav-link {{ request()->is('documentos/controle') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Documentos Técnicos</span>
                            </a>
                            @endif
                            @if(Auth::user()->tipo === 'admin')
                            <a href="/graphs" class="nav-link {{ request()->is('graphs') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Dashboard Gerencial</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'comercial')
                {{-- CRM --}}
                <div class="nav-section">
                    <div class="nav-section-title">CRM</div>
                    <div class="nav-item">
                        <a href="#crmMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-person-lines-fill"></i>
                            <span class="nav-text">CRM</span>
                        </a>
                        <div class="collapse nav-submenu" id="crmMenu">
                            <a href="/CRM" class="nav-link {{ request()->is('CRM') && !request()->is('CRM/*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Controle de Leads</span>
                            </a>
                            @if(Auth::user()->tipo === 'admin')
                            <a href="/CRM/comissoes" class="nav-link {{ request()->is('CRM/comissoes') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Comissões</span>
                            </a>
                            @endif
                            <a href="/Recomendadores/" class="nav-link {{ request()->is('Recomendadores') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Indicador Externo</span>
                            </a>
                            <a href="/Recomendadores/cadastro" class="nav-link {{ request()->is('Recomendadores/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Novo Indicador</span>
                            </a>
                            <a href="/calendar" class="nav-link {{ request()->is('calendar') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Calendário</span>
                            </a>
                        </div>
                    </div>
                    
                    {{-- Clientes --}}
                    <div class="nav-item">
                        <a href="#clientesMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-people"></i>
                            <span class="nav-text">Clientes</span>
                        </a>
                        <div class="collapse nav-submenu" id="clientesMenu">
                            <a href="/cliente/cadastro" class="nav-link {{ request()->is('cliente/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Novo Cliente</span>
                            </a>
                            <a href="/cliente" class="nav-link {{ request()->is('cliente') && !request()->is('cliente/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Clientes Cadastrados</span>
                            </a>
                            <a href="/orcamento" class="nav-link {{ request()->is('orcamento') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Gerar Orçamento</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(Auth::user()->tipo === 'admin')
                {{-- Operadores --}}
                <div class="nav-section">
                    <div class="nav-section-title">Administração</div>
                    <div class="nav-item">
                        <a href="#operadoresMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-person-badge"></i>
                            <span class="nav-text">Operadores</span>
                        </a>
                        <div class="collapse nav-submenu" id="operadoresMenu">
                            <a href="/user/register" class="nav-link {{ request()->is('user/register') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Novo Operador</span>
                            </a>
                            <a href="/user" class="nav-link {{ request()->is('user') && !request()->is('user/register') && !request()->is('user/alteracao/*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Operadores Cadastrados</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#variaveisMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-currency-dollar"></i>
                            <span class="nav-text">Variáveis de Preço</span>
                        </a>
                        <div class="collapse nav-submenu" id="variaveisMenu">
                            <a href="/variaveis-preco/cadastro" class="nav-link {{ request()->is('variaveis-preco/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Nova Variável</span>
                            </a>
                            <a href="/variaveis-preco" class="nav-link {{ request()->is('variaveis-preco') && !request()->is('variaveis-preco/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Variáveis Cadastradas</span>
                            </a>
                            <a href="/CRM/percentuais-comissao" class="nav-link {{ request()->is('CRM/percentuais-comissao') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Percentuais de Comissão</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                {{-- Documentos --}}
                <div class="nav-section">
                    <div class="nav-section-title">Documentos</div>
                    <div class="nav-item">
                        <a href="#documentosMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-file-earmark-text"></i>
                            <span class="nav-text">Documentos Técnicos</span>
                        </a>
                        <div class="collapse nav-submenu" id="documentosMenu">
                            <a href="/documentos/cadastro" class="nav-link {{ request()->is('documentos/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Novo Documento</span>
                            </a>
                            <a href="/documentos" class="nav-link {{ request()->is('documentos') && !request()->is('documentos/cadastro') && !request()->is('documentos/excluidos-anteriormente') && !request()->is('documentos/controle') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Documentos Cadastrados</span>
                            </a>
                            <a href="/documentos/excluidos-anteriormente" class="nav-link {{ request()->is('documentos/excluidos-anteriormente') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Documentos Excluídos</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'comercial')
                {{-- Laudos --}}
                <div class="nav-section">
                    <div class="nav-section-title">Laudos</div>
                    <div class="nav-item">
                        <a href="#laudosMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-file-earmark-medical"></i>
                            <span class="nav-text">Laudos</span>
                        </a>
                        <div class="collapse nav-submenu" id="laudosMenu">
                            <a href="/laudo/cadastro" class="nav-link {{ request()->is('laudo/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Novo Laudo</span>
                            </a>
                            <a href="/laudo" class="nav-link {{ request()->is('laudo') && !request()->is('laudo/cadastro') && !request()->is('laudo/excluidos-anteriormente') && !request()->is('laudo/alteracao/*') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Laudos Cadastrados</span>
                            </a>
                            <a href="/laudo/excluidos-anteriormente" class="nav-link {{ request()->is('laudo/excluidos-anteriormente') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Laudos Excluídos</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca')
                {{-- Status --}}
                <div class="nav-section">
                    <div class="nav-item">
                        <a href="#statusMenu" class="nav-link has-submenu" data-bs-toggle="collapse" aria-expanded="false">
                            <i class="bi bi-list-check"></i>
                            <span class="nav-text">Status</span>
                        </a>
                        <div class="collapse nav-submenu" id="statusMenu">
                            <a href="/status/cadastro" class="nav-link {{ request()->is('status/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Novo Status</span>
                            </a>
                            <a href="/status" class="nav-link {{ request()->is('status') && !request()->is('status/cadastro') ? 'active' : '' }}">
                                <i class="bi bi-circle"></i>
                                <span class="nav-text">Status Cadastrados</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca' || Auth::user()->tipo === 'comercial')
                {{-- Relatórios --}}
                <div class="nav-section">
                    <a href="/relatorios" class="nav-link {{ request()->is('relatorios') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span class="nav-text">Relatórios</span>
                    </a>
                </div>
                @endif
                
                {{-- Google Integration --}}
                <div class="nav-section" style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    @if(auth()->user()->google_access_token)
                        <div class="nav-link" style="cursor: default;">
                            <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
                            <span class="nav-text">Google Vinculado</span>
                        </div>
                    @else
                        <a href="{{ route('login.google') }}" class="nav-link">
                            <i class="bi bi-google" style="color: #4285f4;"></i>
                            <span class="nav-text">Vincular Google</span>
                        </a>
                    @endif
                </div>
                
                {{-- Logout --}}
                @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'seguranca' || Auth::user()->tipo === 'comercial')
                <div class="nav-section">
                    <a href="/logout" class="nav-link">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="nav-text">Sair</span>
                    </a>
                </div>
                @endif
            </nav>
        </aside>
        
        {{-- Main Content --}}
        <main class="main-content">
            <div class="topbar">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn-modern btn-modern-primary sidebar-toggle" id="sidebarToggle" type="button">
                        <i class="bi bi-chevron-double-left"></i>
                    </button>
                    <h1 class="topbar-title mb-0">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="topbar-actions">
                    @yield('topbar-actions')
                </div>
            </div>
            
            <div class="content-area">
                {{-- Flash Messages --}}
                @if(session('mensagem'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('mensagem') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                {{-- Content --}}
                @yield('content')
            </div>
            
            <footer class="app-footer">
                <p>&copy; {{ date('Y') }} Laudos System - Todos os direitos reservados</p>
            </footer>
        </main>
    </div>
    
    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarIcon = sidebarToggle.querySelector('i');
        let isCollapsed = false;
        
        function updateSidebarIcon() {
            // Quando recolhida, seta seta apontando para dentro (abrir)
            // Quando expandida, seta apontando para fora (recolher)
            sidebarIcon.className = isCollapsed
                ? 'bi bi-chevron-double-right'
                : 'bi bi-chevron-double-left';
        }
        
        sidebarToggle.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            sidebar.classList.toggle('collapsed', isCollapsed);
            
            // Salvar preferência no localStorage
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            
            updateSidebarIcon();
        });
        
        // Restaurar preferência
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            isCollapsed = true;
            sidebar.classList.add('collapsed');
        }
        updateSidebarIcon();
        
        // Auto-close submenus on mobile
        if (window.innerWidth < 992) {
            document.querySelectorAll('.nav-link.has-submenu').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!this.classList.contains('active')) {
                        document.querySelectorAll('.nav-submenu.show').forEach(menu => {
                            if (menu !== this.nextElementSibling) {
                                menu.classList.remove('show');
                            }
                        });
                    }
                });
            });
        }
        
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000",
            "extendedTimeOut": "1000"
        };
        
        @if(session('mensagem'))
        toastr.success("{{ session('mensagem') }}");
        @endif

        @if(session('error'))
        toastr.error("{{ session('error') }}");
        @endif
  
        window.addEventListener('toast-sucesso', event => {
            toastr.success(event.detail.message);
        });

    </script>
    
    @livewireScripts

    {{-- Scripts adicionais da página --}}
    @stack('scripts')
    @yield('scripts')
</body>
</html>

