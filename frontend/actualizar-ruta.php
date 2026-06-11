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

        .page-title {
            text-align: center;
            color: var(--vino-dark);
            margin: 20px 0 30px;
            font-weight: 600;
        }

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

    <?php include('includes/header-dinamico.php'); ?>

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

    <h2 class="page-title">Actualización de Rutas</h2>

    <div class="container-fluid pb-5">

        <!-- SECCIÓN 1: BÚSQUEDA -->
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

                <!-- RESULTADOS (ocultos al inicio) -->
                <h6 class="mb-3 fw-bold d-none" id="label-resultados">Resultados obtenidos:</h6>

                <div class="tabla-rutas d-none" id="tabla-resultados-actualizar">
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

                <!-- BOTONES (ocultos al inicio) -->
                <div class="acciones d-none" id="acciones-busqueda">
                    <button class="btn btn-gris" id="btn-cancelar-busqueda" type="button">
                        Cancelar
                    </button>
                    <button class="btn btn-vino" id="btn-actualizar" type="button">
                        Actualizar
                    </button>
                </div>

            </div>
        </div>

        <!-- SECCIÓN 2: FORMULARIO EDICIÓN -->
        <div class="card card-custom mt-4 d-none" id="seccion-form-actualizar">

            <div class="card-header-custom">
                Actualización de Rutas
            </div>

            <div class="card-body">

                <form id="form-actualizar-ruta">

                    <div class="row g-3">

                        <!-- ID (siempre visible) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Identificador de ruta</label>
                            <input type="text" id="act-id-ruta" name="id_ruta" class="form-control" readonly>
                        </div>

                        <!-- Localidad origen (siempre visible) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Localidad origen</label>
                            <select id="act-localidad-origen" name="localidad_origen" class="form-select">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>

                        <!-- Localidad destino (siempre visible) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Localidad destino</label>
                            <select id="act-localidad-destino" name="localidad_destino" class="form-select">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>

                        <!-- Modalidad (siempre visible) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Modalidad</label>
                            <select id="act-modalidad" name="modalidad_ruta" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="Carretera">Carretera</option>
                                <option value="Ferroviaria">Ferroviaria</option>
                                <option value="Marítima">Marítima</option>
                                <option value="Aérea">Aérea</option>
                            </select>
                        </div>

                        <!-- Tipo de ruta: solo Carretera -->
                        <div class="col-md-4 campo-modal d-none" data-modalidad="Carretera">
                            <label class="form-label fw-semibold">Tipo de ruta</label>
                            <select id="act-tipo-ruta" name="tipo_ruta" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="ET">ET – Sin restricción de peso</option>
                                <option value="A">A – Sin restricción de peso</option>
                                <option value="B">B – Máx. 38 ton</option>
                                <option value="C">C – Máx. 25.5 ton</option>
                            </select>
                        </div>

                        <!-- Distancia: Carretera, Ferroviaria, Marítima, Aérea -->
                        <div class="col-md-4 campo-modal d-none" data-modalidad="Carretera Ferroviaria Marítima Aérea">
                            <label class="form-label fw-semibold">Distancia</label>
                            <div class="input-group">
                                <input type="number" id="act-distancia" name="distancia"
                                    class="form-control" placeholder="0" min="0" step="0.01">
                                <span class="input-group-text">km</span>
                            </div>
                        </div>

                        <!-- Peso soportado: Carretera, Ferroviaria, Marítima -->
                        <div class="col-md-4 campo-modal d-none" data-modalidad="Carretera Ferroviaria Marítima">
                            <label class="form-label fw-semibold">Peso soportado (ton)</label>
                            <input type="number" id="act-peso-soportado" name="peso_soportado"
                                class="form-control" placeholder="0" min="0" step="0.01">
                        </div>

                        <!-- Capacidad TEUs: solo Marítima -->
                        <div class="col-md-4 campo-modal d-none" data-modalidad="Marítima">
                            <label class="form-label fw-semibold">Capacidad (TEUs)</label>
                            <input type="number" id="act-teus" name="teus"
                                class="form-control" placeholder="0" min="0">
                        </div>

                        <!-- Carga máxima: solo Aérea -->
                        <div class="col-md-4 campo-modal d-none" data-modalidad="Aérea">
                            <label class="form-label fw-semibold">Carga máxima (kg)</label>
                            <input type="number" id="act-carga-max" name="carga_max"
                                class="form-control" placeholder="0" min="0">
                        </div>

                        <!-- Descripción: siempre visible -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea id="act-descripcion" name="descripcion"
                                class="form-control" rows="3" maxlength="200"
                                placeholder="Descripción opcional de la ruta..."></textarea>
                        </div>

                    </div>

                    <div class="acciones mt-4">
                        <button type="button" class="btn btn-gris" id="btn-cancelar-form-actualizar">
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