<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background-color: white;
            max-width: 600px;
            margin: 40px auto;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            color: #333;
        }

        .email-container h2 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 20px;
        }

        .email-container p {
            font-size: 16px;
            margin-bottom: 20px;
            color: var(--gray-color);
        }

        .token-box {
            font-size: 32px;
            font-weight: bold;
            background-color: var(--primary-color);
            color: black;
            padding: 12px 28px;
            display: inline-block;
            border-radius: 12px;
            letter-spacing: 5px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: var(--gray-color);
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Olá, {{ $nome }}!</h2>
        <p>Recebemos uma solicitação para redefinir sua senha. Use o código abaixo para continuar:</p>
        <div class="token-box">{{ $token }}</div>
        <p>Este código expira em 15 minutos.</p>
        <p class="footer">Se você não solicitou essa alteração, apenas ignore este e-mail.</p>
    </div>
</body>
</html>
