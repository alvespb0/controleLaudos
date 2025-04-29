<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        .container {
            background: #f4f4f4;
            padding: 30px;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .token {
            font-size: 28px;
            font-weight: bold;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            display: inline-block;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Olá, {{ $nome }}!</h2>
        <p>Você solicitou a recuperação de senha. Use o código abaixo:</p>
        <div class="token">{{ $token }}</div>
        <p>Este código expira em 15 minutos.</p>
        <p>Se você não solicitou isso, ignore este e-mail.</p>
    </div>
</body>
</html>
