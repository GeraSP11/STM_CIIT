<?php

require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Personal';
$seccion = 'Eliminar Personal';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .main-content {
            background-color: white;
            margin: 40px auto;
            max-width: 1200px;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            min-height: 600px;
        }

        .main-content h2 {
            color: #4a1026;
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 50px;
            text-align: center;
        }

        .search-section {
            background-color: #f8f8f8;
            border-radius: 8px;
            padding: 60px 40px;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-label {
            background-color: #4a1026;
            color: white;
            padding: 12px 40px;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 40px;
        }

        .search-section input {
            width: 100%;
            max-width: 600px;
            padding: 15px 20px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .search-section input::placeholder {
            color: #999;
        }

        .search-section input:focus {
            outline: none;
            border-color: #4a1026;
        }

        .btn-search {
            background-color: #4a1026;
            color: white;
            padding: 12px 50px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-search:hover {
            background-color: #3a0c1f;
        }

        .hidden {
            display: none;
        }

        .warning-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .warning-box strong {
            color: #856404;
            font-size: 1.2rem;
            display: block;
            margin-bottom: 10px;
        }

        .warning-box p {
            color: #856404;
            margin: 0;
            font-size: 1rem;
        }

        .info-display {
            background-color: #f8f8f8;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .info-display h3 {
            color: #333;
            font-size: 1.3rem;
            font-weight: 500;
            margin-bottom: 25px;
            border-bottom: 2px solid #4a1026;
            padding-bottom: 10px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #333;
        }

        .form-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>

    <!-- Header dinámico -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb" style="padding-left: 15px;">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <main class="main-content">
        <h2><?php echo $seccion; ?></h2>

        <!-- Sección de búsqueda -->
        <div class="search-section">
            <div class="search-label">Filtro de búsqueda: *</div>
            <input type="text" id="curp_busqueda" maxlength="18" placeholder="Ingrese la CURP del usuario">
            <br>
            <button type="button" class="btn-search" id="btnBuscar">Buscar</button>
        </div>

        <!-- Sección de información del personal (oculta inicialmente) -->
        <div id="info-section" class="hidden">
            <!-- Campo oculto para almacenar el ID -->
            <input type="hidden" id="id_personal" value="">

            <div class="warning-box">
                <strong>⚠️ ADVERTENCIA</strong>
                <p>Está a punto de eliminar este registro de forma permanente. Esta acción NO se puede deshacer.</p>
            </div>

            <div class="info-display">
                <h3>Información del Personal</h3>
                <div class="info-row">
                    <span class="info-label">CURP:</span>
                    <span class="info-value" id="display_curp">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nombre Completo:</span>
                    <span class="info-value" id="display_nombre">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Apellido Paterno:</span>
                    <span class="info-value" id="display_paterno">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Apellido Materno:</span>
                    <span class="info-value" id="display_materno">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Afiliación Laboral:</span>
                    <span class="info-value" id="display_afiliacion">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cargo:</span>
                    <span class="info-value" id="display_cargo">-</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-delete" id="btnEliminar">Eliminar Registro</button>
                <button type="button" class="btn-cancel" onclick="history.back()">Cancelar</button>
            </div>
        </div>
    </main>

    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/personal.js"></script>
    <script src="/assets/js/alertas.js"></script>
</body>

</html>