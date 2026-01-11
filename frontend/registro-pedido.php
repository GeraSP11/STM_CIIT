<?php

require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Cliente", "Jefe de Almacén"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Pedidos';
$seccion = 'Registrar pedidos';
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
        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .captura-card {
            width: 90%;
            max-width: 900px;
            border: 1px solid #ccc;
        }

        .captura-header {
            background-color: #5b1d3b;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
        }

        .form-control {
            height: 32px;
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

    <!-- Contenedor principal -->
    <div class="container-fluid d-flex justify-content-center align-items-center">
        <form class="card captura-card" method="post" action="#">

            <!-- Encabezado -->
            <div class="captura-header">
                En captura
            </div>

            <!-- Cuerpo del formulario -->
            <div class="card-body">

                <!-- Fechas -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fechaSolicitud" class="form-label">Fecha de Solicitud:</label>
                        <input
                            type="date"
                            id="fechaSolicitud"
                            name="fechaSolicitud"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="fechaEntrega" class="form-label">Fecha estimada de entrega:</label>
                        <input
                            type="date"
                            id="fechaEntrega"
                            name="fechaEntrega"
                            class="form-control"
                            required>
                    </div>
                </div>

                <!-- Localidades -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="origen" class="form-label">Localidad origen:</label>
                        <input
                            type="text"
                            id="origen"
                            name="origen"
                            class="form-control"
                            placeholder="Ciudad de origen"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="destino" class="form-label">Localidad destino:</label>
                        <input
                            type="text"
                            id="destino"
                            name="destino"
                            class="form-control"
                            placeholder="Ciudad de destino"
                            required>
                    </div>
                </div>

                <!-- Productos -->
                <div class="text-center mb-3">
                    <label class="form-label me-2">Productos:</label>
                    <button
                        type="button"
                        class="btn btn-outline-dark btn-sm rounded-circle"
                        id="btnAgregarProducto"
                        title="Agregar producto">
                        +
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- CRIPT DEL FORMULARIO -->


</body>

</html>