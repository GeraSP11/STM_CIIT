<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Carrocerías';
$seccion = 'Actualizar Carrocería';
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
            border: 1px solid #ccc;
        }

        .form-label { display: block; margin-bottom: 6px; font-weight: bold; }
        .form-input, .form-select { width: 100%; padding: 10px; border: 1px solid #bbb; border-radius: 4px; margin-bottom: 15px; }

        .btn-custom { background-color: #4a1026; color: white; padding: 12px 35px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn-custom:hover { background-color: #3b0d20; }
        .btn-custom:disabled { background-color: #6c757d; cursor: not-allowed; }
        
        .search-section {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid #4a1026;
        }

        h2 { text-align: center; color: #4a1026; margin-top: 10px; margin-bottom: 30px; }

        .alert-info {
            border-left: 5px solid #0dcaf0;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

    <nav aria-label="breadcrumb" class="mt-2" style="padding-left: 15px; font-size: 18px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a></li>
            <li class="breadcrumb-item active"><?php echo $seccion; ?></li>
        </ol>
    </nav>

    <h2><?php echo $seccion; ?></h2>

    <div class="form-container">
        <div class="search-section">
            <label for="buscar_matricula" class="form-label">Buscar Carrocería por Matrícula / NIV</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="buscar_matricula" class="form-control" placeholder="Escriba la matrícula..." list="lista_carrocerias">
                <datalist id="lista_carrocerias"></datalist>
                <button class="btn btn-dark" type="button" id="btnCargarCarroceria">
                    Cargar Datos
                </button>
            </div>
            <small class="text-muted">Debe cargar una carrocería para poder editar los campos.</small>
        </div>

        <form id="formActualizarCarroceria">
            <input type="hidden" id="id_carroceria" name="id_carroceria">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="modalidad_carroceria" class="form-label">Modalidad *</label>
                    <select id="modalidad_carroceria" name="modalidad_carroceria" class="form-select" required>
                        <option value="">Seleccione modalidad</option>
                        <option value="Carretero">Carretero</option>
                        <option value="Ferroviario">Ferroviario</option>
                        <option value="Marítimo">Marítimo</option>
                        <option value="Aéreo">Aéreo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="matricula" class="form-label">Matrícula / NIV *</label>
                    <input type="text" id="matricula" name="matricula" class="form-input" required>
                </div>
                <div class="col-md-4">
                    <label for="estatus_carroceria" class="form-label">Estatus del Registro *</label>
                    <select id="estatus_carroceria" name="estatus_carroceria" class="form-select" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Ensamblada">Ensamblada</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="peso_vehicular" class="form-label">Peso Vehicular (kg) *</label>
                    <input type="number" step="0.01" id="peso_vehicular" name="peso_vehicular" class="form-input" required min="1">
                </div>
                <div class="col-md-4">
                    <label for="numero_ejes_vehiculares" class="form-label">Número de Ejes</label>
                    <input type="number" id="numero_ejes_vehiculares" name="numero_ejes_vehiculares" class="form-input" min="0">
                </div>
                <div class="col-md-4">
                    <label for="tipo_carroceria" class="form-label">Tipo de carrocería (Solo lectura)</label>
                    <input type="text" id="tipo_carroceria" name="tipo_carroceria" class="form-input" readonly 
                           style="background-color: #e9ecef;" title="El tipo no puede cambiarse por integridad de los contenedores">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="localidad_pertenece" class="form-label">Localidad de Pertenencia *</label>
                    <select id="localidad_pertenece" name="localidad_pertenece" class="form-select" required>
                        </select>
                </div>
                <div class="col-md-6">
                    <label for="responsable_carroceria" class="form-label">Responsable Asignado *</label>
                    <select id="responsable_carroceria" name="responsable_carroceria" class="form-select" required>
                        </select>
                </div>
            </div>

            <div class="alert alert-info" id="msgAvisoDetalle" style="display:none;">
                <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Esta carrocería tiene detalles de contenedores vinculados. Para editar dimensiones, use el módulo de "Detalles de Carga".
            </div>

            <div class="d-flex justify-content-center">
                <button class="btn btn-custom" type="submit" id="btnActualizar" disabled>
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    
    <script src="/assets/js/carrocerias.js"></script>
</body>

</html>