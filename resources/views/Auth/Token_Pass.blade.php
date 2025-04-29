<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Token - Controle de Laudos</title>
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

        .form-title {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }

        .token-inputs {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .token-inputs input {
            width: 48px;
            height: 56px;
            font-size: 24px;
            text-align: center;
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            transition: 0.2s;
        }

        .token-inputs input:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(121, 197, 182, 0.2);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
        }

        .toast-error {
            background-color: #f44336 !important;
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
    <div class="login-card">
        <h2 class="form-title">Validar Token</h2>
        <form method="POST" action="{{route('token.validate')}}">
            @csrf
            <div class="token-inputs">
                @for ($i = 1; $i <= 6; $i++)
                    <input type="text" name="digit{{ $i }}" maxlength="1" class="form-control digit-input" required>
                @endfor
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Validar</button>
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

    // Avança automaticamente para o próximo input
    document.querySelectorAll('.digit-input').forEach((input, index, inputs) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
</script>
</body>
</html>
