<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Jefe de Almacén"]);
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Rutas';
$seccion = 'Actualizar Rutas';
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
        :root {
            --vino: #5B1D3B;
            --vino-dark: #4A1026;
            --gris: #6c757d;
            --gris-claro: #f8f9fa;
            --borde: #d9d9d9;
        }

        body {
            background: #ffffff;
            font-family: "Segoe UI", sans-serif;
        }

        /* TITULO */
        .page-title {
            text-align: center;
            color: var(--vino-dark);
            margin: 20px 0 30px;
            font-weight: 600;
        }

        /* CARD */
        .card-custom {
            max-width: 1100px;
            margin: auto;
            border: 1px solid var(--borde);
        }

        .card-header-custom {
            background: var(--vino);
            color: white;
            padding: 12px 20px;
            font-weight: bold;
        }

        /* BUSCADOR */
        .buscador-rutas {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .buscador-rutas label {
            background: var(--vino);
            color: white;
            padding: 8px 20px;
            border-radius: 4px 0 0 4px;
            min-width: 180px;
            font-weight: 600;
            cursor: pointer;
        }

        .buscador-rutas .input-group {
            flex: 1;
        }

        .buscador-rutas input {
            border-radius: 0;
        }

        /* TABLA */
        .tabla-rutas {
            border: 1px solid var(--borde);
            border-radius: 4px;
            overflow: hidden;
        }

        .tabla-rutas thead {
            background: var(--vino);
            color: white;
        }

        .tabla-rutas th {
            text-align: center;
            vertical-align: middle;
        }

        .tabla-rutas td {
            vertical-align: middle;
        }

        .tabla-scroll {
            max-height: 300px;
            overflow-y: auto;
        }

        /* CHECKBOX ESTILO SISTEMA */
        .tabla-rutas input[type="checkbox"] {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid var(--vino);
            border-radius: 3px;
            cursor: pointer;
            position: relative;
            display: block;
            margin: auto;
        }

        .tabla-rutas input[type="checkbox"]:checked {
            background: var(--vino);
        }

        .tabla-rutas input[type="checkbox"]:checked::before {
            content: "✓";
            color: white;
            font-size: 11px;
            position: absolute;
            top: -1px;
            left: 2px;
        }

        .fila-seleccionada {
            background-color: #f5eef2 !important;
        }

        /* BOTONES */
        .acciones {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-vino {
            background: #6B1D2F;
            color: white;
            border: none;
            padding: 10px 35px;
        }

        .btn-vino:hover {
            background: #541729;
            color: white;
        }

        .btn-gris {
            background: #9a9a9a;
            color: white;
            border: none;
            padding: 10px 35px;
        }

        .btn-gris:hover {
            background: #7f7f7f;
            color: white;
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

    <!-- TITULO -->
    <h2 class="page-title">Actualización de Rutas</h2>

    <!-- CONTENIDO -->
    <div class="container-fluid pb-5">

        <!-- ==============================
             SECCIÓN 1: BÚSQUEDA
        =============================== -->
        <div class="card card-custom" id="seccion-busqueda-actualizar">

            <div class="card-header-custom">
                Actualización de rutas
            </div>

            <div class="card-body">

                <!-- BUSCADOR -->
                <div class="buscador-rutas">

                    <label for="input-id-ruta-actualizar">
                        Filtro de búsqueda
                    </label>

                    <div class="input-group">
                        <input
                            type="text"
                            id="input-id-ruta-actualizar"
                            class="form-control"
                            placeholder="Ingrese ID de ruta">
                        <button
                            class="btn btn-vino"
                            id="btn-filtro-actualizar"
                            type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                </div>

                <!-- RESULTADOS -->
                <h6 class="mb-3 fw-bold">Resultados obtenidos:</h6>

                <div class="tabla-rutas" id="tabla-resultados-actualizar">
                    <div class="tabla-scroll">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th width="60"></th>
                                    <th>ID Ruta</th>
                                    <th>Modalidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS renderiza aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="acciones">
                    <button class="btn btn-gris" id="btn-cancelar-busqueda" type="button">
                        Cancelar
                    </button>
                    <button class="btn btn-vino" id="btn-actualizar" type="button">
                        Actualizar
                    </button>
                </div>

            </div>
        </div>

        <!-- ==============================
             SECCIÓN 2: FORMULARIO EDICIÓN
        =============================== -->
        <div class="card card-custom mt-4 d-none" id="seccion-form-actualizar">

            <div class="card-header-custom">
                Actualización de Rutas
            </div>

            <div class="card-body">

                <form id="form-actualizar-ruta">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Identificador de ruta</label>
                            <input
                                type="text"
                                id="act-id-ruta"
                                name="id_ruta"
                                class="form-control"
                                readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Localidad origen</label>
                            <select id="act-localidad-origen" name="localidad_origen" class="form-select">
                                <option value="">Seleccione...</option>
                                <!-- Opciones dinámicas o estáticas según catálogo -->
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Localidad destino</label>
                            <select id="act-localidad-destino" name="localidad_destino" class="form-select">
                                <option value="">Seleccione...</option>
                                <!-- Opciones dinámicas o estáticas según catálogo -->
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Modalidad</label>
                            <select id="act-modalidad" name="modalidad" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="Carretera">Carretera</option>
                                <option value="Ferroviaria">Ferroviaria</option>
                                <option value="Marítima">Marítima</option>
                                <option value="Aérea">Aérea</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Distancia</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    id="act-distancia"
                                    name="distancia"
                                    class="form-control"
                                    placeholder="0"
                                    min="0">
                                <span class="input-group-text">km</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Peso soportado</label>
                            <input
                                type="number"
                                id="act-peso-soportado"
                                name="peso_soportado"
                                class="form-control"
                                placeholder="0"
                                min="0">
                        </div>

                    </div>

                    <!-- BOTONES -->
                    <div class="acciones mt-4">
                        <button
                            type="button"
                            class="btn btn-gris"
                            id="btn-cancelar-form-actualizar">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-vino">
                            Guardar
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/rutas.js"></script>
    <script src="/assets/js/alertas.js"></script>
</body>

</html>