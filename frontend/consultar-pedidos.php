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

    <!-- Bootstrap -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/icons/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        .form-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            margin: 40px auto;
        }

        .form-label-box {
            background-color: #4a1026;
            color: #fff;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 15px;
            width: fit-content;
        }

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

        .btn-custom {
            background-color: #4a1026;
            color: #fff;
            padding: 12px 40px;
            border-radius: 4px;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        .btn-nueva-busqueda {
            background-color: #6c757d;
            color: #fff;
            padding: 10px 30px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .btn-nueva-busqueda:hover {
            background-color: #5a6268;
            color: #fff;
        }

        h2 {
            text-align: center;
            color: #4a1026;
        }

        /* ====== MODAL ESTILO SEGUNDA VISTA ====== */
        .modal-header-custom {
            background-color: #4a1026;
            color: #fff;
        }

        .detalle-box {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px 12px;
            height: 100%;
        }

        .detalle-box small {
            display: block;
            font-weight: bold;
            color: #555;
            font-size: 12px;
        }

        .detalle-box span {
            font-size: 15px;
            color: #000;
        }
    </style>
</head>

<body>

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

<!-- FORMULARIO -->
<!-- FORMULARIO -->
<div class="form-container">
    <form id="formConsulta">
        <div class="form-label-box">Filtro de búsqueda: *</div>
        <input 
            id="clavePedido" 
            class="form-control-custom" 
            placeholder="Clave del pedido (ej: PED-2024-010)"
            pattern="PED-\d{4}-\d{3}"
            title="Formato: PED-YYYY-XXX (ej: PED-2024-010)"
        >
        <div class="d-flex align-items-center my-3">
            <div class="flex-grow-1 border-top"></div>
            <span class="mx-2 fw-bold">o</span>
            <div class="flex-grow-1 border-top"></div>
        </div>
        <!-- Cambiado a SELECT -->
        <select id="origen" class="form-control-custom">
            <option value="">Seleccionar localidad origen</option>
            <!-- Se llenan dinámicamente -->
        </select>
        <select id="destino" class="form-control-custom">
            <option value="">Seleccionar localidad destino</option>
            <!-- Se llenan dinámicamente -->
        </select>
        <div class="text-center">
            <button type="submit" class="btn btn-custom">Consultar</button>
        </div>
    </form>
</div>


<!-- Contenedor resultados -->
<div id="tablaResultados" class="container mt-4" style="display:none;">
    
    <!-- Botón para nueva búsqueda -->
    <div class="text-end mb-3">
        <button class="btn btn-nueva-busqueda" onclick="nuevaBusqueda()">
            <i class="bi bi-arrow-left"></i> Nueva búsqueda
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header text-white fw-bold" style="background:#4a1026;">
            Resultados obtenidos:
        </div>
        <div class="card-body bg-light">
            <table class="table table-bordered table-hover" id="tablaPedidos">
                <thead>
                    <tr style="background-color:#4a1026; color:white;">
                        <th>ID</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Filas se llenan dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL DETALLE -->
<div class="modal fade" id="modalPedido" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header modal-header-custom">
                <h5 class="modal-title">Gestión de Pedidos</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body bg-light">
                <div class="row g-3">

                     <div class="col-md-3">
                        <div class="detalle-box">
                            <small>ID</small>
                            <span id="detalle-id"></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="detalle-box">
                            <small>Estatus</small>
                            <span id="detalle-estatus"></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="detalle-box">
                            <small>Fecha solicitud</small>
                            <span id="detalle-fecha-solicitud"></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="detalle-box">
                            <small>Fecha entrega</small>
                            <span id="detalle-fecha-entrega"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detalle-box">
                            <small>Producto</small>
                            <span id="detalle-producto"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detalle-box">
                            <small>Localidad origen</small>
                            <span id="detalle-origen"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detalle-box">
                            <small>Localidad destino</small>
                            <span id="detalle-destino"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detalle-box">
                            <small>Cantidad</small>
                            <span id="detalle-cantidad"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detalle-box">
                            <small>Unidad</small>
                            <span id="detalle-unidad"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detalle-box">
                            <small>Observaciones</small>
                            <span id="detalle-observaciones"></span>
                        </div>
                    </div>


                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- SCRIPT DEL FORMULARIO -->
    <script src="/assets/js/pedidos.js"></script>
</body>
</html>