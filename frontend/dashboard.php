<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";

requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Cliente", "Jefe de Almacén"]);

$page_title = 'MARINA Corredor Interoceánico';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            overflow: hidden;
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

        /* Menú */
        .menu-bar {
            background-color: #3b0d20;
            flex-shrink: 0;
        }

        .menu-bar .nav-link {
            color: white;
            font-size: clamp(12px, 1.1vw, 16px);
            padding: 0.5vw 1vw;
        }

        .menu-bar .nav-link:hover {
            text-decoration: underline;
        }

        .menu-bar .navbar-text .nav-link {
            color: white;
        }

        .dropdown-menu {
            font-size: clamp(11px, 1vw, 15px);
        }

        /* Enlaces de sección */
        .section-links {
            padding: 0.5vw 1.5vw;
            flex-shrink: 0;
        }

        .section-link {
            font-size: clamp(14px, 1.4vw, 22px);
            color: #6b6b6b;
            text-decoration: none;
        }

        .section-link:hover {
            text-decoration: underline;
            color: #4a1026;
        }

        /* Logo centrado - ocupa el espacio restante */
        .logo-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 0;
            padding: 1vw;
        }

        .imagen-encabezado {
            max-width: 40%;
            max-height: 100%;
            height: auto;
            width: auto;
            object-fit: contain;
        }

        /* Ajustes para pantallas pequeñas en altura */
        @media screen and (max-height: 600px) {
            .top-bar {
                padding: 8px 10px;
            }

            .section-links {
                padding: 5px 15px;
            }

            .imagen-encabezado {
                max-width: 35%;
            }
        }

        /* Ajustes para pantallas muy grandes */
        @media screen and (min-width: 1920px) {
            .imagen-encabezado {
                max-width: 500px;
            }
        }

        /* Móviles */
        @media screen and (max-width: 768px) {

            html,
            body {
                overflow-y: auto;
                overflow-x: hidden;
            }

            .imagen-encabezado {
                max-width: 70%;
            }

            .logo-container {
                min-height: 300px;
            }
        }
    </style>
</head>

<body>
    <div class="top-bar">
        SISTEMA DE TRANSPORTE MULTIMODAL - CORREDOR INTEROCEÁNICO DEL ISTMO DE TEHUANTEPEC
    </div>

    <nav class="navbar navbar-expand-lg menu-bar">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="personalDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Personal</a>
                            <ul class="dropdown-menu" aria-labelledby="personalDropdown">
                                <li><a class="dropdown-item" href="/registro-personal.php">Registrar</a></li>
                                <li><a class="dropdown-item" href="/consultar-personal.php">Consultar</a></li>
                                <li><a class="dropdown-item" href="/actualizar-personal.php">Actualizar</a></li>
                                <li><a class="dropdown-item" href="/eliminar-personal.php">Eliminar</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="usuariosDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Usuarios</a>
                            <ul class="dropdown-menu" aria-labelledby="usuariosDropdown">
                                <li><a class="dropdown-item" href="/consultar-usuarios.php">Consultar</a></li>
                                <li><a class="dropdown-item" href="/actualizar-usuarios.php">Actualizar</a></li>
                                <li><a class="dropdown-item" href="/eliminar-usuarios.php">Eliminar</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS", "Operador Logístico", "Cliente", "Jefe de Almacén"])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="productosDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Productos
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="productosDropdown">
                                <?php if ($_SESSION['cargo'] !== "Cliente"): ?>
                                    <li><a class="dropdown-item" href="/registro-productos.php">Registrar</a></li>
                                    <li><a class="dropdown-item" href="/actualizar-productos.php">Actualizar</a></li>
                                    <li><a class="dropdown-item" href="/eliminar-productos.php">Eliminar</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="/consultar-productos.php">Consultar</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="localidadesDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Localidades</a>
                            <ul class="dropdown-menu" aria-labelledby="localidadesDropdown">
                                <li><a class="dropdown-item" href="/registro-localidades.php">Registrar</a></li>
                                <li><a class="dropdown-item" href="/consultar-localidades.php">Consultar</a></li>
                                <li><a class="dropdown-item" href="/actualizar-localidades.php">Actualizar</a></li>
                                <li><a class="dropdown-item" href="/eliminar-localidades.php">Eliminar</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS"])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="carroceriasDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Carrocerías</a>
                            <ul class="dropdown-menu" aria-labelledby="carroceriasDropdown">
                                <li><a class="dropdown-item" href="/registro-carrocerias.php">Registrar</a></li>
                                <li><a class="dropdown-item" href="/consultar-carrocerias.php">Consultar</a></li>
                                <li><a class="dropdown-item" href="/actualizar-carrocerias.php">Actualizar</a></li>
                                <li><a class="dropdown-item" href="/eliminar-carrocerias.php">Eliminar</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['cargo'], ["Autoridad", "Administrador del TMS", "Cliente"])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="pedidosDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Pedidos</a>
                            <ul class="dropdown-menu" aria-labelledby="pedidosDropdown">
                                <li><a class="dropdown-item" href="/registro-pedido.php">Registrar</a></li>
                                <li><a class="dropdown-item" href="#">Consultar</a></li>
                                <li><a class="dropdown-item" href="#">Actualizar</a></li>
                                <li><a class="dropdown-item" href="#">Eliminar</a></li>
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

    <div class="section-links d-flex justify-content-between">
        <a href="/documentos.php" class="section-link">Documentos</a>
        <a href="/mision-y-vision.php" class="section-link">Misión y Visión</a>
    </div>

    <div class="logo-container">
        <img src="/assets/img/logo_principal.jpeg" alt="Encabezado MARINA-CIIT" class="imagen-encabezado">
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