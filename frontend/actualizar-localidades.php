<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Localidades';
$seccion = 'Actualizar Localidades';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb inline -->
    <nav aria-label="breadcrumb" class="mt-2" style="padding-left: 15px; font-size: 18px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#"><i class="fas fa-home" style="color: #4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Título principal -->
    <h2 style="text-align:center; color:#4a1026; margin-top:10px; margin-bottom:30px;">
        <?php echo $seccion; ?>
    </h2>

    <!-- Barra superior de búsqueda -->
    <div style="width:80%; margin:20px auto; display:flex; align-items:center; gap:10px;">
        <div style="background:#4a1026; color:white; padding:8px 15px; font-weight:bold; border-radius:3px;">
            Nombre de la Localidad:
        </div>
        <input type="text" placeholder="Escribe el nombre de la Localidad"
            style="flex:1; padding:10px; border:1px solid #bbb; border-radius:4px;">
    </div>

    <!-- Contenedor principal -->
    <div
        style="width:80%; margin:25px auto; background:#f8f9fa; padding:35px; border-radius:8px; border:1px solid #ccc;">

        <!-- ID Localidad -->
        <div style="margin-bottom:25px; text-align:center;">
            <label style="font-weight:bold; display:block; margin-bottom:6px;">ID Localidad:</label>
            <input type="text"
                style="padding:8px; width:200px; border:1px solid #bbb; border-radius:4px; display:inline-block;">
        </div>


        <!-- Fila 1 -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">Nombre del Centro de Trabajo
                    *</label>
                <input type="text" style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
            </div>
            <div class="col-md-4">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">Ubicación Georreferenciada (Latitud,
                    Longitud) *</label>
                <input type="text" style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
            </div>
            <div class="col-md-4">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">Población *</label>
                <input type="text" style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
            </div>
        </div>

        <!-- Fila 2 -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">Estado *</label>
                <select style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                    <option>Seleccione un estado</option>
                </select>
            </div>
            <div class="col-md-4">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">Tipo de Instalación *</label>
                <select style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                    <option>Seleccione un tipo de instalación</option>
                </select>
            </div>
            <div class="col-md-4">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">Localidad *</label>
                <input type="text" style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
            </div>
        </div>

        <!-- Nota -->
        <p style="font-size:14px; color:#444;">*Campos obligatorios</p>

        <!-- Botones -->
        <!-- <div style="text-align:center; margin-top:20px;">
            <button
                style="background:#4a1026; color:white; padding:12px 35px; border:none; border-radius:4px; font-size:16px; cursor:pointer; margin-right:15px;">
                Guardar
            </button>
            <button
                style="background:#A00032; color:white; padding:12px 35px; border:none; border-radius:4px; font-size:16px; cursor:pointer;">
                Cancelar
            </button>
        </div> -->

    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>