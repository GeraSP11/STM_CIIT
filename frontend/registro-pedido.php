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
            width: 100%;
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

        .btn-custom {
            background-color: #6A0025;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #50001c;
        }

        .contenedor-productos {
            max-width: 1000px;
            margin: 2rem auto;
            /* centra horizontalmente */
        }


        /* ===== BUSCADOR ===== */
        .buscador-productos {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .buscador-productos label {
            background-color: #541C33;
            color: #fff;
            padding: 6px 20px;
            border-radius: 4px 0 0 4px;
            font-weight: bold;
        }

        .buscador-productos input {
            border-radius: 0 4px 4px 0;
            height: 32px;
        }

        /* ===== TEXTO AYUDA ===== */
        .texto-ayuda {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        /* ===== TABLA ===== */
        .tabla-productos {
            border: 1px solid #ccc;
            border-radius: 4px;
            overflow: hidden;
        }

        .tabla-productos thead {
            background-color: #5b1d3b;
            color: #fff;
        }

        .tabla-productos th {
            background-color: #541C33;
            color: #fff;

            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .tabla-productos td {
            height: 30px;
            vertical-align: middle;
        }

        /* Checkbox circular */
        .tabla-productos input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #5b1d3b;
            border-radius: 50%;
            cursor: pointer;
            position: relative;
        }

        .tabla-productos input[type="checkbox"]:checked::before {
            content: '';
            width: 8px;
            height: 8px;
            background-color: #5b1d3b;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
        }


        /* Scroll interno */
        .tabla-scroll {
            max-height: 230px;
            overflow-y: auto;
        }

        /* ===== BOTONES ===== */
        .btn-vino {
            background-color: #6A0025;
            color: white;
            padding: 10px 30px;
            border-radius: 4px;
            border: none;
        }

        .btn-vino:hover {
            background-color: #50001c;
        }

        .btn-gris {
            background-color: #9e9e9e;
            color: white;
            padding: 10px 30px;
            border-radius: 4px;
            border: none;
        }

        .btn-gris:hover {
            background-color: #7f7f7f;
        }

        .btn-activo {
            background-color: #6A0025 !important;
            color: #fff;
            cursor: pointer;
        }

        .btn-activo:hover {
            background-color: #50001c !important;
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



    <!-- VISTA 1: REGISTRAR PEDIDO -->
    <div id="vista-registro">
        <!-- Título de sección -->
        <h2><?php echo $seccion; ?></h2>
        <div class="container-fluid d-flex  justify-content-center align-items-center">
            <form id="formRegistroProductos" method="POST">
                <input type="hidden" name="action" value="registrar">
                <div class="card captura-card">
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
                                <select class="form-control" id="localidad-origen">
                                    <option value="">Localidad Origen</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="destino" class="form-label">Localidad destino:</label>
                                <select class="form-control" id="localidad-destino">
                                    <option value="">Localidad Destino</option>
                                </select>
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

                        <!-- Tabla de productos agregados -->
                        <div class="mt-4" id="contenedorTablaPedido" style="display:none;">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width:120px;">Cantidad</th>
                                        <th style="width:120px;">Unidad</th>
                                        <th>Observaciones</th>
                                        <th style="width:40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaPedido">
                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
                <!-- Botones -->
                <div class="text-center mt-4" id="botonesRegistro" style="display:none;">

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-custom me-2">Registrar</button>
                        <button type="reset" class="btn btn-custom me-2">Cancelar</button>
                        <button type="reset" class="btn btn-custom">Limpiar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- VISTA 2: PRODUCTOS -->
    <div id="vista-productos" style="display:none;">

        <div class="container-fluid">
            <div class="contenedor-productos">

                <!-- Buscador -->
                <div class="buscador-productos">
                    <label>Productos</label>
                    <input
                        type="text"
                        id="buscarProducto"
                        class="form-control"
                        placeholder="Escribe el nombre del producto">
                </div>

                <div class="texto-ayuda">
                    *Seleccione los productos que desee agregar al pedido.
                </div>

                <!-- Tabla -->
                <div class="tabla-productos">
                    <div class="tabla-scroll">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;"></th>
                                    <th>Nombre del producto</th>
                                    <th>Peso</th>
                                    <th>Localidad</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos">
                                <!-- Filas ejemplo -->
                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="producto">
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <!-- Dinámicas -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botones -->
                <div class="text-center mt-4">
                    <button
                        type="button"
                        class="btn-vino me-2"
                        id="btnRegresar">
                        Regresar
                    </button>

                    <button
                        type="button"
                        class="btn-gris"
                        id="btnAgregarProductos"
                        disabled>
                        Agregar
                    </button>
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

    <!-- CRIPT DEL FORMULARIO -->
    <script src="/assets/js/pedidos.js"></script>


</body>

</html>