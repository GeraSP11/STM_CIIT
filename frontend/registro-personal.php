<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Personal';
$seccion = 'Registro de personal';
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
            padding: 30px;
            border-radius: 8px;
        }

        .btn-custom {
            background-color: #4a1026;
            color: white;
        }

        .btn-custom:hover {
            background-color: #3b0d20;
        }
    </style>
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb con solo la casita -->
    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb" style="padding-left: 15px;">
            <li class="breadcrumb-item">
                <a href="#"><i class="fas fa-home" style="color: #4D2132;"></i></a> <!-- casita color vino -->
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion ?>
            </li>
        </ol>
    </nav>

    <!-- FORMULARIO -->
    <div class="container mt-3">
        <div class="row justify-content-center">
            <!-- Ajuste de ancho con Bootstrap -->
            <div class="col-12 col-md-8 col-lg-6 form-container shadow">

                <h2 class="text-center mb-4" style="color:#4a1026;">Registro de Personal</h2>

                <form action="#" method="POST">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" name="nombres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" name="apellido_paterno" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" name="apellido_materno" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cargo</label>
                            <select class="form-select" name="cargo" required>
                                <option value="">Seleccione un cargo</option>
                                <option value="administrativo">Administrativo</option>
                                <option value="operativo">Operativo</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Afiliación Laboral</label>
                            <select class="form-select" name="localidad" required>
                                <option value="">Seleccione una localidad</option>
                                <option value="localidad1">Localidad 1</option>
                                <option value="localidad2">Localidad 2</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CURP</label>
                            <input type="text" class="form-control" name="curp" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <button type="submit" class="btn btn-custom">Guardar</button>
                        <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
                    </div>



                </form>

            </div>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>