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

        <form id="formConsultaProductos" class="mb-3">
            <h4 class="fw-bold mb-4">Búsqueda por:</h4>

            <!-- Nombre -->
            <div class="mb-3">
                <label for="nombre_producto_consulta">Nombre del producto:</label>
                <input type="text" id="nombre_producto_consulta" class="form-control" name="nombre_producto" placeholder="Nombre del producto">
            </div>

            <!-- Filtro -->
            <div class="mb-3">
                <label for="filtro_busqueda">Aplicar un filtro:</label>
                <select id="filtro_busqueda" class="form-control" name="filtro">
                    <option selected value="">Seleccione un filtro</option>
                    <option value="ubicacion">Ubicación del producto</option>
                    <option value="tipo_mercancia">Tipo de mercancía</option>
                    <option value="peso">Peso</option>
                    <option value="existencia">Cantidad en existencia</option>
                </select>
            </div>

            <!-- Contenedores dinámicos -->
            <div id="filter_ubicacion_container" style="display:none;" class="mb-3">
                <label for="filter_ubicacion">Ubicación del producto:</label>
                <select id="filter_ubicacion" class="form-control">
                    <option value="">Seleccione una localidad</option>
                </select>
            </div>

            <div id="filter_tipo_mercancia_container" style="display:none;" class="mb-3">
                <label for="filter_tipo_mercancia">Tipo de mercancía:</label>
                <select id="filter_tipo_mercancia" class="form-control">
                    <option value="">Seleccione</option>
                </select>
            </div>

            <div id="filter_peso_container" style="display:none;" class="mb-3">
                <label for="filter_peso">Rango de peso:</label>
                <select id="filter_peso" class="form-control">
                    <option value="">Seleccione</option>
                    <option value="0-0.99">0 - 0.99 Kg</option>
                    <option value="1-4.99">1 - 4.99 Kg</option>
                    <option value="5-9.99">5 - 9.99 Kg</option>
                    <option value="10-99.99">10 - 99.99 Kg</option>
                    <option value="100-99999">100 Kg o más</option>
                </select>
            </div>

            <div id="filter_existencia_container" style="display:none;" class="mb-3">
                <label for="filter_existencia">Cantidad en existencia (>=):</label>
                <input type="number" id="filter_existencia" class="form-control" min="0" step="1" value="">
            </div>

            <div class="text-center">
                <button type="submit" id="btnConsultar" class="btn-consultar">Consultar</button>
            </div>
        </form>

        <!-- TABLA OCULTA DE RESULTADOS -->
        <div id="tablaResultadosProductos" style="display:none; margin-top:20px;">
            <div class="card">
                <div class="card-header" style="background:#4D2132;color:#fff;">
                    Resultados
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="resultadosTabla">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Ubicación</th>
                                    <th>Tipo Mercancía</th>
                                    <th>Tipo Embalaje</th>
                                    <th>Peso (Kg)</th>
                                    <th>Unidades</th>
                                    <th>Tipo instalación</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyResultadosProductos">
                                <!-- llenado dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button id="btnVolverResultados" class="btn btn-custom">Volver</button>
                </div>
            </div>
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
    <script src="/assets/js/productos.js"></script>

</body>

</html>