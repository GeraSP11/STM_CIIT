<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Localidades';
$seccion = 'Consultar Localidades';
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
            width: 70%;
            min-height: 450px;
            border-radius: 5px;
        }

        .footer-line {
            margin-top: 80px;
            height: 8px;
            background: #4a1026;
            width: 100%;
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

        /* ESTILOS NECESARIOS PARA LOS FILTROS — NO MODIFICAN LOS TUYOS */
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

        <!-- Título de sección -->
        <div class="section-title">
            <?php echo $seccion; ?>
        </div>

        <!-- Contenedor principal del formulario -->
        <div id="formContainer">
            <form id="formConsultarLocalidades" class="text-center">

                <select id="selectFiltro" class="form-select d-inline-block" style="width: 55%;">
                    <option value="">Selecciona un filtro</option>
                    <option value="nombre_centro_trabajo">Nombre del centro de trabajo</option>
                    <option value="localidad">Localidad</option>
                    <option value="poblacion">Población</option>
                    <option value="estado">Estado</option>
                </select>

                <button id="btnAddFiltro" type="button" class="btn-add">
                    <i class="fas fa-plus"></i>
                </button>

                <!-- Contenedor de filtros dinámicos -->
                <div id="filtrosContainer" class="mt-4"></div>

                <!-- Botón CONSULTAR (se muestra dinámicamente) -->
                <div id="contenedorConsultar" class="justify-content-center mt-4" style="display:none;">
                    <button id="btnConsultar" type="submit" class="btn btn-custom">Consultar</button>
                </div>

            </form>
        </div>

        <!-- Contenedor de resultados, separado del formulario -->
        <div id="tablaResultados" class="mt-4" style="display:none;">
            <table id="tablaLocalidades" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>ubicacion</th>
                        <th>poblacion</th>
                        <th>estado</th>
                        <th>Tipo de instalacion</th>
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

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- CRIPT DEL FORMULARIO -->
    <script src="/assets/js/localidades.js"></script>

</body>

</html>