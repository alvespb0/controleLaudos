<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento Gerado</title>
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
        .btn-whatsapp {
            background-color: #25d366;
            color: #fff;
            border: none;
        }
        .btn-whatsapp:hover, .btn-whatsapp:focus {
            background-color: #128c7e;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-whatsapp:disabled, .btn-whatsapp.disabled {
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
            <i class="bi bi-journal-bookmark-fill icon-success"></i>
            <div class="title-success mt-2">{{$fileName}} gerado com sucesso!</div>
        </div>
        <div class="d-flex flex-column gap-2 align-items-center">
            <form id="aprovarForm" method="post" action="{{ route('orcamento.aprovar', [$fileName, $dados['lead_id']]) }}" style="display:inline-block;">
                @csrf
                <input type="hidden" name="lead_id" value="{{$dados['lead_id']}}">
                <button type="button" id="aprovarBtn" class="btn btn-success btn-custom me-2">Aprovar</button>
            </form>
            <form method="POST" action="{{ route('orcamento.retificar') }}" style="display:inline-block;">
                @csrf
                @foreach ($dados as $key => $value)
                    <input type="hidden" name="dados[{{ $key }}]" value="{{ $value }}">
                @endforeach
                <input type="hidden" name="fileName" value="{{$fileName}}">
                <button type="submit" class="btn btn-warning btn-custom me-2">Retificar</button>
            </form>
            <button id="downloadBtn" class="btn btn-primary btn-custom me-2" onclick="handleDownload(event)">Download</button>
            <button id="whatsappBtn" class="btn btn-whatsapp btn-custom d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#whatsappModal" disabled>
                <i class="bi bi-whatsapp"></i> Encaminhar pelo WhatsApp
            </button>
            <a href="/" id="linkHome" class="mt-2 small text-decoration-none text-secondary" style="display:none;">Página Inicial</a>
        </div>
        <div id="downloadAlert" class="mt-3 text-danger fw-bold text-center" style="display:none;">
            O download só pode ser realizado uma vez. Caso precise novamente, gere um novo orçamento.
        </div>
        <form id="downloadForm" action="{{ route('orcamento.download', $fileName) }}" method="get" target="_blank" style="display:none;"></form>
    </div>
    <!-- Modal WhatsApp -->
    <div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
                <h5 class="modal-title">Atendimento via Zappy Plataforma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3" role="alert">
                            Esta mensagem abrirá um atendimento na plataforma de WhatsApp para o seu setor.
                        </div>
                        <form enctype="multipart/form-data" action="{{route('orcamento.zappy')}}" method="POST">
                            @csrf
                            <input type="hidden" name="telefone" value="{{ $dados['telefoneCliente']}}">
                            <div class="mb-3">
                                <label class="form-label">Número do Cliente</label>
                                <input type="text" class="form-control" value="{{$dados['telefoneCliente']}}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensagem</label>
                                <textarea class="form-control" name="mensagem" rows="3" required placeholder="Digite sua mensagem..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="Orcamento"  class="form-label">Orçamento em PDF</label>
                                <input type="file" class="form-control" name="fileOrcamento" id="Orcamento">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Enviar pelo Zappy</button>
                            </div>
                        </form>
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
            btn.disabled = true;
            btn.innerText = 'Download realizado';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
            document.getElementById('downloadAlert').style.display = 'block';
            document.getElementById('downloadForm').submit();
        }

        // Aprovar via fetch
        document.getElementById('aprovarBtn').addEventListener('click', function() {
            const btnAprovar = document.getElementById('aprovarBtn');
            const btnWhatsapp = document.getElementById('whatsappBtn');
            btnAprovar.disabled = true;
            btnAprovar.innerText = 'Aprovando...';
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('Enviando requisição para aprovação...');
            fetch("{{ route('orcamento.aprovar', [$fileName, $dados['lead_id']]) }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(function(response) {
                console.log('Status HTTP:', response.status);
                return response.json().catch(function(jsonErr) {
                    console.error('Erro ao fazer parse do JSON:', jsonErr);
                    return null;
                });
            })
            .then(function(data) {
                console.log('Resposta JSON:', data);
                if (data && data.success) {
                    btnAprovar.innerText = 'Aprovado';
                    btnAprovar.classList.remove('btn-success');
                    btnAprovar.classList.add('btn-secondary');
                    btnWhatsapp.disabled = false;
                    document.getElementById('linkHome').style.display = 'block';
                } else {
                    btnAprovar.disabled = false;
                    btnAprovar.innerText = 'Aprovar';
                    alert('Erro ao aprovar orçamento.');
                    console.error('Resposta inesperada:', data);
                }
            })
            .catch(function(e) {
                btnAprovar.disabled = false;
                btnAprovar.innerText = 'Aprovar';
                alert('Erro de conexão ao aprovar orçamento.');
                console.error('Erro no fetch:', e);
            });
        });
    </script>
</body>
</html>
