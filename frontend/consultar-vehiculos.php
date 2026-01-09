<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Vehículos';
$seccion = 'Consultar Vehículos';
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
        .content-area {
            background: #f5f5f5;
            padding: 40px;
            margin: 0 auto;
            width: 75%;
            min-height: 450px;
            border-radius: 5px;
        }

        .btn-add {
            padding: 10px 18px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-left: 8px;
            cursor: pointer;
            font-size: 18px;
        }

        .btn-add:hover {
            background: #eaeaea;
        }

        .section-title {
            text-align: center;
            font-size: 24px;
            color: #4a1026;
            margin-bottom: 30px;
        }

        .filter-row {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }

        .delete-btn {
            border: none;
            background: rgb(240, 79, 79);
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: red;
        }

        .btn-custom {
            background-color: #4a1026;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }
        
        /* Estilo para que la tabla no se vea tan apretada */
        #tablaVehiculos th {
            background-color: #4a1026;
            color: white;
            text-align: center;
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

    <div class="content-area shadow">

        <div class="section-title">
            <?php echo $seccion; ?>
        </div>

        <div id="formContainer">
            <form id="formConsultarVehiculos" class="text-center">

                <select id="selectFiltro" class="form-select d-inline-block" style="width: 55%;">
                    <option value="">Selecciona un filtro</option>
                    <option value="placas">Placas</option>
                    <option value="marca">Marca</option>
                    <option value="modelo">Modelo (Año)</option>
                    <option value="tipo_vehiculo">Tipo de Vehículo</option>
                </select>

                <button id="btnAddFiltro" type="button" class="btn-add">
                    <i class="fas fa-plus"></i>
                </button>

                <div id="filtrosContainer" class="mt-4"></div>

                <div id="contenedorConsultar" class="justify-content-center mt-4" style="display:none;">
                    <button id="btnConsultar" type="submit" class="btn btn-custom">Consultar</button>
                </div>

            </form>
        </div>

        <div id="tablaResultados" class="mt-4" style="display:none;">
            <div class="table-responsive">
                <table id="tablaVehiculos" class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Placas</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Capacidad (kg)</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                <button id="btnVolver" class="btn btn-custom">Nueva Consulta</button>
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