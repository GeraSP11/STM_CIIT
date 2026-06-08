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
        .tabla-rutas input[type="radio"] {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid var(--vino);
            border-radius: 50%;
            cursor: pointer;
            position: relative;
        }

        .tabla-rutas input[type="radio"]:checked::before {
            content: "";
            width: 8px;
            height: 8px;
            background: var(--vino);
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
        }

        .fila-seleccionada {
            background-color: #f5eef2;
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
    <h2 class="page-title">
        Actualización de Rutas
    </h2>

    <!-- CONTENIDO -->
    <div class="container-fluid pb-5">

        <div class="card card-custom">

            <div class="card-header-custom">
                Actualización de rutas
            </div>

            <div class="card-body">

                <!-- BUSCADOR -->
                <div class="buscador-rutas">

                    <label>
                        Filtro de búsqueda
                    </label>

                    <div class="input-group">

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Ingrese ID de ruta">

                    </div>

                </div>

                <!-- RESULTADOS -->
                <h6 class="mb-3 fw-bold">
                    Resultados obtenidos:
                </h6>

                <div class="tabla-rutas">

                    <div class="tabla-scroll">

                        <table class="table table-bordered mb-0">

                            <thead>
                                <tr>
                                    <th width="60"></th>
                                    <th>ID Ruta</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="ruta">
                                    </td>
                                    <td>1001</td>
                                    <td>Ruta 1</td>
                                </tr>

                                <tr class="fila-seleccionada">
                                    <td class="text-center">
                                        <input type="radio" name="ruta" checked>
                                    </td>
                                    <td>1002</td>
                                    <td>Ruta 2</td>
                                </tr>

                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="ruta">
                                    </td>
                                    <td>1003</td>
                                    <td>Ruta 3</td>
                                </tr>

                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="ruta">
                                    </td>
                                    <td>1004</td>
                                    <td>Ruta 4</td>
                                </tr>

                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="ruta">
                                    </td>
                                    <td>1005</td>
                                    <td>Ruta 5</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

                <!-- BOTONES -->
                <div class="acciones">

                    <button class="btn btn-gris">
                        Cancelar
                    </button>

                    <button class="btn btn-vino">
                        Actualizar
                    </button>

                </div>

            </div>

        </div>

    </div>

</body>

</html>