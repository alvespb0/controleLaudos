<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Gerado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #dfeeec 0%, #79c5b6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-custom {
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            max-width: 420px;
            width: 100%;
            padding: 2.5rem 2rem 2rem 2rem;
            background: #fff;
        }
        .btn-custom {
            min-width: 120px;
            font-weight: 500;
            border-radius: 8px;
        }
        .btn-success {
            background-color: #79c5b6;
            border: none;
            color: #fff;
        }
        .btn-success:hover, .btn-success:focus {
            background-color: #5c9c90;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-warning {
            background-color: #f0ad4e;
            color: #fff;
            border: none;
        }
        .btn-warning:hover, .btn-warning:focus {
            background-color: #d48806;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-primary {
            background-color: #2c645c;
            border: none;
            color: #fff;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #4a7a72;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-info {
            background-color: #17a2b8;
            color: #fff;
            border: none;
        }
        .btn-info:hover, .btn-info:focus {
            background-color: #138496;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-primary:disabled, .btn-primary.disabled {
            background-color: #bdbdbd !important;
            color: #fff !important;
            border: none !important;
            opacity: 1;
            cursor: not-allowed;
        }
        .btn-success:disabled, .btn-success.disabled {
            background-color: #bdbdbd !important;
            color: #fff !important;
            border: none !important;
            opacity: 1;
            cursor: not-allowed;
        }
        .btn-warning:disabled, .btn-warning.disabled {
            background-color: #bdbdbd !important;
            color: #fff !important;
            border: none !important;
            opacity: 1;
            cursor: not-allowed;
        }
        .btn-info:disabled, .btn-info.disabled {
            background-color: #bdbdbd !important;
            color: #fff !important;
            border: none !important;
            opacity: 1;
            cursor: not-allowed;
        }
        .icon-success { color: #79c5b6; font-size: 2.5rem; }
        .title-success { font-size: 1.3rem; font-weight: 600; color: #2c645c; }
        #downloadAlert { font-size: 0.95rem; }
    </style>
</head>
<body>
    <div class="card card-custom mx-auto">
        <div class="text-center mb-3">
            <i class="bi bi-file-earmark-text icon-success"></i>
            <div class="title-success mt-2">Contrato gerado com sucesso!</div>
        </div>
        <div class="d-flex flex-column gap-2 align-items-center">
            <button id="downloadBtn" class="btn btn-primary btn-custom me-2" onclick="handleDownload(event)">Download</button>
            
            <form id="aprovarForm" method="post" action="{{route('contrato.aprovar', [$fileName, $dados['dados']['lead_id'] ?? null])}}" style="display:inline-block;">
                @csrf
                <input type="hidden" name="lead_id" value="{{isset($dados['dados']['lead_id']) ? $dados['dados']['lead_id'] : ''}}">
                <input type="hidden" name="cliente_id" value="{{isset($dados['dados']['cliente_id']) ? $dados['dados']['cliente_id'] : ''}}">
                <button type="button" id="aprovarBtn" class="btn btn-success btn-custom me-2" disabled>Aprovar Contrato</button>
            </form>
            
            <button id="reprovarBtn" class="btn btn-warning btn-custom me-2" disabled>Reprovar Contrato</button>
            
            <button id="encaminharBtn" class="btn btn-info btn-custom d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#assinaturaModal" disabled>
                <i class="bi bi-send"></i> Encaminhar para Assinatura
            </button>
            
            <a href="/" id="linkHome" class="btn btn-secondary btn-custom mt-2" style="display:none;">Página Inicial</a>
        </div>
        <div id="downloadAlert" class="mt-3 text-danger fw-bold text-center" style="display:none;">
            O download só pode ser realizado uma vez. Caso precise novamente, gere um novo contrato.
        </div>
        <form id="downloadForm" action="{{ route('contrato.download', $fileName) }}" method="get" target="_blank" style="display:none;"></form>
    </div>

    <!-- Modal Assinatura -->
    <div class="modal fade" id="assinaturaModal" tabindex="-1" aria-labelledby="assinaturaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar Contrato para assinatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('teste.autentique')}}" enctype="multipart/form-data" method="POST" class="p-3 rounded shadow-sm border bg-light">
                        @csrf
                        <input type="hidden" name="lead_id" value="{{isset($dados['dados']['lead_id']) ? $dados['dados']['lead_id'] : ''}}">
                        <div class="mb-3">
                            <label for="Nome_Documento" class="form-label fw-semibold">Nome do Documento</label>
                            <input type="text" name="nome_documento" class="form-control" id="Nome_Documento" value="Contrato - {{$dados['dados']['razaoSocialCliente'] ?? 'Cliente'}}" placeholder="Ex: Contrato de Prestação de Serviços">
                        </div>
                        <div class="mb-3">
                            <label for="Documento" class="form-label fw-semibold">Documento para assinatura</label>
                            <input type="file" name="documento" id="Documento" class="form-control" accept=".pdf" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-people"></i> Signatários</label>
                            <div id="signatariosList">
                                <div class="input-group mb-2">
                                    <input type="email" class="form-control" name="emails[]" placeholder="E-mail do signatário" required>
                                    <button class="btn btn-danger btn-remove-signatario" type="button" style="display:none;">&times;</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="adicionarSignatario()">
                                <i class="bi bi-plus"></i> Adicionar signatário
                            </button>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-primary col-sm-12">Enviar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function handleDownload(e) {
            e.preventDefault();
            const btn = document.getElementById('downloadBtn');
            const btnAprovar = document.getElementById('aprovarBtn');
            const btnReprovar = document.getElementById('reprovarBtn');
            btn.disabled = true;
            btn.innerText = 'Download realizado';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
            btnAprovar.disabled = false;
            btnReprovar.disabled = false;
            document.getElementById('downloadAlert').style.display = 'block';
            document.getElementById('downloadForm').submit();
        }

        // Aprovar contrato via fetch
        document.getElementById('aprovarBtn').addEventListener('click', function() {
            const btnAprovar = document.getElementById('aprovarBtn');
            const btnReprovar = document.getElementById('reprovarBtn');
            const btnEncaminhar = document.getElementById('encaminharBtn');
            btnAprovar.disabled = true;
            btnReprovar.disabled = true;
            btnAprovar.innerText = 'Aprovando...';
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch("{{ route('contrato.aprovar', [$fileName, $dados['dados']['lead_id'] ?? null]) }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(function(response) {
                return response.json().catch(function(jsonErr) {
                    console.error('Erro ao fazer parse do JSON:', jsonErr);
                    return null;
                });
            })
            .then(function(data) {
                if (data && data.success) {
                    btnAprovar.innerText = 'Aprovado';
                    btnAprovar.classList.remove('btn-success');
                    btnAprovar.classList.add('btn-secondary');
                    btnEncaminhar.disabled = false;
                    document.getElementById('linkHome').style.display = 'block';
                } else {
                    btnAprovar.disabled = false;
                    btnReprovar.disabled = false;
                    btnAprovar.innerText = 'Aprovar Contrato';
                    alert('Erro ao aprovar contrato.');
                }
            })
            .catch(function(e) {
                btnAprovar.disabled = false;
                btnReprovar.disabled = false;
                btnAprovar.innerText = 'Aprovar Contrato';
                alert('Erro de conexão ao aprovar contrato.');
                console.error('Erro no fetch:', e);
            });
        });

        // Reprovar contrato - apenas habilita página inicial
        document.getElementById('reprovarBtn').addEventListener('click', function() {
            const btnAprovar = document.getElementById('aprovarBtn');
            const btnReprovar = document.getElementById('reprovarBtn');
            btnAprovar.disabled = true;
            btnReprovar.disabled = true;
            btnReprovar.innerText = 'Reprovado';
            btnReprovar.classList.remove('btn-warning');
            btnReprovar.classList.add('btn-secondary');
            document.getElementById('linkHome').style.display = 'block';
        });

        // Função para adicionar/remover signatários dinamicamente
        function adicionarSignatario() {
            const list = document.getElementById('signatariosList');
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="email" class="form-control" name="emails[]" placeholder="E-mail do signatário" required>
                <button class="btn btn-danger btn-remove-signatario" type="button">&times;</button>
            `;
            div.querySelector('.btn-remove-signatario').addEventListener('click', function() {
                div.remove();
                atualizarBotoesRemover(list);
            });
            list.appendChild(div);
            atualizarBotoesRemover(list);
        }

        function atualizarBotoesRemover(list) {
            const grupos = list.querySelectorAll('.input-group');
            grupos.forEach((grupo, idx) => {
                const btn = grupo.querySelector('.btn-remove-signatario');
                btn.style.display = grupos.length > 1 && idx > 0 ? '' : 'none';
            });
        }

        // Inicializar botões de remover ao carregar a página
        document.addEventListener('DOMContentLoaded', function () {
            atualizarBotoesRemover(document.getElementById('signatariosList'));
        });
    </script>
</body>
</html>
