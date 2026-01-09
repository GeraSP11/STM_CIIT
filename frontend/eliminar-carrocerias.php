<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Carrocerías';
$seccion = 'Eliminar Carrocerías';
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
        /* Mismos estilos consistentes con eliminar-localidades.php */
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
            width: 290px;
            padding: 8px;
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

        .btn-custom:hover { background-color: #3b0d20; }

        .additional-field { display: none; }

        .warning-text {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }

        /* Ajustes para la visualización de resultados */
        #resultadosBusqueda label { font-weight: bold; font-size: 1.05rem; }
    </style>
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb" style="padding-left: 15px;">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $seccion; ?></li>
        </ol>
    </nav>

    <h2 class="text-center mt-2" style="color:#4a1026;"><?php echo $seccion; ?></h2>

    <div id="filtroELiminar" class="form-container">
        <form id="formConsulta">
            <div class="form-label-box">Filtro de búsqueda:</div>
            <select id="filtroBusqueda" class="form-control-custom">
                <option value="">Seleccione un filtro</option>
                <option value="id">ID de la carrocería</option>
                <option value="matricula">Matrícula / NIV</option>
            </select>

            <div id="campoId" class="additional-field">
                <div class="form-label-box">ID de la carrocería:</div>
                <input type="text" class="form-control-custom" id="inputId" name="inputId" placeholder="Ingrese el ID numérico" pattern="^[0-9]+$">
            </div>

            <div id="campoMatricula" class="additional-field">
                <div class="form-label-box">Matrícula:</div>
                <input type="text" class="form-control-custom" id="inputMatricula" name="inputMatricula" placeholder="Ingrese la matrícula">
            </div>

            <div class="d-flex justify-content-center">
                <button id="btnConsultar" type="submit" class="btn btn-custom">Buscar para eliminar</button>
            </div>
        </form>
    </div>

    <div id="resultadosBusqueda" class="form-container" style="display:none; margin-top:15px; padding:25px; width:90%; min-width:350px;">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>ID Carrocería</label>
                    <input type="text" id="res_id" class="form-control-custom" readonly>
                </div>
                <div class="col-md-4">
                    <label>Matrícula</label>
                    <input type="text" id="res_matricula" class="form-control-custom" readonly>
                </div>
                <div class="col-md-4">
                    <label>Modalidad</label>
                    <input type="text" id="res_modalidad" class="form-control-custom" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Tipo</label>
                    <input type="text" id="res_tipo" class="form-control-custom" readonly>
                </div>
                <div class="col-md-4">
                    <label>Estatus</label>
                    <input type="text" id="res_estatus" class="form-control-custom" readonly>
                </div>
                <div class="col-md-4">
                    <label>Responsable</label>
                    <input type="text" id="res_responsable" class="form-control-custom" readonly>
                </div>
            </div>

            <div class="d-flex justify-content-start mb-3">
                <button id="btnEliminar" class="btn-custom me-3">Confirmar Eliminación</button>
                <button id="btnCancelar" class="btn-custom" style="background-color:#6c757d;">Cancelar</button>
            </div>

            <div class="warning-text">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Atención:</strong> Esta acción eliminará permanentemente la carrocería del inventario. Asegúrese de que no esté asignada a un viaje activo.
            </div>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/js/alertas.js"></script>
    <script src="/assets/js/carrocerias.js"></script>

</body>
</html>