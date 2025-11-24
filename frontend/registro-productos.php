<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Productos';
$seccion = 'Registro de Productos';
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
            color: #A64300;
            margin-bottom: 25px;
        }

        .form-section {
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #bbb;
            font-size: 15px;
            box-sizing: border-box;
        }

        .form-row {
            display: flex;
            gap: 25px;
        }

        .form-col {
            flex: 1;
        }

        .btn-custom {
            background-color: #6A0025;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #50001c;
        }
    </style>
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

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

    <h2><?php echo $seccion; ?></h2>

    <div class="content-area">

        <form id="formRegistroProductos" class="form-section" method="POST">
            <input type="hidden" name="action" value="registrar">

            <div class="form-row">

                <!-- Columna izquierda -->
                <div class="form-col">
                    <div class="form-group">
                        <label for="nombre_producto">Nombre del Producto:</label>
                        <input type="text" id="nombre_producto" name="nombre_producto" placeholder="Ej. Producto A"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="peso">Peso (Kg):</label>
                        <input type="number" id="peso" name="peso_kg" step="0.01" value="0.00">
                    </div>

                    <div class="form-group">
                        <label for="largo">Largo (m):</label>
                        <input type="number" id="largo" name="largo_m" step="0.01" value="0.00">
                    </div>

                    <div class="form-group">
                        <label for="cajas_cama">Cajas por Cama:</label>
                        <input type="number" id="cajas_cama" name="cajas_por_cama" step="1" value="0">
                    </div>

                    <div class="form-group">
                        <label for="peso_soportado">Peso Soportado (Kg):</label>
                        <input type="number" id="peso_soportado" name="peso_soportado_kg" step="0.01" value="0.00">
                    </div>

                    <div class="form-group">
                        <label for="unidades_existencia">Unidades en Existencia:</label>
                        <input type="number" id="unidad_existencia" name="unidad_existencia" step="1" value="0">
                    </div>

                    <div class="form-group">
                        <label for="tipo_mercancia">Tipos de Mercancía:</label>
                        <select id="tipo_mercancia" name="tipo_mercancia">
                            <option value="">Seleccione un tipo</option>

                        </select>
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="form-col">
                    <div class="form-group">
                        <label for="ubicacion_producto">Ubicación del Producto:</label>
                        <select id="ubicacion_producto" name="id_localidad">
                            <option value="">Seleccione una localidad</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="altura">Altura (m):</label>
                        <input type="number" id="altura" name="altura_m" step="0.01" value="0.00">
                    </div>

                    <div class="form-group">
                        <label for="ancho">Ancho (m):</label>
                        <input type="number" id="ancho" name="ancho_m" step="0.01" value="0.00">
                    </div>

                    <div class="form-group">
                        <label for="camas_por_pallet">Camas por Pallet:</label>
                        <input type="number" id="camas_por_pallet" name="cajas_por_pallet" step="1" value="0">
                    </div>

                    <div class="form-group">
                        <label for="peso_volumetrico">Peso Volumétrico (Kg):</label>
                        <input type="number" id="peso_volumetrico" name="peso_volumetrico_kg" step="0.01" value="0.00">
                    </div>

                    <div class="form-group">
                        <label for="tipo_embalaje">Tipo de Embalaje:</label>
                        <select id="tipo_embalaje" name="tipo_embalaje">
                            <option value="">Seleccione un tipo de embalaje</option>
                        </select>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-custom me-2">Guardar</button>
                <button type="reset" class="btn btn-custom">Limpiar</button>
            </div>

        </form>

    </div>

    <!-- Bootstrap JS local -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- SCRIPT DEL FORMULARIO -->
    <script src="/assets/js/productos.js"></script>

</body>

</html>