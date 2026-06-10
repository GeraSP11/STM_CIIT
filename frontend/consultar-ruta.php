<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Jefe de Almacén"]);
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Rutas';
$seccion = 'Consultar Rutas';
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

        .card-container {
            max-width: 650px;
            margin: 0 auto;
            background: #f7f7f7;
            padding: 30px;
            border-radius: 6px;
        }

        .ruta-card {
            max-width: 950px;
            margin: 0 auto;
            background: #f7f7f7;
            padding: 40px;
            border-radius: 6px;
        }

        .tabla-card {
            max-width: 950px;
            margin: 0 auto;
            background: #f7f7f7;
            padding: 30px;
            border-radius: 6px;
        }

        .badge-filter {
            background-color: #5a1e2d;
            color: #fff;
            padding: 6px 15px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .form-control,
        .form-select {
            height: 42px;
        }

        .readonly-field {
            background-color: #e5e5e5;
        }

        .btn-maroon {
            background-color: #5a1e2d;
            color: #fff;
            padding: 8px 35px;
            border: none;
            border-radius: 5px;
        }

        .btn-maroon:hover {
            background-color: #471624;
            color: #fff;
        }

        .hidden {
            display: none;
        }

        .table thead {
            background-color: #5a1e2d;
            color: #fff;
        }

        .table tbody tr:hover {
            background-color: #f0e6e9;
            cursor: pointer;
        }

        .tr-seleccionada {
            background-color: #e0c8ce !important;
        }

        .label-campo {
            font-weight: 600;
            color: #4a1026;
            font-size: 13px;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2" style="padding-left: 15px; font-size: 18px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $seccion; ?></li>
        </ol>
    </nav>

    <h2><?php echo $seccion; ?></h2>

    <!-- ===================== SECCIÓN 1: FILTROS ===================== -->
    <div id="seccion-filtros" class="card-container">

        <span class="badge-filter">Filtro de búsqueda</span>

        <div class="mb-3">
            <label class="label-campo">Localidad Origen</label>
            <select id="sel-origen" class="form-select">
                <option value="">-- Seleccionar --</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="label-campo">Localidad Destino</label>
            <select id="sel-destino" class="form-select">
                <option value="">-- Seleccionar --</option>
            </select>
        </div>

        <div class="text-center">
            <button id="btn-consultar" class="btn btn-maroon">
                <i class="fas fa-search me-2"></i>Consultar
            </button>
        </div>

    </div>

    <!-- ===================== SECCIÓN 2: RESULTADOS ===================== -->
    <div id="seccion-resultados" class="tabla-card hidden mt-4">

        <span class="badge-filter">Resultados encontrados</span>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID Ruta</th>
                        <th>Localidad Origen</th>
                        <th>Localidad Destino</th>
                        <th>Modalidad</th>
                        <th>Tipo</th>
                        <th>Distancia (km)</th>
                    </tr>
                </thead>
                <tbody id="tbody-resultados">
                    <!-- Llenado dinámico -->
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button id="btn-regresar-filtros" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Regresar a filtros
            </button>
            <button id="btn-ver-detalle" class="btn btn-maroon">
                <i class="fas fa-eye me-2"></i>Ver detalle
            </button>
        </div>

    </div>

    <!-- ===================== SECCIÓN 3: DETALLE ===================== -->
    <div id="seccion-detalle" class="ruta-card hidden mt-4">

        <span class="badge-filter">Detalle de Ruta</span>

        <div class="row mb-4 mt-3">
            <div class="col-md-4 mb-3">
                <label class="label-campo">ID de Ruta</label>
                <input type="text" id="det-id-ruta" class="form-control readonly-field" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="label-campo">Localidad Origen</label>
                <input type="text" id="det-origen" class="form-control readonly-field" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="label-campo">Localidad Destino</label>
                <input type="text" id="det-destino" class="form-control readonly-field" readonly>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <label class="label-campo">Modalidad</label>
                <input type="text" id="det-modalidad" class="form-control readonly-field" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="label-campo">Tipo de Ruta</label>
                <input type="text" id="det-tipo" class="form-control readonly-field" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="label-campo">Distancia (km)</label>
                <input type="text" id="det-distancia" class="form-control readonly-field" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="label-campo">Peso Soportado (ton)</label>
                <input type="text" id="det-peso" class="form-control readonly-field" readonly>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <label class="label-campo">Descripción</label>
                <textarea id="det-descripcion" class="form-control readonly-field" rows="3" readonly></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-2">
            <button id="btn-regresar-resultados" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Regresar a resultados
            </button>
        </div>

    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets/js/rutas.js"></script>

</body>
</html>