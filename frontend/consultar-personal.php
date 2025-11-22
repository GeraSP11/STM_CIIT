<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Personal';
$seccion = 'Consulta de Personal';
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
            padding: 30px;
            border-radius: 8px;
            width: 50%;
            min-width: 400px;
            margin: 40px auto;
        }

        .form-label-box {
            background-color: #4a1026;
            color: white;
            width: 120px;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-control-custom {
            width: 90%;
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
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
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

    <!-- Contenedor formulario consulta -->
    <div id="consultaCurp" class="form-container">
        <form id="formConsulta">
            <div class="form-label-box">CURP:</div>
            <input type="text" class="form-control-custom" style="text-transform: uppercase;"
                oninput="this.value = this.value.toUpperCase()" id="curp" name="curp" required maxlength="18"
                placeholder="Clave Única de Registro de Población"
                pattern="([A-Z][AEIOUX][A-Z]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)"
                title="CURP válido de 18 caracteres">
            <div class="d-flex justify-content-center">
                <button id="btnConsultar" type="submit" class="btn btn-custom">Consultar</button>
            </div>
        </form>
    </div>


    <!-- Contenedor tabla resultados, oculto inicialmente -->
    <div id="tablaResultados" class="form-container" style="display:none;">
        <table id="tablaPersonal" class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Afiliación Laboral</th>
                    <th>Cargo</th>
                    <th>CURP</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="d-flex justify-content-center">
            <button id="btnVolver" class="btn btn-custom">Volver</button>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- TU SCRIPT DEL FORMULARIO -->
    <script src="/assets/js/personal.js"></script>
</body>

</html>