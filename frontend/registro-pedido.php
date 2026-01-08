<?php

require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Cliente", "Jefe de Almacén"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Pedidos';
$seccion = 'Registro de Pedido';
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

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-2" style="padding-left: 15px; font-size: 18px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Título de sección -->
    <h2><?php echo $seccion; ?></h2>

    <!-- Contenedor principal -->
    

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- CRIPT DEL FORMULARIO -->

    
</body>

</html>