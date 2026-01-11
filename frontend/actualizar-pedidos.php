<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Jefe de Almacén"]);
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Pedidos';
$seccion = 'Consulta de Pedidos';
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
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }

    .header-seccion {
        background-color: #6b2d42;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .contenedor-principal {
        background-color: white;
        border-radius: 8px;
        padding: 40px;
        max-width: 800px;
        margin: 0 auto 40px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .titulo-consulta {
        color: #8b4513;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .filtro-titulo {
        background-color: #4d2132;
        color: white;
        padding: 10px 20px;
        display: inline-block;
        margin-bottom: 30px;
        font-weight: bold;
    }

    .form-control {
        border: 2px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 15px;
    }

    .separador-o {
        text-align: center;
        margin: 20px 0;
        font-weight: bold;
        color: #666;
    }

    .btn-consultar, .btn-actualizar, .btn-guardar {
        background-color: #4d2132;
        color: white;
        padding: 12px 40px;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        display: block;
        margin: 30px auto 0;
    }

    .btn-consultar:hover, .btn-actualizar:hover, .btn-guardar:hover {
        background-color: #6b2d42;
        color: white;
    }

    .btn-consultar:disabled, .btn-actualizar:disabled, .btn-guardar:disabled {
        background-color: #999;
        cursor: not-allowed;
    }

    .resultados-header {
        background-color: #6b2d42;
        color: white;
        padding: 15px 20px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .resultado-item {
        background-color: #f8f8f8;
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .resultado-item:hover {
        background-color: #e8e8e8;
    }

    .resultado-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-right: 15px;
        cursor: pointer;
    }

    .detalle-pedido {
        background-color: white;
        border: 2px solid #ccc;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 20px;
    }

    .campo-detalle {
        background-color: #f0f0f0;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 4px;
    }

    .campo-detalle label {
        font-weight: bold;
        color: #333;
        display: block;
        margin-bottom: 5px;
    }

    .campo-detalle input, .campo-detalle select, .campo-detalle textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: white;
    }

    .campo-detalle textarea {
        resize: vertical;
        min-height: 60px;
    }

    .campo-detalle .texto-info {
        color: #666;
        padding: 8px 0;
    }

    .dropdown-estatus {
        position: relative;
    }

    .dropdown-estatus select {
        appearance: none;
        background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 20px;
        padding-right: 40px;
    }

    .fila-campos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .icono-actualizar {
        width: 24px;
        height: 24px;
        margin-left: 10px;
        vertical-align: middle;
    }

    .breadcrumb {
        background-color: transparent;
        padding-left: 15px;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
    }

    .mensaje-vacio {
        text-align: center;
        padding: 30px;
        color: #666;
        font-style: italic;
    }

    .spinner-border {
        width: 2rem;
        height: 2rem;
        border-width: 0.25em;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .alert {
        margin: 20px auto;
        max-width: 800px;
    }
</style>
</head>
<body>
<!-- Header dinámico -->
<?php include('includes/header-dinamico.php'); ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mt-2">
<ol class="breadcrumb" id="breadcrumb-nav">
<li class="breadcrumb-item">
<a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
</li>
<li class="breadcrumb-item active" aria-current="page">
Consulta de Pedidos
</li>
</ol>
</nav>

<div class="header-seccion">
    Gestión de Pedidos
</div>

<!-- Mensajes de alerta -->
<div id="mensaje-alerta"></div>

<!-- Vista 1: Formulario de Búsqueda -->
<div id="vista-busqueda" class="contenedor-principal">
    <h2 class="titulo-consulta">Consulta de pedidos</h2>
    
    <div class="filtro-titulo">Filtro de búsqueda: *</div>
    
    <form id="form-busqueda">
        <input type="text" class="form-control" id="clave-pedido" placeholder="Clave del Pedido">
        
        <div class="separador-o">o</div>
        
        <select class="form-control" id="localidad-origen">
            <option value="">Localidad Origen</option>
        </select>
        <select class="form-control" id="localidad-destino">
            <option value="">Localidad Destino</option>
        </select>
        
        <button type="submit" class="btn-consultar" id="btn-buscar">Consultar</button>
    </form>
</div>

<!-- Vista 2: Resultados de Búsqueda -->
<div id="vista-resultados" class="contenedor-principal" style="display: none; max-width: 900px;">
    <div class="resultados-header">
        Resultados obtenidos:
    </div>
    
    <div id="lista-resultados">
        <!-- Los resultados se cargarán dinámicamente aquí -->
    </div>
    
    <button type="button" class="btn-actualizar" id="btn-actualizar">
        Actualizar
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAA6ElEQVRIie2UsQ3CMBBE/yMxAR2MQEeHmIAJoGQDRmCGdEhsgBiBlg1gAjr6lC4IyTiOE4kK+dQnne7+7vxnG/gjDVABa2AKTH4JnwNbYAe8gHPK1wnYAgfgnvL1QjSBE/DKYD+gyxG0wCODbYF+DqABrsA9gx2BQQ6gA+4Z7Ap0OYDdH/A1sInwUYTPJTgCbmXhxo+EG2ALzCQc4I4QylSiS+CsiA6AJ3AGnsBBEe2BM/AAPgrRFnijCb6BewaLahXCotH0P7kqMFLgUwX+VcFyGf0fWWfwGDYpX3wNxumrv9YHMyNTkYSzcXAAAAAASUVORK5CYII=" alt="actualizar" class="icono-actualizar">
    </button>
</div>

<!-- Vista 3: Actualizar Pedido -->
<div id="vista-actualizar" class="contenedor-principal" style="display: none; max-width: 900px;">
    <div class="detalle-pedido">
        <div class="fila-campos">
            <div class="campo-detalle">
                <label>ID:</label>
                <div class="texto-info" id="detalle-id"></div>
            </div>
            
            <div class="campo-detalle">
                <label>Clave Pedido:</label>
                <div class="texto-info" id="detalle-clave"></div>
            </div>
            
            <div class="campo-detalle">
                <label>Estatus:</label>
                <div class="dropdown-estatus">
                    <select id="detalle-estatus" class="form-control">
                        <option value="En captura">En captura</option>
                        <option value="En preparación">En preparación</option>
                        <option value="En recolección">En recolección</option>
                        <option value="Enviado">Enviado</option>
                        <option value="En tránsito">En tránsito</option>
                        <option value="En reparto">En reparto</option>
                        <option value="Entregado">Entregado</option>
                    </select>
                </div>
            </div>
            
            <div class="campo-detalle">
                <label>Fecha de solicitud:</label>
                <input type="date" id="detalle-fecha-solicitud" class="form-control">
            </div>
            
            <div class="campo-detalle">
                <label>Fecha de entrega:</label>
                <input type="date" id="detalle-fecha-entrega" class="form-control">
            </div>
        </div>
        
        <div class="fila-campos">
            <div class="campo-detalle">
                <label>Localidad origen:</label>
                <div class="texto-info" id="detalle-localidad-origen"></div>
            </div>
            
            <div class="campo-detalle">
                <label>Localidad destino:</label>
                <div class="texto-info" id="detalle-localidad-destino"></div>
            </div>
        </div>
        
        <div class="campo-detalle">
            <label>Observaciones:</label>
            <textarea id="detalle-observaciones" class="form-control" placeholder="Agregar observaciones"></textarea>
        </div>
    </div>
    
    <button type="button" class="btn-guardar" id="btn-guardar">Guardar cambios</button>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/pedidos.js"></script>
</body>
</html>