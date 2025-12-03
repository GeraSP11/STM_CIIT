<?php

require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Localidades';
$seccion = 'Registro de Localidades';
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
        .form-container {
            background-color: #f8f9fa;
            padding: 40px;
            border-radius: 8px;
            width: 80%;
            min-width: 400px;
            margin: 40px auto;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .btn-custom {
            background-color: #4a1026;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 15px;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
            margin-bottom: 30px;
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
    <div class="form-container">
        <form id="formLocalidades">
            <!-- Fila 1 -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="nombre_centro" class="form-label">Nombre del centro de trabajo</label>
                    <input type="text" id="nombre_centro" name="nombre_centro" class="form-input" placeholder="Ej. PUBIS"
                        required maxlength="100" min="10" pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ0-9\s\-]+$"
                        title="Solo letras, números y espacios">
                </div>
                <div class="col-md-4">
                    <label for="ubicacionGeo" class="form-label">Ubicación Georreferenciada (Latitud, Longitud)</label>
                    <input type="text" id="ubicacionGeo" name="ubicacionGeo" class="form-input"
                        placeholder="Ej. 19.4326, -99.1332" required pattern="^-?\d{1,3}\.\d+,\s*-?\d{1,3}\.\d+$"
                        title="Formato correcto: latitud,longitud (ej. 19.4326, -99.1332)">
                </div>
                <div class="col-md-4">
                    <label for="poblacion" class="form-label">Población</label>
                    <input type="text" id="poblacion" name="poblacion" class="form-input"
                        placeholder="Ej. San Pedro Mixtepec" required maxlength="100"
                        pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ\s\-]+$" title="Solo letras y espacios">
                </div>
            </div>

            <!-- Fila 2 -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="localidad" class="form-label">Localidad</label>
                    <input type="text" id="localidad" name="localidad" class="form-input" placeholder="Ej. Centro"
                        required maxlength="100" pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ\s\-]+$"
                        title="Solo letras y espacios">
                </div>
                <div class="col-md-4">
                    <label for="estados" class="form-label">Estado</label>
                    <select id="estados" name="estado" class="form-select" required>
                        <option value="">Seleccione un estado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tipoInstalacion" class="form-label">Tipo de instalación</label>
                    <select id="tipoInstalacion" name="tipoInstalacion" class="form-select" required>
                        <option value="">Seleccione un tipo de instalación</option>
                        <option value="Centro Productivo">Centro Productivo</option>
                        <option value="Centro de Distribucion">Centro de Distribución</option>
                        <option value="PODEBI">PODEBI</option>
                        <option value="Almacen">Almacén</option>
                    </select>
                </div>
            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-center">
                <button class="btn btn-custom" type="submit">Guardar</button>
                <button class="btn btn-outline-secondary" type="reset">Cancelar</button>
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

    <script src="/assets/js/estados.js"></script>
    <script src="/assets/js/localidades.js"></script>
</body>

</html>