<?php
    $page_title = 'Actualizar Productos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar producto</title>
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
            background: linear-gradient( #4b0000 100%);
            color: white;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .main-content h2 {
            color: #8b4513;
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .search-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
            justify-content: center;
        }
        
        .search-label {
            background-color: #5c2e3e;
            color: white;
            padding: 12px 35px;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 4px;
        }
        
        .buscador-container {
            position: relative;
            flex: 0 0 700px;
        }
        
        #buscador_producto {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        #buscador_producto::placeholder {
            color: #999;
        }
        
        #buscador_producto:focus {
            outline: none;
            border-color: #5c2e3e;
        }
        
        .sugerencias {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .sugerencias.activo {
            display: block;
        }
        
        .sugerencia-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .sugerencia-item:hover {
            background-color: #f8f9fa;
        }
        
        .sugerencia-item:last-child {
            border-bottom: none;
        }
        
        .sugerencia-nombre {
            font-weight: bold;
            color: #333;
        }
        
        .sugerencia-id {
            font-size: 12px;
            color: #666;
            margin-left: 5px;
        }
        
        .no-resultados {
            padding: 10px;
            text-align: center;
            color: #999;
        }
        
        .form-container {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .form-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-col label {
            color: #333;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-col input,
        .form-col select {
            padding: 10px 15px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            font-size: 0.95rem;
            background-color: #e8e8e8;
        }
        
        .form-col input:focus,
        .form-col select:focus {
            outline: none;
            border-color: #5c2e3e;
            background-color: white;
        }
        
        .readonly {
            background-color: #e9ecef !important;
            cursor: not-allowed;
        }
        
        .form-row-triple {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .mensaje {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            display: none;
            text-align: center;
            font-weight: 500;
        }
        
        .mensaje.exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .botones {
            display: none;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Gestión de Productos</h1>
    <div class="header-logo">
        <img src="/assets/img/logo_principal.jpeg" alt="Corredor Interoceánico">
    </div>
</div>

<div class="breadcrumb-nav">
    <i class="bi bi-house-door-fill"></i>
    <span>></span>
    <span>Actualizar Productos</span>
</div>

<main class="main-content">
    <h2>Actualizar Productos</h2>

    <div id="mensaje" class="mensaje"></div>

    <div class="search-section">
        <div class="search-label">Buscar Producto:</div>
        <div class="buscador-container">
            <input type="text" 
                   id="buscador_producto" 
                   placeholder="Escribe el nombre del producto."
                   autocomplete="off">
            <div id="sugerencias" class="sugerencias"></div>
        </div>
    </div>

    <div class="form-container">
        <div class="form-col">
            <input type="hidden" name="id_producto" id="id_producto">

            <div class="form-group">
                <label for="nombre_producto">Nombre del Producto:</label>
                <input type="text" id="nombre_producto" name="nombre_producto">
            </div>

            <div class="form-group">
                <label for="cajas_por_cama">Cajas por Cama:</label>
                <input type="number" id="cajas_por_cama" name="cajas_por_cama">
            </div>

            <div class="form-group">
                <label for="peso">Peso:</label>
                <input type="number" id="peso" name="peso" step="0.01">
            </div>

            <div class="form-row-triple">
                <div class="form-group">
                    <label for="altura">Altura:</label>
                    <input type="number" id="altura" name="altura" step="0.01" oninput="calcularPesoVolumetrico()">
                </div>
                <div class="form-group">
                    <label for="largo">Largo:</label>
                    <input type="number" id="largo" name="largo" step="0.01" oninput="calcularPesoVolumetrico()">
                </div>
                <div class="form-group">
                    <label for="ancho">Ancho:</label>
                    <input type="number" id="ancho" name="ancho" step="0.01" oninput="calcularPesoVolumetrico()">
                </div>
            </div>

            <div class="form-group">
                <label for="peso_volumetrico">Peso Volumetrico:</label>
                <input type="number" id="peso_volumetrico" name="peso_volumetrico" step="0.01" class="readonly" readonly>
            </div>

            <div class="form-group">
                <label for="tipo_de_embalaje">Tipo de embalaje:</label>
                <input type="text" id="tipo_de_embalaje" name="tipo_de_embalaje">
            </div>
        </div>

        <div class="form-col">
            <div class="form-group">
                <label for="ubicacion_producto">Ubicación (id_localidad):</label>
                <input type="number" id="ubicacion_producto" name="ubicacion_producto">
            </div>

            <div class="form-group">
                <label for="camas_por_pallet">Cajas por Pallet:</label>
                <input type="number" id="camas_por_pallet" name="camas_por_pallet">
            </div>

            <div class="form-group">
                <label for="peso_soportado">Peso soportado (Kg):</label>
                <input type="number" id="peso_soportado" name="peso_soportado" step="0.01">
            </div>

            <div class="form-group" style="margin-top: 97px;">
                <label for="unidades_existencia">Unidades en existencia:</label>
                <input type="number" id="unidades_existencia" name="unidades_existencia" step="0.01">
            </div>

            <div class="form-group">
                <label for="tipo_de_mercancia">Tipo de mercancía:</label>
                <input type="text" id="tipo_de_mercancia" name="tipo_de_mercancia">
            </div>
        </div>
    </div>

    <div class="botones">
        <button class="btn-guardar" onclick="guardarProducto()">Guardar Cambios</button>
        <button class="btn-cancelar" onclick="limpiarFormulario()">Cancelar</button>
    </div>
</main>

<script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="./js/actualizarEliminarProductos.js"></script>

</body>
</html>