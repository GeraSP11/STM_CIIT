<?php
$page_title = 'MARINA Corredor Interoceánico';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap CSS local -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Colores y fuentes */
        .top-bar {
            background-color: #4a1026;
            color: white;
            padding: 30px 0;
            text-align: center;
            font-weight: 600;
            font-size: 20px;
        }

        .menu-bar {
            background-color: #3b0d20;
        }

        .menu-bar .nav-link {
            color: white;
        }

        .menu-bar .nav-link:hover {
            text-decoration: underline;
        }

        .section-link {
            font-size: 22px;
            color: #6b6b6b;
            text-decoration: none;
        }

        .section-link:hover {
            text-decoration: underline;
        }

        /* Logo centrado */
        .logo-container {
            display: flex;
            justify-content: center;
            margin: 0px 0;
        }

        .imagen-encabezado {
            max-width: 40%;
            height: auto;
        }
    </style>
</head>

<body>
    <!-- HEADER PRINCIPAL -->
    <div class="top-bar">
        SISTEMA DE TRANSPORTE MULTIMODAL - CORREDOR INTEROCEÁNICO DEL ISTMO DE TEHUANTEPEC
    </div>

    <!-- MENÚ -->
    <nav class="navbar navbar-expand-lg menu-bar">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Personal -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="personalDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Personal</a>
                        <ul class="dropdown-menu" aria-labelledby="personalDropdown">
                            <li><a class="dropdown-item" href="#">Registrar</a></li>
                            <li><a class="dropdown-item" href="#">Consultar</a></li>
                            <li><a class="dropdown-item" href="#">Actualizar</a></li>
                            <li><a class="dropdown-item" href="#">Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Usuarios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="usuariosDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Usuarios</a>
                        <ul class="dropdown-menu" aria-labelledby="usuariosDropdown">
                            <li><a class="dropdown-item" href="#">Consultar</a></li>
                            <li><a class="dropdown-item" href="#">Actualizar</a></li>
                            <li><a class="dropdown-item" href="#">Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Productos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="productosDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Productos</a>
                        <ul class="dropdown-menu" aria-labelledby="productosDropdown">
                            <li><a class="dropdown-item" href="#">Registrar</a></li>
                            <li><a class="dropdown-item" href="#">Consultar</a></li>
                            <li><a class="dropdown-item" href="#">Actualizar</a></li>
                            <li><a class="dropdown-item" href="#">Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Localidades -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="localidadesDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Localidades</a>
                        <ul class="dropdown-menu" aria-labelledby="localidadesDropdown">
                            <li><a class="dropdown-item" href="#">Registrar</a></li>
                            <li><a class="dropdown-item" href="#">Consultar</a></li>
                            <li><a class="dropdown-item" href="#">Actualizar</a></li>
                            <li><a class="dropdown-item" href="#">Eliminar</a></li>
                        </ul>
                    </li>
                </ul>

                <span class="navbar-text">
                    <a class="nav-link" href="#">Cerrar sesión</a>
                </span>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <div class="container-fluid mt-2 px-3">
        <div class="d-flex justify-content-between">
            <a href="#" class="section-link">Documentos</a>
            <a href="#" class="section-link">Misión y Visión</a>
        </div>
    </div>

    <!-- Logo centrado -->
    <div class="logo-container">
        <img src="/assets/img/logo_principal.jpeg" alt="Encabezado MARINA-CIIT" class="imagen-encabezado">
    </div>

    <!-- Bootstrap JS local -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
