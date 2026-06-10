<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Jefe de Almacén"]);
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Rutas';
$seccion = 'Registro de Rutas';
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
        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .ruta-card {
            background-color: #f7f7f7;
            border-radius: 6px;
            padding: 40px 50px;
            max-width: 900px;
            margin: 0 auto;
        }

        label {
            font-weight: 600;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            height: 42px;
        }

        .form-control[readonly] {
            background-color: #e5e5e5;
        }

        /* Botones */
        .btn-maroon {
            background-color: #5a1e2d;
            color: #fff;
            padding: 8px 35px;
            border-radius: 5px;
            border: none;
        }

        .btn-maroon:hover {
            background-color: #471624;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 30px;
        }

        /* Tipos de ruta según modalidad */
        #grupo-tipo-ruta {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Header dinámico -->
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

    <!-- Contenedor del formulario -->
    <div class="ruta-card">

        <form id="form-registrar-ruta" novalidate>

            <!-- Fila 1: ID, Localidad Origen, Localidad Destino -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="reg-id-ruta">
                        Identificador de ruta:
                        <span class="text-danger">*</span>
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Identificador único de la ruta. Se genera automáticamente."></i>
                    </label>
                    <input type="text"
                           id="reg-id-ruta"
                           name="id_ruta"
                           class="form-control"
                           readonly
                           placeholder="Se asignará automáticamente"
                           title="El ID se genera automáticamente al guardar">
                </div>

                <div class="col-md-4">
                    <label for="reg-localidad-origen">
                        Localidad origen:
                        <span class="text-danger">*</span>
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Seleccione la localidad donde inicia la ruta."></i>
                    </label>
                    <select id="reg-localidad-origen" name="localidad_origen" class="form-select">
                        <option value="" selected disabled>Cargando localidades...</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="reg-localidad-destino">
                        Localidad destino:
                        <span class="text-danger">*</span>
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Seleccione la localidad de llegada. Debe ser distinta al origen."></i>
                    </label>
                    <select id="reg-localidad-destino" name="localidad_destino" class="form-select">
                        <option value="" selected disabled>Cargando localidades...</option>
                    </select>
                </div>
            </div>

            <!-- Fila 2: Modalidad, Tipo de Ruta, Distancia, Peso Soportado -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="reg-modalidad">
                        Modalidad:
                        <span class="text-danger">*</span>
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Tipo de infraestructura de la ruta: Carretera, Ferroviaria, Marítima o Aérea."></i>
                    </label>
                    <select id="reg-modalidad" name="modalidad_ruta" class="form-select">
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="Carretera">Carretera</option>
                        <option value="Ferroviaria">Ferroviaria</option>
                        <option value="Marítima">Marítima</option>
                        <option value="Aérea">Aérea</option>
                    </select>
                </div>

                <div class="col-md-3" id="grupo-tipo-ruta">
                    <label for="reg-tipo-ruta">
                        Tipo de ruta:
                        <span class="text-danger">*</span>
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Clasificación de la ruta según normativa (ET, A, B, C). Solo aplica a modalidad Carretera."></i>
                    </label>
                    <select id="reg-tipo-ruta" name="tipo_ruta" class="form-select">
                        <option value="" selected disabled>Seleccione tipo...</option>
                        <option value="ET">ET – Sin restricción de peso</option>
                        <option value="A">A – Sin restricción de peso</option>
                        <option value="B">B – Máx. 38 ton</option>
                        <option value="C">C – Máx. 25.5 ton</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="reg-distancia">
                        Distancia (km):
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Distancia total de la ruta expresada en kilómetros. Debe ser mayor a 0."></i>
                    </label>
                    <input type="number"
                           id="reg-distancia"
                           name="distancia"
                           class="form-control"
                           min="0.01"
                           step="0.01"
                           placeholder="Ej. 350.5">
                </div>

                <div class="col-md-3">
                    <label for="reg-peso-soportado">
                        Peso soportado (ton):
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Peso máximo permitido en toneladas. Debe ser mayor a 0 y acorde a la modalidad."></i>
                    </label>
                    <input type="number"
                           id="reg-peso-soportado"
                           name="peso_soportado"
                           class="form-control"
                           min="0.01"
                           step="0.01"
                           placeholder="Ej. 38">
                </div>
            </div>

            <!-- Fila 3: Descripción -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <label for="reg-descripcion">
                        Descripción:
                        <i class="fas fa-circle-info text-secondary ms-1"
                           data-bs-toggle="tooltip"
                           title="Información adicional sobre la ruta (máximo 200 caracteres)."></i>
                    </label>
                    <textarea id="reg-descripcion"
                              name="descripcion"
                              class="form-control"
                              rows="3"
                              maxlength="200"
                              placeholder="Descripción opcional de la ruta..."></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="btn-container">
                <button type="submit" class="btn btn-maroon" id="btn-guardar-ruta">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
                <button type="button" class="btn btn-maroon" id="btn-limpiar-ruta">
                    <i class="fas fa-eraser me-1"></i> Limpiar
                </button>
                <button type="button" class="btn btn-maroon" onclick="window.location.href='/dashboard.php'">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
            </div>

        </form>

    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script de rutas -->
    <script src="/assets/js/rutas.js"></script>

</body>

</html>
