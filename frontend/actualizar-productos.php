<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Productos';
$seccion = 'Actualizar Productos';
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
            width: 90%;
            margin: 40px auto;
            background: #f8f9fa;
            padding: 30px;
            border-radius: 5px;
        }

        .breadcrumb-container {
            padding-left: 15px;
            font-size: 18px;
        }

        .breadcrumb-container a i {
            color: #4D2132;
        }

        h2 {
            text-align: center;
            color: #8A3B0A;
            margin-bottom: 25px;
        }

        .form-section {
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #bbb;
            font-size: 15px;
        }

        .form-row {
            display: flex;
            gap: 25px;
        }

        .form-col {
            flex: 1;
        }

        .form-subrow {
            display: flex;
            gap: 10px;
        }

        .form-subrow .form-col {
            flex: 1;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
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

        .form-subrow {
            display: flex;
            gap: 25px;
            /* espacio entre Altura y Largo */
        }

        .form-subrow .form-col {
            flex: 1;
            /* ambas columnas iguales */
            display: flex;
            flex-direction: column;
            /* label arriba, input abajo */
        }

        .form-subrow .form-col input {
            width: 100%;
            /* igual que los demás inputs */
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #bbb;
            font-size: 15px;
            box-sizing: border-box;
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
    <h2><?php echo $seccion; ?></h2>

    <div class="content-area">

        <!-- Buscador -->
        <div class="search-box">
            <div style="background:#4a1026; color:white; padding:8px 15px; font-weight:bold; border-radius:3px;">
                Buscar producto:
            </div>
            <input type="text" placeholder="Escribe el nombre del producto.">
        </div>

        <!-- Formulario -->
        <div class="form-section">
            <form method="POST" action="procesar_producto.php">
                <div class="form-row">

                    <!-- Columna izquierda -->
                    <div class="form-col">
                        <div class="form-group">
                            <label>Nombre del Producto:</label>
                            <input type="text" name="nombre_producto" required>
                        </div>

                        <div class="form-group">
                            <label>Cajas por Cama:</label>
                            <input type="text" name="cajas_por_cama">
                        </div>

                        <div class="form-group">
                            <label>Peso:</label>
                            <input type="text" name="peso">
                        </div>

                        <div class="form-subrow">
                            <div class="form-col">
                                <label for="altura">Altura:</label>
                                <input type="text" id="altura" name="altura">
                            </div>
                            <div class="form-col">
                                <label for="largo">Largo:</label>
                                <input type="text" id="largo" name="largo">
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Peso Volumétrico:</label>
                            <input type="text" name="peso_volumetrico">
                        </div>

                        <div class="form-group">
                            <label>Tipo de embalaje:</label>
                            <input type="text" name="tipo_embalaje">
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="form-col">
                        <div class="form-group">
                            <label>Ubicación (id_localidad):</label>
                            <input type="text" name="id_localidad">
                        </div>

                        <div class="form-group">
                            <label>Cajas por Pallet:</label>
                            <input type="text" name="cajas_por_pallet">
                        </div>

                        <div class="form-group">
                            <label>Peso soportado (Kg):</label>
                            <input type="text" name="peso_soportado">
                        </div>

                        <div class="form-group">
                            <label>Ancho:</label>
                            <input type="text" name="ancho">
                        </div>

                        <div class="form-group">
                            <label>Unidades en existencia:</label>
                            <input type="text" name="unidades_existencia">
                        </div>

                        <div class="form-group">
                            <label>Tipo de mercancía:</label>
                            <input type="text" name="tipo_mercancia">
                        </div>
                    </div>

                </div>

                <!-- Botón guardar -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-custom">Actualizar Producto</button>
                </div>
            </form>
        </div>

    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>