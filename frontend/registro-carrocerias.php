<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Carrocerías';
$seccion = 'Registro de Carrocerías';
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

        .btn-custom:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        h2 {
            text-align: center;
            color: #4a1026;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .card-detalle {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
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
 <form id="formCarrocerias">
    <div id="seccionPrincipal">
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
                <input type="text" id="matricula" name="matricula" class="form-input" placeholder="Ej. ABC-1234" required maxlength="100">
            </div>
            <div class="col-md-4">
                <label for="tipo_carroceria" class="form-label">Tipo de carrocería *</label>
                <select id="tipo_carroceria" name="tipo_carroceria" class="form-select" required>
                    <option value="">Seleccione tipo</option>
                    <option value="Unidad de arrastre">Unidad de arrastre</option>
                    <option value="Unidad de carga">Unidad de carga</option>
                    <option value="Mixta">Mixta</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="peso_vehicular" class="form-label">Peso Vehicular (kg) *</label>
                <input type="number" step="0.01" id="peso_vehicular" name="peso_vehicular" class="form-input" placeholder="Ej. 1500.50" required min="1">
            </div>
            <div class="col-md-4">
                <label for="numero_ejes_vehiculares" class="form-label">Número de Ejes</label>
                <input type="number" id="numero_ejes_vehiculares" name="numero_ejes_vehiculares" class="form-input" placeholder="Ej. 2" min="1" max="20">
            </div>
            <div class="col-md-4">
                <label for="numero_contenedores" class="form-label">Número de Contenedores</label>
                <input type="number" id="numero_contenedores" name="numero_contenedores" class="form-input" placeholder="Ej. 1" min="0" max="10">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="localidad_pertenece" class="form-label">Localidad de Pertenencia *</label>
                <select id="localidad_pertenece" name="localidad_pertenece" class="form-select" required>
                    <option value="">Seleccione localidad</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="responsable_carroceria" class="form-label">Responsable Asignado *</label>
                <select id="responsable_carroceria" name="responsable_carroceria" class="form-select" required>
                    <option value="">Seleccione responsable</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            <button class="btn btn-custom" type="button" id="btnSiguiente">
                Siguiente <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div> <div id="seccionDetalles" style="display: none;">
        <h4 class="mb-4"><i class="fas fa-boxes"></i> Detalles de Contenedores</h4>
        
        <div id="contenedor-detalles"></div>

        <div class="d-flex justify-content-center mt-4">
            <button class="btn btn-outline-secondary me-3" type="button" id="btnAnterior">
                <i class="fas fa-arrow-left"></i> Anterior
            </button>
            <button class="btn btn-custom" type="submit">
                <i class="fas fa-save"></i> Guardar Carrocería
            </button>
        </div>
    </div> </form>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>
    <script src="/assets/js/alertas.js"></script>

    <script src="/assets/js/carrocerias.js"></script>
</body>

</html>