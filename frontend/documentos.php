<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";

// Mantener los mismos permisos que el Dashboard para consistencia
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Cliente", "Jefe de Almacén"]);

$page_title = 'MARINA Corredor Interoceánico';
$seccion = 'Documentos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: white;
        }

        /* Header principal */
        .top-bar {
            background-color: #4a1026;
            color: white;
            padding: 1.5vw 1vw;
            text-align: center;
            font-weight: 600;
            font-size: clamp(12px, 1.3vw, 20px);
        }

        /* Menú */
        .menu-bar {
            background-color: #3b0d20;
        }

        .menu-bar .nav-link {
            color: white;
            font-size: clamp(12px, 1.1vw, 16px);
            padding: 0.5vw 1vw;
        }

        .menu-bar .nav-link:hover {
            text-decoration: underline;
        }

        .content-area {
            padding: 30px 60px;
        }

        .breadcrumb {
            font-size: 18px;
            margin-left: 20px;
            margin-top: 10px;
        }

        .title-doc {
            font-size: 32px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .doc-left {
            float: left;
            width: 40%;
            font-size: 18px;
            line-height: 1.6;
        }

        .doc-right {
            float: right;
            width: 55%;
            font-size: 16px;
            text-align: justify;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    <div class="top-bar">
        SISTEMA DE TRANSPORTE MULTIMODAL - CORREDOR INTEROCEÁNICO DEL ISTMO DE TEHUANTEPEC
    </div>

    <nav class="navbar navbar-expand-lg menu-bar">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    
                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Personal</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/registro-personal.php">Registrar</a></li>
                            <li><a class="dropdown-item" href="/consultar-personal.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-personal.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-personal.php">Eliminar</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Usuarios</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/consultar-usuarios.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-usuarios.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-usuarios.php">Eliminar</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Productos</a>
                        <ul class="dropdown-menu">
                            <?php if ($_SESSION['cargo'] !== "Cliente"): ?>
                                <li><a class="dropdown-item" href="/registro-productos.php">Registrar</a></li>
                                <li><a class="dropdown-item" href="/actualizar-productos.php">Actualizar</a></li>
                                <li><a class="dropdown-item" href="/eliminar-productos.php">Eliminar</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/consultar-productos.php">Consultar</a></li>
                        </ul>
                    </li>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Localidades</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/registro-localidades.php">Registrar</a></li>
                            <li><a class="dropdown-item" href="/consultar-localidades.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-localidades.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-localidades.php">Eliminar</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Carrocerías</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/registro-carrocerias.php">Registrar</a></li>
                            <li><a class="dropdown-item" href="/consultar-carrocerias.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-carrocerias.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-carrocerias.php">Eliminar</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS", "Operador Logístico"])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Vehículos</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/registro-vehiculos.php">Registrar</a></li>
                            <li><a class="dropdown-item" href="/consultar-vehiculos.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-vehiculos.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-vehiculos.php">Eliminar</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>

                <span class="navbar-text">
                    <a class="nav-link" href="#" onclick="confirmarLogout();">Cerrar sesión</a>
                </span>

            </div>
        </div>
    </nav>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active">Documentos</li>
        </ol>
    </nav>

    <div class="content-area">
        <h1 class="title-doc">Documentos</h1>

        <div class="doc-left">
            Documento Técnico del Sistema de Transporte Multimodal del CIIT<br><br>
            Lunes, 20 de octubre de 2025
        </div>

        <div class="doc-right">
            Este documento contiene la especificación completa de los requerimientos funcionales y no funcionales del Sistema de Gestión Logística Multimodal del Corredor Interoceánico del Istmo de Tehuantepec, incluyendo procesos, restricciones, roles y anexos técnicos.
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarLogout() {
            if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
                window.location.href = "/backend/middleware/logout.php";
            }
        }
    </script>
</body>
</html>