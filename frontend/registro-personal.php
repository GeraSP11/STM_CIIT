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
                <a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a>
                <!-- casita color vino -->
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
                            <label class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="nombre_personal" name="nombre_personal" required
                                pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ\s\-]+$" placeholder="Ej. José Antonio"
                                title="Solo letras">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno"
                                required pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ\s\-]+$" placeholder="Ej. Pérez"
                                title="Solo letras">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                                pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ\s\-]+$" placeholder="Ej. López"
                                title="Solo letras">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cargo</label>
                            <select class="form-select" id="cargo" name="cargo" required>
                                <option value="">Seleccione un cargo</option>
                                <option value="Autoridad">Autoridad</option>
                                <option value="Administrador del TMS">Administrador del TMS</option>
                                <option value="Operador Logístico">Operador Logístico</option>
                                <option value="Cliente">Cliente</option>
                                <option value="Jefe de Almacén">Jefe de Almacén</option>
                            </select>
                        </div>

                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Afiliación Laboral</label>
                            <select class="form-select" id="afiliacion_laboral" name="afiliacion_laboral" required>
                                <option value="">Seleccione una localidad</option>

                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CURP</label>
                            <input type="text" class="form-control" style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase()" id="curp" name="curp" required
                                maxlength="18"
                                pattern="([A-Z][AEIOUX][A-Z]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)"
                                title="CURP válido de 18 caracteres">
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

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- TU SCRIPT DEL FORMULARIO -->
    <script src="/assets/js/personal.js"></script>

</body>

</html>