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

        /* Contenedor principal */
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

        <form>

            <!-- Fila 1 -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label>Identificador de ruta:</label>
                    <input type="text" class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label>Localidad origen:</label>
                    <select class="form-select">
                        <option selected disabled></option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Localidad destino:</label>
                    <select class="form-select">
                        <option selected disabled></option>
                    </select>
                </div>
            </div>

            <!-- Fila 2 -->
            <div class="row">
                <div class="col-md-4">
                    <label>Modalidad:</label>
                    <input type="text" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Distancia:</label>
                    <input type="text" class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label>Peso soportado:</label>
                    <input type="number" class="form-control">
                </div>
            </div>

            <!-- Botones -->
            <div class="btn-container">
                <button type="submit" class="btn btn-maroon">Guardar</button>
                <button type="button" class="btn btn-maroon">Cancelar</button>
            </div>

        </form>

    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- scripts -->

</body>

</html>