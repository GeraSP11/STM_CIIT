<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Vehículos';
$seccion = 'Actualizar Vehículos';
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

        datalist {
            max-height: 200px;
            overflow-y: auto;
        }

        #contenedorBusqueda {
            display: flex;
        }

        #contenedorBusqueda.oculto {
            display: none;
        }

        #contenedorBotones {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .form-main-container {
            width: 80%;
            margin: 25px auto;
            background: #f8f9fa;
            padding: 35px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

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

    <h2 style="text-align:center; color:#4a1026; margin-top:10px; margin-bottom:30px;">
        <?php echo $seccion; ?>
    </h2>

    <div id="contenedorBusqueda" style="width:80%; margin:20px auto; align-items:center; gap:10px; position:relative;">
        <div style="background:#4a1026; color:white; padding:8px 15px; font-weight:bold; border-radius:3px; white-space:nowrap;">
            Buscar Placas:
        </div>
        <div style="flex:1; position:relative;">
            <input type="text" id="inputBuscarVehiculo" placeholder="Escribe las placas del vehículo"
                autocomplete="off" list="listaVehiculos" style="text-transform: uppercase;">
            <datalist id="listaVehiculos">
                </datalist>
        </div>
    </div>

    <form id="formActualizarVehiculo" method="POST">
        <div class="form-main-container">

            <input type="hidden" name="id_vehiculo" id="inputIdVehiculo">

            <div style="margin-bottom:25px; text-align:center;">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">ID Sistema:</label>
                <input type="text" id="inputIdVehiculoDisplay" readonly placeholder="ID"
                    style="padding:8px; width:200px; border:1px solid #bbb; border-radius:4px; display:inline-block; background:#e9ecef; text-align:center;">
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Placas *</label>
                    <input type="text" name="placas" id="inputPlacas" required maxlength="10" 
                        pattern="^[A-Z0-9\-]+$" style="text-transform: uppercase;"
                        title="Solo mayúsculas, números y guiones">
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Marca *</label>
                    <input type="text" name="marca" id="inputMarca" required maxlength="50">
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Modelo (Año) *</label>
                    <input type="number" name="modelo" id="inputModelo" required min="1990" max="2026">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Capacidad (kg) *</label>
                    <input type="number" name="capacidad_carga" id="inputCapacidad" required step="0.01">
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Tipo de Vehículo *</label>
                    <select name="tipo_vehiculo" id="selectTipoVehiculo" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="Tractocamion">Tractocamión</option>
                        <option value="Torton">Torton</option>
                        <option value="Rabón">Rabón</option>
                        <option value="Camioneta">Camioneta</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Carrocería *</label>
                    <select name="id_carroceria" id="selectCarroceria" required>
                        <option value="">Seleccione una carrocería</option>
                        </select>
                </div>
            </div>

            <p style="font-size:14px; color:#444;">* Campos obligatorios</p>

            <div id="contenedorBotones">
                <button type="submit"
                    style="background:#4a1026; color:white; padding:12px 35px; border:none; border-radius:4px; font-size:16px; cursor:pointer; margin-right:15px;">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" onclick="window.location.href='dashboard.php'"
                    style="background:#A00032; color:white; padding:12px 35px; border:none; border-radius:4px; font-size:16px; cursor:pointer;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>

        </div>
    </form>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/js/alertas.js"></script>

    <script src="/assets/js/vehiculos.js"></script>
    
    <script>
        // Carga inicial de carrocerías para el select
        document.addEventListener("DOMContentLoaded", function() {
            const selectCarr = document.getElementById('selectCarroceria');
            fetch('/ajax/carroceria-ajax.php', {
                method: 'POST',
                body: new URLSearchParams({'action': 'consultar-carrocerias'})
            })
            .then(res => res.json())
            .then(data => {
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id_carroceria;
                    opt.textContent = `${c.tipo_carroceria} - ${c.numero_serie}`;
                    selectCarr.appendChild(opt);
                });
            });
        });
    </script>

</body>
</html>