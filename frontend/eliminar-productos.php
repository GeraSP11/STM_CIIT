<?php
$page_title = 'Eliminar Productos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Productos</title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
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

        .header {
            background: linear-gradient(#4b0000 100%);
            color: white;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 300;
            margin: 0;
        }

        .header-logo {
            width: 100px;
            height: 100px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            padding: 10px;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .breadcrumb-nav {
            background-color: white;
            padding: 15px 40px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }

        .breadcrumb-nav i {
            font-size: 1.5rem;
            color: #5c2e3e;
        }

        .breadcrumb-nav span {
            color: #333;
            font-weight: 500;
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
            color: #8b4513;
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
            background-color: #5c2e3e;
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
            border-color: #5c2e3e;
        }

        .btn-search {
            background-color: #5c2e3e;
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
            background-color: #7d3e54;
        }

        .hidden {
            display: none;
        }

        .results-section {
            margin-top: 40px;
        }

        .results-header {
            background-color: #5c2e3e;
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
            background-color: #5c2e3e;
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
            background-color: #7d3e54;
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
    </style>
</head>

<body>

    <div class="header">
        <h1>Gestión de Productos</h1>
        <div class="header-logo">
            <img src="/assets/images/logo-corredor.png" alt="Corredor Interoceánico">
        </div>
    </div>

    <div class="breadcrumb-nav" id="breadcrumb">
        <i class="bi bi-house-door-fill"></i>
        <span>></span>
        <span id="breadcrumb-text">Eliminar Productos</span>
    </div>

    <main class="main-content">
        <h2>Eliminar Productos</h2>

        <!-- Sección de búsqueda -->
        <div class="search-section" id="searchSection">
            <div class="search-label">Filtro de búsqueda: *</div>
            <select id="producto_select">
                <option value="">Identificador del Producto</option>
                <!-- Las opciones se cargarán dinámicamente -->
            </select>
            <br>
            <button type="button" class="btn-search" id="btnBuscar">Buscar</button>
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

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>