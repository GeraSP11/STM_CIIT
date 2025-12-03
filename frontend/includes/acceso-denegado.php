<?php
session_start();
require_once "../../backend/middleware/no-cache.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        /* Header principal */
        .top-bar {
            background-color: #4a1026;
            color: white;
            padding: 1.5vw 1vw;
            text-align: center;
            font-weight: 600;
            font-size: clamp(12px, 1.3vw, 20px);
            flex-shrink: 0;
        }

        /* Contenedor que centra la tarjeta */
        .content-wrapper {
            flex: 1; /* Ocupa todo el resto del espacio */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-denied {
            width: 420px;
            border-radius: 16px;
            border: none;
            padding: 25px;
            background: white;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .icon-denied {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 15px;
        }

        .btn-primary, .btn-secondary {
            padding: 10px 16px;
            font-size: 15px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <!-- HEADER PRINCIPAL -->
    <div class="top-bar">
        SISTEMA DE TRANSPORTE MULTIMODAL - CORREDOR INTEROCEÁNICO DEL ISTMO DE TEHUANTEPEC
    </div>

    <!-- CONTENIDO CENTRADO -->
    <div class="content-wrapper">
        <div class="card-denied">
            <div class="icon-denied">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <h2 class="fw-bold text-danger">Acceso denegado</h2>

            <p class="text-muted mb-4">
                No tienes permisos para acceder a esta sección.<br>
                Si crees que esto es un error, contacta al administrador.
            </p>

            <a href="/dashboard.php" class="btn btn-primary w-100 mb-2">
                Volver al Dashboard
            </a>

            <a href="/backend/middleware/logout.php" class="btn btn-secondary w-100">
                Cerrar sesión
            </a>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>
