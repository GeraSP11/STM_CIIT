<?php
$page_title = 'MARINA Corredor Interoceánico';
$seccion = 'Misión y Visión';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap CSS local -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

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
                            <li><a class="dropdown-item" href="/registro-personal.php">Registrar</a></li>
                            <li><a class="dropdown-item" href="/consultar-personal.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-personal.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-personal.php">Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Usuarios -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="usuariosDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Usuarios</a>
                        <ul class="dropdown-menu" aria-labelledby="usuariosDropdown">
                            <li><a class="dropdown-item" href="/consultar-usuarios.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-usuarios.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-usuarios.php">Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Productos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="productosDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Productos</a>
                        <ul class="dropdown-menu" aria-labelledby="productosDropdown">
                            <li><a class="dropdown-item" href="/registro-productos.php">Registrar</a></li>
                            <li><a class="dropdown-item" href="/consultar-productos.php">Consultar</a></li>
                            <li><a class="dropdown-item" href="/actualizar-productos.php">Actualizar</a></li>
                            <li><a class="dropdown-item" href="/eliminar-productos.php">Eliminar</a></li>
                        </ul>
                    </li>

                    <!-- Localidades -->
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
                </ul>

                <span class="navbar-text">
                    <a class="nav-link" href="#">Cerrar sesión</a>
                </span>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb inline -->
    <nav aria-label="breadcrumb" class="mt-2" style="padding-left: 15px; font-size: 18px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Contenido principal -->
    <div class="container my-4">
        <h1 class="text-center text-purple"><?php echo $seccion; ?></h1>

        <h2 class="text-purple">MISIÓN</h2>
        <p>
            Instrumentar una Plataforma Logística multimodal que integre la prestación de servicios
            de las Administraciones del Sistema Portuario Nacional Coatzacoalcos S.A. de C.V.,
            Salina Cruz S.A. de C.V., Dos Bocas S.A. de C.V. y Puerto Chiapas S.A. de C.V., y su
            interconexión mediante transporte ferroviario, por conducto del Ferrocarril del Istmo
            de Tehuantepec, S.A. de C.V.; mediante las acciones que le permitan armonizar la
            plataforma multimodal, así como la adquisición, concesión, o en su caso, enajenación
            de los inmuebles denominados Polos de Desarrollo para el Bienestar, para el desarrollo
            económico, social y cultural, desde el ámbito sustentable e incluyente, en beneficio de
            la población de la Región del Istmo de Tehuantepec y su Área de Influencia.
        </p>

        <h2 class="text-purple">VISIÓN</h2>
        <p>
            Ser un organismo que consolide el desarrollo integral, sustentable, sostenible e incluyente
            en el Istmo de Tehuantepec, a través de la plataforma logística, conformada por la
            prestación de servicios de administración portuaria y su interconexión mediante transporte
            ferroviario, y de las demás actividades que concrete el desarrollo de la región del Istmo
            de Tehuantepec y en las áreas de influencia, a fin de que genere un crecimiento económico
            e integral para la población desde el ámbito social y cultural.
        </p>
    </div>


    <!-- Bootstrap JS local -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>