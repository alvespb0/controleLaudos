<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Controle de Laudos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 3rem 2rem;
            width: 100%;
            max-width: 400px;
            transition: 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(121, 197, 182, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
        }

        .form-title {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }

        .small-link {
            display: block;
            margin-top: 1rem;
            text-align: center;
            color: var(--gray-color);
        }

        .small-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        .toast-error {
            background-color: #f44336 !important; /* vermelho forte */
            color: white !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .toast-error .toast-message {
            font-weight: bold;
        }
        .toast-error .toast-close-button {
            color: white;
        }

    </style>
</head>
<body>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="login-card">
        <h2 class="form-title">Redefinição de senha</h2>
        <form method="POST" action="{{route('alter.password')}}">
            @csrf
            <input type="hidden" name="id" value="{{$userId}}">
            <input type="hidden" name="tokenId" value="{{$tokenId}}">
            <div class="mb-3">
                <label for="password" class="form-label">Nova Senha</label>
                <input type="password" class="form-control" id="password" name="password" required autofocus>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>
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
        toastr.error("{{ session('mensagem') }}");
    @endif
</script>

</body>
</html>
