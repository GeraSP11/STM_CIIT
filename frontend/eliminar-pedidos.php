<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Jefe de Almacén"]);
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Pedidos';
$seccion = 'Eliminar Pedidos';
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
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .contenedor-principal {
            background-color: white;
            border-radius: 8px;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .titulo-consulta {
            color: #8b4513;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .filtro-titulo {
            background-color: #4d2132;
            color: white;
            padding: 10px 20px;
            display: inline-block;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .form-control {
            border: 2px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .btn-buscar {
            background-color: #4d2132;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            display: block;
            margin: 30px auto 0;
        }

        .btn-buscar:hover {
            background-color: #6b2d42;
            color: white;
        }

        .btn-buscar:disabled {
            background-color: #999;
            cursor: not-allowed;
        }

        .resultados-header {
            background-color: #6b2d42;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .detalle-pedido {
            background-color: white;
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
        }

        .fila-detalle {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .fila-detalle-2cols {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .fila-detalle-4cols {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .campo-detalle {
            display: flex;
            flex-direction: column;
        }

        .campo-detalle label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .campo-detalle .texto-info {
            color: #666;
            font-size: 14px;
        }

        .advertencia-container {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
        }

        .advertencia-texto {
            color: #856404;
            font-weight: bold;
            margin: 0;
        }

        .botones-accion {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .btn-eliminar {
            background-color: #4d2132;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-eliminar:hover {
            background-color: #6b2d42;
        }

        .btn-cancelar {
            background-color: #6c757d;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-cancelar:hover {
            background-color: #5a6268;
        }

        .breadcrumb {
            background-color: transparent;
            padding-left: 15px;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: ">";
        }

        .mensaje-vacio {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
            border-width: 0.25em;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .alert {
            margin: 20px auto;
            max-width: 800px;
        }
    </style>
</head>

<body>
    <!-- Header dinámico -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb" id="breadcrumb-nav">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Eliminar pedidos
            </li>
        </ol>
    </nav>

    <!-- Vista 1: Formulario de Búsqueda -->
    <div id="vista-busqueda-eliminar" class="contenedor-principal">
        <h2 class="titulo-consulta">Eliminar pedidos</h2>

        <div class="filtro-titulo">Filtro de búsqueda: *</div>

        <form id="form-busqueda-eliminar">
            <input type="text" class="form-control" id="clave-pedido" placeholder="Clave del Pedido">

            <button type="submit" class="btn-buscar" id="btn-buscar-eliminar">Buscar</button>
        </form>
    </div>

    <!-- Vista 2: Resultados de Búsqueda -->
    <div id="vista-resultados-eliminar" class="container my-4 p-4 bg-white rounded shadow" style="display: n; max-width: 1200px;">
        <div class="mb-3 p-3 rounded" style="background-color: #4d2132; color: #fff;">
            <h5 class="mb-0">Resultados obtenidos:</h5>
        </div>


        <div class="detalle-pedido">
            <!-- Primera fila: 5 columnas -->
            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Clave:</label>
                    <div class="form-control-plaintext" id="resultado-clave"></div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Estatus:</label>
                    <div class="form-control-plaintext" id="resultado-estatus"></div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Fecha de solicitud:</label>
                    <div class="form-control-plaintext" id="resultado-fecha-solicitud"></div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Fecha de entrega:</label>
                    <div class="form-control-plaintext" id="resultado-fecha-entrega"></div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Producto:</label>
                    <div class="form-control-plaintext mb-2" id="resultado-producto"></div>
                    <select class="form-select" id="resultado-producto-select">
                        <!-- Opciones se llenarán con JS -->
                    </select>
                </div>
            </div>

            <!-- Segunda fila: 2 columnas -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Localidad origen:</label>
                    <div class="form-control-plaintext" id="resultado-localidad-origen"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Localidad destino:</label>
                    <div class="form-control-plaintext" id="resultado-localidad-destino"></div>
                </div>
            </div>

            <!-- Tercera fila: 3 columnas -->
            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Cantidad:</label>
                    <div class="form-control-plaintext" id="resultado-cantidad"></div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Unidad:</label>
                    <div class="form-control-plaintext" id="resultado-unidad"></div>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-bold">Observaciones:</label>
                    <div class="form-control-plaintext" id="resultado-observaciones"></div>
                </div>
            </div>
        </div>

        <div class="alert alert-warning mt-4 text-center">
            <strong>Advertencia:</strong> Esta acción es irreversible. El pedido será eliminado del sistema de forma permanente.
        </div>

        <div class="d-flex justify-content-center gap-3 mt-3">
            <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar">Eliminar</button>
            <button type="button" class="btn btn-secondary" id="btn-cancelar-eliminar">Cancelar</button>
        </div>
    </div>



    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <!-- Scripts -->
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