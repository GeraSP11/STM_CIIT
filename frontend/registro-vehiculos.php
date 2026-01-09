<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Vehículos';
$seccion = 'Registro de Vehículos';
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
        .form-container {
            background-color: #f8f9fa;
            padding: 40px;
            border-radius: 8px;
            width: 80%;
            min-width: 400px;
            margin: 40px auto;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .btn-custom {
            background-color: #4a1026;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 15px;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
            margin-bottom: 30px;
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

    <h2><?php echo $seccion; ?></h2>

    <div class="form-container">
        <form id="formVehiculos">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="placas" class="form-label">Placas</label>
                    <input type="text" id="placas" name="placas" class="form-input" placeholder="Ej. ABC-1234"
                        required maxlength="10" pattern="^[A-Z0-9\-]+$" 
                        style="text-transform: uppercase;"
                        title="Solo letras mayúsculas, números y guiones">
                </div>
                <div class="col-md-4">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" id="marca" name="marca" class="form-input" placeholder="Ej. Kenworth"
                        required maxlength="50" pattern="^[A-Za-z0-9\s]+$"
                        title="Marca del vehículo">
                </div>
                <div class="col-md-4">
                    <label for="modelo" class="form-label">Modelo (Año)</label>
                    <input type="number" id="modelo" name="modelo" class="form-input" placeholder="Ej. 2024"
                        required min="1990" max="<?php echo date('Y') + 1; ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="capacidad_carga" class="form-label">Capacidad de Carga (kg)</label>
                    <input type="number" id="capacidad_carga" name="capacidad_carga" class="form-input" 
                        placeholder="Ej. 15000" required step="0.01">
                </div>
                <div class="col-md-4">
                    <label for="tipo_vehiculo" class="form-label">Tipo de Vehículo</label>
                    <select id="tipo_vehiculo" name="tipo_vehiculo" class="form-select" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="Tractocamion">Tractocamión</option>
                        <option value="Torton">Torton</option>
                        <option value="Rabón">Rabón</option>
                        <option value="Camioneta">Camioneta</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="id_carroceria" class="form-label">Carrocería Asignada</label>
                    <select id="id_carroceria" name="id_carroceria" class="form-select" required>
                        <option value="">Cargando carrocerías...</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                <button class="btn btn-custom" type="submit">Guardar Vehículo</button>
                <button class="btn btn-outline-secondary" type="reset">Limpiar</button>
            </div>
        </form>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <script src="/assets/js/alertas.js"></script>

    <script src="/assets/js/vehiculos.js"></script>

    <script>
        // Función rápida para cargar carrocerías disponibles al cargar la página
        document.addEventListener("DOMContentLoaded", function() {
            const selectCarroceria = document.getElementById('id_carroceria');
            
            // Reutilizamos apiRequest si ya está en vehiculos.js o hacemos un fetch rápido
            fetch('/ajax/carroceria-ajax.php', {
                method: 'POST',
                body: new URLSearchParams({'action': 'consultar-carrocerias'})
            })
            .then(res => res.json())
            .then(data => {
                selectCarroceria.innerHTML = '<option value="">Seleccione una carrocería</option>';
                data.forEach(c => {
                    const option = document.createElement('option');
                    option.value = c.id_carroceria;
                    option.textContent = `${c.tipo_carroceria} - ${c.numero_serie}`;
                    selectCarroceria.appendChild(option);
                });
            })
            .catch(() => {
                selectCarroceria.innerHTML = '<option value="">Error al cargar carrocerías</option>';
            });
        });
    </script>
</body>

</html>