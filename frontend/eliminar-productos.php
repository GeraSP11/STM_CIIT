<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Productos';
$seccion = 'Eliminar Productos';
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .main-content {
            background-color: white;
            margin: 40px auto;
            max-width: 1300px;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            min-height: 600px;
        }

        .main-content h2 {
            color: #4a1026;
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 50px;
            text-align: center;
        }

        .search-section {
            background-color: #f8f8f8;
            border-radius: 8px;
            padding: 60px 40px;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-label {
            background-color: #4a1026;
            color: white;
            padding: 12px 40px;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 40px;
        }

        .search-section select {
            width: 100%;
            max-width: 600px;
            padding: 15px 20px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 30px;
            background-color: white;
            cursor: pointer;
        }

        .search-section select:focus {
            outline: none;
            border-color: #4a1026;
        }

        .btn-search {
            background-color: #4a1026;
            color: white;
            padding: 12px 50px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-search:hover {
            background-color: #3a0c1f;
        }

        .hidden {
            display: none;
        }

        .results-section {
            margin-top: 40px;
        }

        .results-header {
            background-color: #4a1026;
            color: white;
            padding: 20px 30px;
            border-radius: 8px 8px 0 0;
            font-size: 1.3rem;
            font-weight: 500;
        }

        .results-container {
            background-color: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 40px;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }

        .results-grid-bottom {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .result-item {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .result-label {
            color: #333;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 10px;
            display: block;
        }

        .result-value {
            color: #999;
            font-size: 0.95rem;
        }

        .form-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .btn-delete {
            background-color: #4a1026;
            color: white;
            padding: 12px 50px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-delete:hover {
            background-color: #3a0c1f;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 12px 50px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        .sugerencias {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: -30px;
    }

    .sugerencias.activo {
        display: block;
    }

    .sugerencia-item {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }

    .sugerencia-item:hover {
        background-color: #f8f9fa;
    }

    .sugerencia-item:last-child {
        border-bottom: none;
    }

    .sugerencia-nombre {
        font-weight: 600;
        color: #333;
        display: block;
    }

    .sugerencia-id {
        font-size: 0.85rem;
        color: #666;
        margin-top: 3px;
    }

    .no-resultados {
        padding: 15px;
        text-align: center;
        color: #999;
        font-style: italic;
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

    <main class="main-content">
        <h2><?php echo $seccion; ?></h2>

        <!-- Sección de búsqueda -->
        <div class="search-section" id="searchSection">
    <div class="search-label">Filtro de búsqueda: *</div>
    <div style="position: relative; max-width: 600px; margin: 0 auto;">
        <input 
            type="text" 
            id="producto_input" 
            placeholder="Escribe el nombre del producto"
            autocomplete="off"
            style="width: 100%; padding: 15px 20px; border: 2px solid #ccc; border-radius: 8px; font-size: 1rem; margin-bottom: 30px;"
        >
        <div id="sugerencias_eliminar" class="sugerencias"></div>
    </div>
</div>

        <!-- Sección de resultados (oculta inicialmente) -->
        <div id="resultsSection" class="results-section hidden">
            <input type="hidden" id="id_producto" value="">

            <div class="results-header">
                Resultados obtenidos:
            </div>

            <div class="results-container">
                <div class="results-grid">
                    <div class="result-item">
                        <span class="result-label">Nombre del Producto:</span>
                        <span class="result-value" id="display_nombre">Queso Oaxaca 1 Kg</span>
                    </div>

                    <div class="result-item">
                        <span class="result-label">Ubicación georeferenciada:</span>
                        <span class="result-value" id="display_ubicacion">-</span>
                    </div>

                    <div class="result-item">
                        <span class="result-label">Peso volumétrico:</span>
                        <span class="result-value" id="display_peso_volumetrico">-</span>
                    </div>
                </div>

                <div class="results-grid-bottom">
                    <div class="result-item">
                        <span class="result-label">Tipo de Mercancía:</span>
                        <span class="result-value" id="display_tipo_mercancia">-</span>
                    </div>

                    <div class="result-item">
                        <span class="result-label">Unidades en existencia:</span>
                        <span class="result-value" id="display_unidades">-</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-delete" id="btnEliminar">Eliminar</button>
                    <button type="button" class="btn-cancel" id="btnCancelar">Cancelar</button>
                </div>
            </div>
        </div>
    </main>

    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/productos.js"></script>
    <script src="/assets/js/alertas.js"></script>
</body>

</html>