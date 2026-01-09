<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Carrocerías';
$seccion = 'Actualizar Carrocerías';
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
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
        }
        datalist { max-height: 200px; overflow-y: auto; z-index: 1000; }
        #contenedorBotones { display: none; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <?php include('includes/header-dinamico.php'); ?>

    <nav aria-label="breadcrumb" class="mt-2" style="padding-left: 15px; font-size: 18px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $seccion; ?></li>
        </ol>
    </nav>

    <h2 style="text-align:center; color:#4a1026; margin-top:10px; margin-bottom:30px;"><?php echo $seccion; ?></h2>

    <div id="contenedorBusqueda" style="width:80%; margin:20px auto; display:flex; align-items:center; gap:10px;">
        <div style="background:#4a1026; color:white; padding:8px 15px; font-weight:bold; border-radius:3px;">
            Matrícula de la Carrocería:
        </div>
        <div style="flex:1; position:relative;">
            <input type="text" id="inputBuscarCarroceria" placeholder="Escribe la matrícula para buscar..." autocomplete="off" list="carrocerias">
            <datalist id="carrocerias"></datalist>
        </div>
    </div>

    <form id="formActualizarCarroceria" method="POST">
        <div style="width:80%; margin:25px auto; background:#f8f9fa; padding:35px; border-radius:8px; border:1px solid #ccc;">
            
            <input type="hidden" name="id_carroceria" id="inputIdCarroceria">

            <div style="margin-bottom:25px; text-align:center;">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">ID Carrocería:</label>
                <input type="text" id="inputIdCarroceriaDisplay" readonly placeholder="ID" style="width:200px; background:#e9ecef; display:inline-block;">
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label style="font-weight:bold;">Matrícula *</label>
                    <input type="text" name="matricula" id="inputMatricula" required>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold;">Modalidad *</label>
                    <select name="modalidad_carroceria" id="selectModalidad" required>
                        <option value="Carretero">Carretero</option>
                        <option value="Ferroviario">Ferroviario</option>
                        <option value="Marítimo">Marítimo</option>
                        <option value="Aéreo">Aéreo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold;">Tipo *</label>
                    <select name="tipo_carroceria" id="selectTipo" required>
                        <option value="Unidad de arrastre">Unidad de arrastre</option>
                        <option value="Unidad de carga">Unidad de carga</option>
                        <option value="Mixta">Mixta</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label style="font-weight:bold;">Peso Vehicular (kg) *</label>
                    <input type="number" step="0.01" name="peso_vehicular" id="inputPeso" required>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold;">Ejes Vehiculares</label>
                    <input type="number" name="numero_ejes_vehiculares" id="inputEjes">
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold;">Num. Contenedores</label>
                    <input type="number" name="numero_contenedores" id="inputContenedores">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label style="font-weight:bold;">Localidad Perteneciente *</label>
                    <select name="localidad_pertenece" id="selectLocalidad" required></select>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold;">Responsable *</label>
                    <select name="responsable_carroceria" id="selectResponsable" required></select>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold;">Estatus *</label>
                    <select name="estatus_carroceria" id="selectEstatus" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Ensamblada">Ensamblada</option>
                        <option value="En mantenimiento">En mantenimiento</option>
                        <option value="En reparación">En reparación</option>
                    </select>
                </div>
            </div>

            <div id="contenedorBotones">
                <button type="submit" class="btn" style="background:#4a1026; color:white; padding:12px 35px;">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" onclick="window.location.href='dashboard.php'" class="btn" style="background:#A00032; color:white; padding:12px 35px; margin-left:15px;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </form>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/js/alertas.js"></script>
    <script src="/assets/js/carrocerias.js"></script>
</body>
</html>