<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ni modo</title>

    <!-- Bootstrap local -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #541C33;
            --secondary: #4B0000;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .card-custom {
            background: white;
            padding: 3rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s;
            max-width: 450px;
            text-align: center;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-custom img {
            width: 100%;
            max-width: 250px;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
        }

        h1 {
            color: var(--primary);
            font-size: clamp(1.8rem, 4vw, 2.3rem);
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .btn-back {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.7rem 1.2rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(84, 28, 51, 0.3);
        }
    </style>
</head>

<body>

    <div class="card-custom">
        <img src="/assets/img/olvidarcontra.png" alt="Imagen">

        <h1> por wei jfakjaskhfask</h1>

        <button class="btn-back" onclick="history.back()">Atrás</button>
    </div>

</body>

</html>
