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


        /* Tarjetas */
        .card-container {
            max-width: 600px;
            margin: 0 auto;
            background: #f7f7f7;
            padding: 30px;
            border-radius: 6px;
        }

        .ruta-card {
            max-width: 900px;
            margin: 0 auto;
            background: #f7f7f7;
            padding: 40px;
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

        .readonly {
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
        }

        .or-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 15px 0;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #aaa;
        }

        .or-divider span {
            padding: 0 10px;
            font-weight: bold;
        }

        .hidden {
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

    <!-- ================= FILTROS ================= -->
    <div id="filtros" class="card-container">

        <span class="badge-filter">Filtro de búsqueda: *</span>

        <div class="mb-3">
            <input type="text" class="form-control" placeholder="Identificador de Ruta">
        </div>

        <div class="or-divider">
            <span>o</span>
        </div>

        <div class="mb-3">
            <input type="text" class="form-control" placeholder="Localidad Origen">
        </div>

        <div class="mb-4">
            <input type="text" class="form-control" placeholder="Localidad Destino">
        </div>

        <div class="text-center">
            <button class="btn btn-maroon" onclick="mostrarDetalle()">Consultar</button>
        </div>

    </div>

    <!-- ================= DETALLE ================= -->
    <div id="detalle" class="ruta-card hidden">

        <form>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label>Identificador de ruta:</label>
                    <input type="text" class="form-control readonly" readonly value="RT-001">
                </div>

                <div class="col-md-4">
                    <label>Localidad origen:</label>
                    <input type="text" class="form-control readonly" readonly value="Salina Cruz">
                </div>

                <div class="col-md-4">
                    <label>Localidad destino:</label>
                    <input type="text" class="form-control readonly" readonly value="Coatzacoalcos">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>Modalidad:</label>
                    <input type="text" class="form-control readonly" readonly value="Terrestre">
                </div>

                <div class="col-md-4">
                    <label>Distancia:</label>
                    <input type="text" class="form-control readonly" readonly value="308 km">
                </div>

                <div class="col-md-4">
                    <label>Peso soportado:</label>
                    <input type="text" class="form-control readonly" readonly value="25 Toneladas">
                </div>
            </div>

        </form>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-maroon" onclick="regresarFiltros()">
                Regresar a filtros
            </button>
        </div>
    </div>


    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- scripts -->
    <script>
        function mostrarDetalle() {
            document.getElementById('filtros').classList.add('hidden');
            document.getElementById('detalle').classList.remove('hidden');
        }

        function regresarFiltros() {
            document.getElementById('detalle').classList.add('hidden');
            document.getElementById('filtros').classList.remove('hidden');
        }
    </script>


</body>

</html>