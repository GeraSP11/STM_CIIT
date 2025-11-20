<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Personal';
$seccion = 'Eliminar Personal';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .content-area {
            padding: 30px;
            width: 55%;
            margin: 40px auto;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .search-label {
            background-color: #4a1026;
            color: white;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 3px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #bbb;
            font-size: 15px;
        }

        .btn-custom {
            display: block;
            background-color: #4a1026;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            margin: 25px auto 0;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
            font-weight: bold;
        }

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

    <!-- Header dinámico -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-container mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#"><i class="fas fa-home"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Título -->
    <h2><?php echo $seccion; ?></h2>

    <!-- Contenedor principal -->
    <div class="content-area">

        <div class="search-label">Filtro de búsqueda: *</div>

        <input type="text" placeholder="Ingrese la CURP del usuario" class="form-control-custom">

        <button class="btn btn-custom">Buscar</button>

    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
