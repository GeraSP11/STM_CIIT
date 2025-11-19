<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Marina - Corredor Interoceánico'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .barra-titulo {
            background: #4D2132;
            padding: 15px 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .contenedor-titulo {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
        }

        .texto-titulo {
            color: #ffffff;
            font-size: 32px;
            font-weight: 300;
            letter-spacing: 1px;
            flex: 1;
        }

        .logo-circular {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            margin-left: 30px;
        }

        .logo-circular img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .barra-titulo {
                padding: 15px 20px;
            }

            .contenedor-titulo {
                flex-direction: row;
                gap: 15px;
            }

            .texto-titulo {
                font-size: 20px;
            }

            .logo-circular {
                width: 60px;
                height: 60px;
                margin-left: 15px;
            }
        }

        @media (max-width: 480px) {
            .texto-titulo {
                font-size: 18px;
            }

            .logo-circular {
                width: 50px;
                height: 50px;
                margin-left: 10px;
            }
        }
    </style>
</head>

<body>
    <header class="barra-titulo">
        <div class="contenedor-titulo">
            <h1 class="texto-titulo">
                <?php echo isset($titulo_seccion) ? htmlspecialchars($titulo_seccion) : 'Titulo dinamico de módulo'; ?>
            </h1>
            <div class="logo-circular">
                <img src="<?php echo isset($base_url) ? $base_url : ''; ?>../../../../assets/img/logo_encabezado.jpeg"
                    alt="Logo Corredor Interoceánico">
            </div>
        </div>
    </header>