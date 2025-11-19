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

        .encabezado-principal {
            background-color: #F7F7F7;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .contenedor-encabezado {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .imagen-encabezado {
            max-width: 600px;
            width: 100%;
            height: auto;
            display: block;
        }

        @media (max-width: 768px) {
            .encabezado-principal {
                padding: 10px 15px;
            }

            .imagen-encabezado {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <header class="encabezado-principal">
        <div class="contenedor-encabezado">
            <img src="<?php echo isset($base_url) ? $base_url : ''; ?>../../../../assets/img/encabezado.jpeg"
                alt="Encabezado MARINA-CIIT"
                class="imagen-encabezado">
        </div>
    </header>