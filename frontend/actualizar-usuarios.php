<?php

require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Usuarios';
$seccion = 'Actualizar Usuarios';
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
            background-color: #f5f5f5;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-box {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .search-title {
            background-color: #5c2e3e;
            color: white;
            padding: 10px 20px;
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 25px;
            display: inline-block;
        }

        .form-label {
            color: #333;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .required {
            color: red;
        }

        .form-control {
            padding: 10px 15px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            font-size: 0.95rem;
            width: 100%;
        }

        .form-control:focus {
            outline: none;
            border-color: #5c2e3e;
        }

        .search-hint {
            color: #999;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .btn-search {
            background-color: #5c2e3e;
            color: white;
            padding: 10px 40px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            float: right;
            margin-top: 10px;
        }

        .btn-search:hover {
            background-color: #4b0000;
        }

        .update-form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .update-form.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .btn-submit {
            background-color: #5c2e3e;
            color: white;
            padding: 12px 50px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: block;
            margin: 30px auto 0;
        }

        .btn-submit:hover {
            background-color: #4b0000;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 0.95rem;
            display: none;
        }

        .alert.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 20px;
            }
        }
    </style>
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

    <main class="main-content">
        <!-- Caja de búsqueda -->
        <div class="search-box">
            <div class="search-title">Actualización de Usuarios:</div>

            <div>
                <label class="form-label">CURP del Usuario <span class="required">*</span></label>
                <input type="text" id="curp_busqueda" class="form-control" maxlength="18"
                    placeholder="Ingrese la CURP del usuario" style="text-transform: uppercase;">
                <div class="search-hint">Presione ENTER o clic en Buscar</div>
            </div>

            <button type="button" id="btnBuscarUsuario" class="btn-search">
                Buscar
            </button>
            <div style="clear: both;"></div>
        </div>

        <!-- Alertas -->
        <div id="alertMessage" class="alert"></div>

        <!-- Formulario de actualización -->
        <!-- Formulario de actualización - VERSIÓN CORREGIDA -->
        <!-- Formulario de actualización -->
        <form id="updateForm" class="update-form">
            <!-- Campos ocultos necesarios -->
            <input type="hidden" id="id_usuario" name="id_usuario" value="">
            <input type="hidden" id="curp" name="curp" value="">
            <input type="hidden" id="nombre_usuario" name="nombre_usuario" value="">
            <input type="hidden" id="identificador_de_rh" name="identificador_de_rh" value="">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nombre: <span class="required">*</span></label>
                    <input type="text" id="nombre_personal" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Apellido Paterno: <span class="required">*</span></label>
                    <input type="text" id="apellido_paterno" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Apellido Materno: <span class="required">*</span></label>
                    <input type="text" id="apellido_materno" class="form-control" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Correo electrónico: <span class="required">*</span></label>
                    <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nueva Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control" minlength="6"
                        placeholder="Dejar vacío para mantener actual">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña:</label>
                    <input type="password" id="contrasena_confirmar" class="form-control" minlength="6"
                        placeholder="Confirmar nueva contraseña">
                </div>
            </div>

            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </form>
    </main>

    <!-- Bootstrap -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tu archivo de alertas -->
    <script src="/assets/js/alertas.js"></script>

    <!-- Tu archivo de usuarios -->
    <script src="/assets/js/usuarios.js"></script>
</body>

</html>