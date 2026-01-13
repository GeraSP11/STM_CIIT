<?php

require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Pedidos';
$seccion = 'Consulta de Pedidos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        .form-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            margin: 40px auto;
        }

        /* Etiqueta "Filtro de búsqueda" */
        .form-label-box {
            background-color: #4a1026;
            color: white;
            width: fit-content;
            padding: 8px 16px;
            font-weight: bold;
            margin: 0 0 15px 0;
            text-align: left;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Inputs */
        .form-control-custom {
            width: 100%;
            max-width: 520px;
            padding: 12px;
            margin: 0 auto 20px auto;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: 15px;
            display: block;
        }

        /* Botón */
        .btn-custom {
            background-color: #4a1026;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        /* Título */
        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
        }

        /* Separador "o" */
        .separador-o span {
            font-weight: bold;
            color: #555;
        }

        /* Ajustes móviles */
        @media (max-width: 576px) {
            .form-container {
                padding: 20px;
                margin: 20px auto;
            }

            .btn-custom {
                width: 100%;
            }
        }

    </style>
</head>

<body>

    <!-- Header -->
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

    <!-- Contenedor formulario consulta -->
    <div id="consultaPedido" class="form-container">
        <form id="formConsultaPedido">

            <!-- Etiqueta -->
            <div class="form-label-box">Filtro de búsqueda: *</div>

            <!-- Identificador del pedido -->
            <input
                type="text"
                class="form-control-custom"
                id="identificadorPedido"
                name="identificadorPedido"
                placeholder="Identificador del pedido"
                required
                maxlength="30"
            >

            <!-- Separador O -->
            <div class="d-flex align-items-center my-3">
                <div class="flex-grow-1 border-top"></div>
                <span class="mx-2">o</span>
                <div class="flex-grow-1 border-top"></div>
            </div>

            <!-- Localidad Origen -->
            <input
                type="text"
                class="form-control-custom"
                id="localidadOrigen"
                name="localidadOrigen"
                placeholder="Localidad origen"
                maxlength="100"
            >

            <!-- Localidad Destino -->
            <input
                type="text"
                class="form-control-custom"
                id="localidadDestino"
                name="localidadDestino"
                placeholder="Localidad destino"
                maxlength="100"
            >

            <!-- Botón -->
            <div class="d-flex justify-content-center">
                <button
                    id="btnConsultarPedido"
                    type="submit"
                    class="btn btn-custom"
                >
                    Consultar
                </button>
            </div>

        </form>
    </div>



    <!-- Contenedor tabla resultados, oculto inicialmente -->
    <div id="tablaResultados" class="form-container" style="display:none;">
        <table id="tablaPersonal" class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Afiliación Laboral</th>
                    <th>Cargo</th>
                    <th>CURP</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="d-flex justify-content-center">
            <button id="btnVolver" class="btn btn-custom">Volver</button>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- TU SCRIPT DEL FORMULARIO -->
    <script src="/assets/js/personal.js"></script>
</body>

</html>