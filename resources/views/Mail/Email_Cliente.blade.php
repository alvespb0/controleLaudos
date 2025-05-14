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

        .email-container p {
            font-size: 16px;
            margin-bottom: 20px;
            color: var(--gray-color);
        }

    </style>
</head>
<body>
    <div class="email-container">
        <p>
            {{$body}}
        </p>
    </div>
</body>
</html>
