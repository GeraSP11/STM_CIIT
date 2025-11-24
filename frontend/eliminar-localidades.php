<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Localidades';
$seccion = 'Eliminar Localidades';
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
        .search-box {
            margin: 40px auto;
            width: 55%;
            background: #f5f5f5;
            padding: 40px;
            border-radius: 5px;
        }

        .search-title {
            background: #4a1026;
            color: white;
            display: inline-block;
            padding: 8px 20px;
            font-weight: bold;
            margin-bottom: 25px;
            font-size: 18px;
            border-radius: 4px;
        }

        .btn-custom {
            background: #4a1026;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-custom:hover {
            background: #3a0c1f;
        }

        .btn-custom:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        /* Estilo para el input con datalist */
        #inputBuscarLocalidad {
            width: 70%;
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>

    <!-- Header dinámico -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb" style="padding-left: 15px;">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Título -->
    <h2 class="text-center mt-2" style="color:#4a1026;"><?php echo $seccion; ?></h2>

    <!-- Contenedor principal -->
    <div class="search-box shadow">
        <div class="search-title">Filtro de búsqueda: *</div>

        <input 
            type="text" 
            class="form-control mt-3" 
            id="inputBuscarLocalidad" 
            list="localidades" 
            placeholder="Escribe el nombre del centro o localidad (mínimo 2 caracteres)..."
            autocomplete="off"
        >
        <datalist id="localidades"></datalist>

        <button class="btn-custom mt-4" id="btnEliminar">Eliminar</button>
    </div>

    <!-- Scripts -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets/js/alertas.js"></script>
    <script src="/assets/js/localidades.js"></script>
</body>

</html>