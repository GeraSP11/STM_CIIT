<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Productos';
$seccion = 'Consultar Productos';
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
        .breadcrumb-container {
            padding-left: 15px;
            font-size: 18px;
        }

        .breadcrumb-container a i {
            color: #4D2132;
        }

        .title-section {
            text-align: center;
            font-size: 28px;
            color: #A64300;
            margin-top: 10px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .content-area {
            width: 60%;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 6px;
        }

        label {
            font-size: 18px;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .form-control {
            max-width: 340px;
            height: 40px;
            font-size: 16px;
        }

        .btn-consultar {
            margin-top: 30px;
            background: #6A0025;
            color: white;
            padding: 12px 35px;
            font-size: 18px;
            border: none;
            border-radius: 4px;
        }

        .btn-consultar:hover {
            background: #51001c;
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
                <a href="/dashboard.php"><i class="fas fa-home"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Título -->
    <div class="title-section"><?php echo $seccion; ?></div>

    <!-- FORMULARIO -->
    <div class="content-area">

        <form method="GET" action="consultar_producto.php">

            <h4 class="fw-bold mb-4">Búsqueda por:</h4>

            <!-- Nombre -->
            <div>
                <label>Nombre del producto:</label>
                <input type="text" class="form-control" name="nombre_producto">
            </div>

            <!-- Filtro -->
            <div>
                <label>Aplicar un filtro:</label>
                <select class="form-control" name="filtro">
                    <option selected disabled value="">Seleccione un filtro</option>
                    <option value="ubicacion">Ubicación del producto</option>
                    <option value="tipo_mercancia">Tipo de mercancía</option>
                    <option value="peso">Peso</option>
                    <option value="existencia">Cantidad en existencia</option>
                </select>
            </div>

            <!-- Botón -->
            <button type="submit" class="btn-consultar d-block mx-auto">Consultar</button>

        </form>

    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>