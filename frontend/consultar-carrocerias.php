<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Carrocerías';
$seccion = 'Consultar Carrocerías';
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
        .content-area { background: #f5f5f5; padding: 40px; margin: 0 auto; width: 85%; min-height: 450px; border-radius: 5px; }
        .section-title { text-align: center; font-size: 24px; color: #4a1026; margin-bottom: 30px; }
        .btn-add { padding: 10px 18px; background: white; border: 1px solid #ccc; border-radius: 4px; margin-left: 8px; cursor: pointer; }
        .btn-custom { background-color: #4a1026; color: white; padding: 12px 35px; border: none; border-radius: 4px; }
    </style>
</head>
<body>
    <?php include('includes/header-dinamico.php'); ?>

    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb" style="padding-left: 15px;">
            <li class="breadcrumb-item"><a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a></li>
            <li class="breadcrumb-item active"><?php echo $seccion; ?></li>
        </ol>
    </nav>

    <div class="content-area shadow">
        <div class="section-title"><?php echo $seccion; ?></div>

        <div id="formContainer">
            <form id="formConsultarCarrocerias" class="text-center">
                <select id="selectFiltro" class="form-select d-inline-block" style="width: 55%;">
                    <option value="">Selecciona un filtro</option>
                    <option value="matricula">Matrícula</option>
                    <option value="modalidad_carroceria">Modalidad</option>
                    <option value="tipo_carroceria">Tipo de Carrocería</option>
                    <option value="estatus_carroceria">Estatus</option>
                </select>

                <button id="btnAddFiltro" type="button" class="btn-add"><i class="fas fa-plus"></i></button>

                <div id="filtrosContainer" class="mt-4"></div>

                <div id="contenedorConsultar" class="justify-content-center mt-4" style="display:none;">
                    <button id="btnConsultar" type="submit" class="btn btn-custom">Consultar</button>
                </div>
            </form>
        </div>

        <div id="tablaResultados" class="mt-4" style="display:none;">
            <table id="tablaCarrocerias" class="table table-bordered bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Matrícula</th>
                        <th>Modalidad</th>
                        <th>Tipo</th>
                        <th>Estatus</th>
                        <th>Localidad</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="d-flex justify-content-center">
                <button id="btnVolver" class="btn btn-custom">Volver</button>
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