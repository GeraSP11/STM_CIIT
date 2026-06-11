<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Jefe de Almacén"]);
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Rutas';
$seccion = 'Eliminar Rutas';
?>
<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        h2 {
            text-align: center;
            color: #4a1026;
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        /* Tarjeta principal del formulario */
        .vehiculo-card {
            background-color: #f2f2f2;
            border-radius: 6px;
            padding: 35px 45px 45px 45px;
            max-width: 820px;
            margin: 0 auto;
        }

        /* Etiqueta "Filtro de búsqueda:" */
        .filtro-label {
            display: inline-block;
            background-color: #5a1e2d;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            padding: 5px 14px;
            border-radius: 4px;
            margin-bottom: 14px;
        }

        /* Select del filtro */
        #filtro-select {
            width: 100%;
            height: 42px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            color: #555;
            font-size: 14px;
            padding: 0 12px;
            appearance: auto;
        }

        /* Botón buscar */
        .btn-buscar {
            display: block;
            margin: 28px auto 0 auto;
            background-color: #5a1e2d;
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            padding: 9px 40px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-buscar:hover {
            background-color: #471624;
        }

        /* Área de resultados (oculta por defecto, la lógica la mostrará) */
        #resultado-container {
            max-width: 820px;
            margin: 30px auto 0 auto;
            display: none;
        }
    </style>
</head>

<body>
    <!-- Header dinámico -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
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

    <!-- Título de sección -->
    <h2><?php echo $seccion; ?></h2>

    <!-- Tarjeta de búsqueda -->
    <div class="vehiculo-card">
        <span class="filtro-label">Filtro de búsqueda:</span>

        <select id="filtro-select">
            <option value="" disabled selected>Seleccione un filtro</option>

        </select>

        <button id="btn-buscar-vehiculo" class="btn-buscar">
            Buscar Rutas
        </button>
    </div>

    <!-- Contenedor para resultados (la lógica irá aquí) -->
    <div id="resultado-container">
        <!-- Se llenará dinámicamente -->
    </div>
</body>
</html>