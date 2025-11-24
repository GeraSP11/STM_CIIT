<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Usuarios';
$seccion = 'Consultar Usuario';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos institucionales del header -->
    <link rel="stylesheet" href="/assets/css/headers-styles.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Área principal */
        .content-area {
            padding: 30px;
            width: 55%;
            margin: 40px auto;
            background: #f8f9fa;
            border-radius: 5px;
        }

        /* Etiqueta del título pequeño */
        .search-label {
            background-color: #4D2132;
            color: white;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 3px;
            display: inline-block;
            margin-bottom: 20px;
        }

        /* Inputs */
        .form-control-custom {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #bbb;
            font-size: 15px;
        }

        /* Botón */
        .btn-custom {
            display: block;
            background-color: #4D2132;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            margin: 25px auto 0;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #3b1826;
        }

        /* Título principal */
        h2 {
            text-align: center;
            color: #4D2132;
            margin-top: 10px;
            font-weight: bold;
        }

        /* Breadcrumb */
        .breadcrumb-container {
            padding-left: 15px;
            font-size: 18px;
        }

        .breadcrumb-container a i {
            color: #4D2132;
        }
        
    </style>
</head>

<body>

    <!-- Header dinámico con variables -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-container mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Título -->
    <h2><?php echo $seccion; ?></h2>

    <!-- Contenido -->
    <div class="content-area">

        <form id="formConsultaUsuarios">
            <div class="search-label">Clave: *</div>

            <input type="text" id="clave_usuario" name="clave_usuario" placeholder="Clave de Identificación de Personal" class="form-control-custom">
            <small style="color: gray;">*Campos obligatorios</small>

            <button class="btn btn-custom">Consultar</button>
        </form>

    </div>

    <!-- Contenedor tabla resultados, oculto inicialmente -->
    <div id="tablaUsuarios" class="mt-4" style="display:none;">
        <table class="table table-bordered w-75 mx-auto shadow">
            <thead style="background-color: #4D2132; color: #4D2132; font-size: 18px;">
                <tr>
                    <th colspan="2" class="text-center">Resultados</th>
                </tr>
            </thead>

            <tbody id="tbodyUsuarios">
                <tr>
                    <td class="fw-bold">Nombre de usuario:</td>
                    <td id="td_usuario"></td>
                </tr>
                <tr>
                    <td class="fw-bold">Nombre completo:</td>
                    <td id="td_nombre"></td>
                </tr>
                <tr>
                    <td class="fw-bold">Correo electrónico:</td>
                    <td id="td_correo"></td>
                </tr>
                <tr>
                    <td class="fw-bold">Clave de Identificación de Personal:</td>
                    <td id="td_clave"></td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            <button id="btnVolver" class="btn btn-custom">Volver</button>
        </div>
    </div>


    <!-- Bootstrap JS local -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- SCRIPT DEL FORMULARIO -->
    <script src="/assets/js/usuarios.js"></script>
</body>

</html>