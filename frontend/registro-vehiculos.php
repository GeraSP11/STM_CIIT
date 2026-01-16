<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Vehículos'; // Consistente con localidades
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
        /* Estructura idéntica a Localidades */
        .form-container {
            background-color: #f8f9fa;
            padding: 40px;
            border-radius: 8px;
            width: 90%; /* Un poco más ancho para las dos columnas */
            min-width: 400px;
            margin: 40px auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .btn-custom {
            background-color: #4a1026;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
            color: white;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .section-title {
            color: #4a1026;
            font-weight: bold;
            border-bottom: 2px solid #4a1026;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .scroll-table {
            max-height: 400px;
            overflow-y: auto;
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
            <div class="row">
                <div class="col-lg-7">
                    <h5 class="section-title"><i class="fas fa-truck"></i> Información del Vehículo</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modalidad_vehiculo" class="form-label">Modalidad</label>
                            <select id="modalidad_vehiculo" name="modalidad_vehiculo" class="form-select" required>
                                <option value="">Seleccione modalidad</option>
                                <option value="Carretero">Carretero</option>
                                <option value="Ferroviario">Ferroviario</option>
                                <option value="Marítimo">Marítimo</option>
                                <option value="Aéreo">Aéreo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="clave_vehiculo" class="form-label">Clave vehícular</label>
                            <input type="text" id="clave_vehiculo" name="clave_vehiculo" class="form-control" placeholder="Ej: T3-S2-123" required>
                        </div>

                        <div class="col-md-12">
                            <label for="descripcion_vehiculo" class="form-label">Descripción</label>
                            <textarea id="descripcion_vehiculo" name="descripcion_vehiculo" class="form-control" rows="2" placeholder="Marca, modelo, color..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="chofer_asignado" class="form-label">Chofer Asignado</label>
                            <select id="chofer_asignado" name="chofer_asignado" class="form-select" required>
                                <option value="">Cargando personal...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="nomenclatura" class="form-label">Nomenclatura</label>
                            <input type="text" id="nomenclatura" name="nomenclatura" class="form-control" placeholder="Ej: T3-S2">
                        </div>

                        <div class="col-md-4">
                            <label for="clase" class="form-label">Clase</label>
                            <input type="text" id="clase" name="clase" class="form-control" maxlength="2" placeholder="Ej: T">
                        </div>

                        <div class="col-md-4" id="div_ejes" style="display:none;">
                            <label for="numero_de_ejes" class="form-label">No. Ejes</label>
                            <input type="number" id="numero_de_ejes" name="numero_de_ejes" class="form-control" value="0">
                        </div>

                        <div class="col-md-4" id="div_llantas" style="display:none;">
                            <label for="numero_de_llantas" class="form-label">No. Llantas</label>
                            <input type="number" id="numero_de_llantas" name="numero_de_llantas" class="form-control" value="0">
                        </div>

                        <div class="col-md-4">
                            <label for="peso_bruto_vehicular" class="form-label">Peso Bruto (Ton)</label>
                            <input type="number" step="0.1" id="peso_bruto_vehicular" name="peso_bruto_vehicular" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <h5 class="section-title"><i class="fas fa-boxes"></i> Carrocerias</h5>
                    <div class="table-container">
                        <p class="text-muted small">Seleccione las carrocerías compatibles con la modalidad elegida.</p>
                        <div class="scroll-table">
                            <table class="table table-hover align-middle" id="tablaCarrocerias">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40px">Sel.</th>
                                        <th>Tipo</th>
                                        <th>Serie</th>
                                    </tr>
                                </thead>
                                <tbody id="lista_carrocerias">
                                    <tr>
                                        <td colspan="3" class="text-center">Seleccione una modalidad primero</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="mensaje_validacion_carroceria" class="mt-2 small text-danger"></div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button class="btn btn-custom me-3" type="submit">Guardar Vehículo</button>
                <button class="btn btn-outline-secondary" type="reset">Cancelar</button>
            </div>
        </form>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/js/alertas.js"></script>
    <script src="/assets/js/vehiculos.js"></script>
</body>

</html>