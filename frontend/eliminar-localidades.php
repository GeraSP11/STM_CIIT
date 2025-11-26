<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Localidades';
$seccion = 'Eliminar Localidades';
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
        /* Estilo general para el formulario */
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

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
        }

        /* Hacer el campo adicional oculto por defecto */
        .additional-field {
            display: none;
        }

        /* Estilos para los resultados de la búsqueda */
        .result-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .result-box {
            background-color: #4a1026;
            color: white;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .result-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .result-details div {
            width: 45%;
        }

        .result-details input {
            width: 100%;
            padding: 8px;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .warning-text {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }

        /* ======== RESULTADOS SUPER COMPACTO EN 2 FILAS ======== */

        /* Contenedor más pequeño */
        #resultadosBusqueda.form-container {
            padding: 10px 15px;
            width: 90%;
            max-width: 1100px;
            margin-top: 10px;
        }

        /* Ocultar título */
        #resultadosBusqueda h4 {
            display: none;
        }

        /* Etiquetas más pequeñas */
        #resultadosBusqueda .form-label-box {
            background-color: #4a1026;
            padding: 3px 5px;
            margin-bottom: 3px;
            font-size: 0.7rem;
            border-radius: 3px;
            width: fit-content;
        }

        /* Inputs compactos */
        #resultadosBusqueda .form-control-custom {
            padding: 6px 8px;
            font-size: 0.75rem;
            height: 32px;
            margin-bottom: 5px;
        }

        /* Filas pequeñas */
        #resultadosBusqueda .row {
            margin-bottom: 8px;
        }

        /* Compactar columnas */
        #resultadosBusqueda .col-md-2,
        #resultadosBusqueda .col-md-3,
        #resultadosBusqueda .col-md-4,
        #resultadosBusqueda .col-md-5 {
            padding-right: 6px;
            padding-left: 6px;
        }

        /* Botones pequeños */
        #resultadosBusqueda .btn-custom {
            padding: 5px 15px;
            font-size: 0.75rem;
            border-radius: 4px;
        }

        #btnCancelar {
            background-color: #6c757d !important;
        }

        /* Advertencia compacta */
        #resultadosBusqueda .warning-text {
            padding: 5px 6px;
            margin-top: 8px;
            font-size: 0.7rem;
        }
    </style>
</head>

<body>

    <!-- Header dinámico -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
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

    <!-- Título -->
    <h2 class="text-center mt-2" style="color:#4a1026;"><?php echo $seccion; ?></h2>

    <!-- Div para la búsqueda de localidad -->
    <div id="filtroELiminar" class="form-container">
        <form id="formConsulta">
            <div class="form-label-box">Filtro de búsqueda:</div>
            <select id="filtroBusqueda" class="form-control-custom">
                <option value="">Seleccione un filtro</option>
                <option value="id">Identificador de la localidad</option>
                <option value="nombre_trabajo">Nombre del centro de trabajo</option>
                <option value="ubicacion">Ubicación georeferenciada</option>
            </select>

            <div id="campoId" class="additional-field">
                <div class="form-label-box">Identificador de la localidad:</div>
                <input type="text" class="form-control-custom" id="inputId" name="inputId"
                    placeholder="Ingrese el ID de la localidad" pattern="^[0-9]+$">
            </div>

            <div id="campoNombreTrabajo" class="additional-field">
                <div class="form-label-box">Nombre del centro de trabajo:</div>
                <input type="text" class="form-control-custom" id="inputNombreTrabajo" name="inputNombreTrabajo"
                    placeholder="Ingrese el nombre del centro de trabajo" maxlength="100" min="10"
                    pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ0-9\s\-]+$">
            </div>


            <div id="campoUbicacion" class="additional-field">
                <div class="form-label-box">Ubicación georeferenciada:</div>
                <input type="text" class="form-control-custom" id="inputUbicacion" name="inputUbicacion"
                    placeholder="Ej. 19.4326, -99.1332" pattern="^-?\d{1,3}\.\d+,\s*-?\d{1,3}\.\d+$"
                    title="Formato correcto: latitud,longitud (ej. 19.4326, -99.1332)">
            </div>

            <div class="d-flex justify-content-center">
                <button id="btnConsultar" type="submit" class="btn btn-custom">Consultar</button>
            </div>
        </form>
    </div>

    <!-- Div con los resultados obtenidos -->
    <!-- Div con los resultados obtenidos -->
    <!-- Div con los resultados obtenidos -->
    <!-- Div con los resultados obtenidos -->
    <!-- Div con los resultados obtenidos -->
    <div id="resultadosBusqueda" class="form-container"
        style="display:none; margin-top:15px; padding:25px; width:90%; min-width:350px;">

        <div class="container-fluid">

            <!-- Fila 1 -->
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <label class="d-block w-100 mb-1" style="font-weight:bold; font-size:1.05rem;">ID de la
                        Localidad</label>
                    <input type="text" id="res_id" class="form-control-custom w-100 p-3" readonly
                        style="font-size:1.05rem;">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="d-block w-100 mb-1" style="font-weight:bold; font-size:1.05rem;">Nombre del Centro de
                        Trabajo</label>
                    <input type="text" id="res_nombre" class="form-control-custom w-100 p-3" readonly
                        style="font-size:1.05rem;">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="d-block w-100 mb-1" style="font-weight:bold; font-size:1.05rem;">Ubicación
                        Georreferenciada</label>
                    <input type="text" id="res_ubicacion" class="form-control-custom w-100 p-3" readonly
                        style="font-size:1.05rem;">
                </div>
            </div>

            <!-- Fila 2 -->
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <label class="d-block w-100 mb-1" style="font-weight:bold; font-size:1.05rem;">Población</label>
                    <input type="text" id="res_poblacion" class="form-control-custom w-100 p-3" readonly
                        style="font-size:1.05rem;">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="d-block w-100 mb-1" style="font-weight:bold; font-size:1.05rem;">Localidad</label>
                    <input type="text" id="res_localidad" class="form-control-custom w-100 p-3" readonly
                        style="font-size:1.05rem;">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="d-block w-100 mb-1" style="font-weight:bold; font-size:1.05rem;">Estado</label>
                    <input type="text" id="res_estado" class="form-control-custom w-100 p-3" readonly
                        style="font-size:1.05rem;">
                </div>
            </div>

            <!-- Fila 3 -->
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <label class="d-block w-100 mb-1" style="font-weight:bold; font-size:1.05rem;">Tipo de
                        Instalación</label>
                    <input type="text" id="res_tipo_instalacion" class="form-control-custom w-100 p-3" readonly
                        style="font-size:1.05rem;">
                </div>
            </div>

            <!-- Botones acción -->
            <div class="d-flex justify-content-start mb-3">
                <button id="btnEliminar" class="btn-custom me-3 px-3 py-2" style="font-size:1rem;">Eliminar</button>
                <button id="btnCancelar" class="btn-custom px-3 py-2"
                    style="background-color:#6c757d; font-size:1rem;">Cancelar</button>
            </div>

            <!-- Advertencia -->
            <div class="warning-text p-2" style="font-size:0.95rem;">
                Advertencia: Esta acción es irreversible. La localidad será eliminada del sistema de forma permanente.
            </div>

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
    <script src="/assets/js/localidades.js"></script>

</body>

</html>