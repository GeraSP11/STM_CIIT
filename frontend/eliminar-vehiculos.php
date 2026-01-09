<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Vehículos';
$seccion = 'Eliminar Vehículos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .form-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            width: 50%;
            min-width: 400px;
            margin: 40px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label-box {
            background-color: #4a1026;
            color: white;
            width: fit-content;
            padding: 5px 15px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px;
            margin-bottom: 25px;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: 15px;
        }

        .btn-custom {
            background-color: #4a1026;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
        }

        .additional-field {
            display: none;
        }

        .warning-text {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        /* Estilos compactos para resultados */
        #resultadosBusqueda .form-control-custom {
            height: 38px;
            padding: 6px 10px;
            background-color: #e9ecef;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

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

    <h2><?php echo $seccion; ?></h2>

    <div id="filtroEliminar" class="form-container">
        <form id="formConsultaEliminar">
            <div class="form-label-box">Filtro de búsqueda:</div>
            <select id="filtroBusqueda" class="form-control-custom">
                <option value="">Seleccione un filtro</option>
                <option value="id">ID de Sistema</option>
                <option value="placas">Número de Placas</option>
            </select>

            <div id="campoId" class="additional-field">
                <div class="form-label-box">ID del Vehículo:</div>
                <input type="text" class="form-control-custom" id="inputId" placeholder="Ej. 15">
            </div>

            <div id="campoPlacas" class="additional-field">
                <div class="form-label-box">Placas:</div>
                <input type="text" class="form-control-custom" id="inputPlacas" placeholder="Ej. ABC-123" style="text-transform: uppercase;">
            </div>

            <div class="d-flex justify-content-center">
                <button id="btnConsultar" type="submit" class="btn btn-custom">Buscar Vehículo</button>
            </div>
        </form>
    </div>

    <div id="resultadosBusqueda" class="form-container" style="display:none; width:80%; max-width:1000px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <label style="font-weight:bold;">ID</label>
                    <input type="text" id="res_id" class="form-control-custom" readonly>
                </div>
                <div class="col-md-3">
                    <label style="font-weight:bold;">Placas</label>
                    <input type="text" id="res_placas" class="form-control-custom" readonly>
                </div>
                <div class="col-md-3">
                    <label style="font-weight:bold;">Marca</label>
                    <input type="text" id="res_marca" class="form-control-custom" readonly>
                </div>
                <div class="col-md-3">
                    <label style="font-weight:bold;">Modelo</label>
                    <input type="text" id="res_modelo" class="form-control-custom" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label style="font-weight:bold;">Tipo de Vehículo</label>
                    <input type="text" id="res_tipo" class="form-control-custom" readonly>
                </div>
                <div class="col-md-6">
                    <label style="font-weight:bold;">Capacidad de Carga</label>
                    <input type="text" id="res_capacidad" class="form-control-custom" readonly>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-3">
                <button id="btnEliminar" class="btn btn-custom me-3" style="background-color:#d9534f;">Confirmar Eliminación</button>
                <button id="btnCancelar" class="btn btn-custom" style="background-color:#6c757d;">Cancelar</button>
            </div>

            <div class="warning-text text-center">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Cuidado:</strong> Eliminar este vehículo podría afectar los registros de carga y logística asociados. Esta acción no se puede deshacer.
            </div>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/js/alertas.js"></script>
    
    <script src="/assets/js/vehiculos.js"></script>
</body>
</html>