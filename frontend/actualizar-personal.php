
<?php
$page_title = 'Actualizar Personal';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Personal</title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
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

        .header {
            background: linear-gradient(#4b0000 100%);
            color: white;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 300;
            margin: 0;
        }

        .header-logo {
            width: 100px;
            height: 100px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            padding: 10px;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
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
            background-color: white;
            margin: 40px auto;
            max-width: 1200px;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content h2 {
            color: #8b4513;
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 30px;
            text-align: center;
        }

        .curp-search-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
            justify-content: center;
        }

        .curp-label {
            background-color: #5c2e3e;
            color: white;
            padding: 12px 35px;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 4px;
        }

        .curp-search-section input {
            flex: 0 0 500px;
            padding: 12px 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .curp-search-section input::placeholder {
            color: #999;
        }

        .update-form {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #333;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: red;
        }

        .form-group input,
        .form-group select {
            padding: 10px 15px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            font-size: 0.95rem;
            background-color: #e8e8e8;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #5c2e3e;
            background-color: white;
        }

        .form-footer {
            color: #999;
            font-size: 0.9rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .form-actions {
            display: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Gestión de personal</h1>
        <div class="header-logo">
            <img src="/assets/img/logo_principal.jpeg" alt="Corredor Interoceánico">
        </div>
    </div>
    <div class="breadcrumb-nav">
        <i class="bi bi-house-door-fill"></i>
        <span>></span>
        <span>Actualizar Personal</span>
    </div>
    <main class="main-content">
        <h2>Actualizar Registro de Personal</h2>

        <div class="curp-search-section">
            <div class="curp-label">CURP</div>
            <input type="text" id="curp_busqueda" maxlength="18" placeholder="Escribe la CURP">
        </div>

        <form action="#" method="post" class="update-form" id="updateForm">
            <input type="hidden" id="id_personal" name="id_personal" value="">

            <div class="form-grid">
                <div class="form-group">
                    <label for="curp">CURP (Editable): <span class="required">*</span></label>
                    <input type="text" id="curp" name="curp" maxlength="18" value="" placeholder="CURP del personal"
                        required>
                </div>

                <div class="form-group">
                    <label for="nombre_personal">Nombre: <span class="required">*</span></label>
                    <input type="text" id="nombre_personal" name="nombre_personal" value="" required>
                </div>

                <div class="form-group">
                    <label for="apellido_paterno">Apellido Paterno: <span class="required">*</span></label>
                    <input type="text" id="apellido_paterno" name="apellido_paterno" value="" required>
                </div>

                <div class="form-group">
                    <label for="apellido_materno">Apellido Materno: <span class="required">*</span></label>
                    <input type="text" id="apellido_materno" name="apellido_materno" value="">
                </div>

                <div class="form-group">
                    <label for="afiliacion_laboral">Afiliación Laboral<span class="required">*</span></label>
                    <select id="afiliacion_laboral" name="afiliacion_laboral" required>
                        <option value="">Seleccione una opción</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cargo">Cargo: <span class="required">*</span></label>
                    <select id="cargo" name="cargo" required>
                        <option value="">Seleccione un cargo</option>
                        <option value="Autoridad">Autoridad</option>
                        <option value="Administrador del TMS">Administrador del TMS</option>
                        <option value="Operador Logístico">Operador Logístico</option>
                        <option value="Cliente">Cliente</option>
                        <option value="Jefe de Almacén">Jefe de Almacén</option>
                    </select>
                </div>
            </div>

            <div class="form-footer">
                *Campos obligatorios
            </div>

            <div class="form-actions">
                <button type="submit">Actualizar</button>
                <button type="reset">Limpiar</button>
                <button type="button" onclick="history.back()">Cancelar</button>
            </div>
        </form>
    </main>
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./js/actualizarEliminarPersonal.js"></script>
</body>

</html>