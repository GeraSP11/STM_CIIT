<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Usuarios</title>
    <style>
        /* RESET Y ESTILOS GENERALES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #D9D9D9;
            color: #000000;
        }

        /* HEADER PRINCIPAL */
        .main-header {
            background: #541C33;
            color: #FFFFFF;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-header h1 {
            font-size: 1.8rem;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #541C33;
        }

        /* BREADCRUMB*/
        .breadcrumb {
            background: #FFFFFF;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid #949494;
        }

        .breadcrumb a {
            color: #4B0000;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .breadcrumb span {
            color: #7E7B7B;
        }

        /* SUBHEADER*/
        .subheader {
            background: #FFFFFF;
            padding: 2.5rem 2rem;
            text-align: center;
            border-bottom: 1px solid #949494;
        }

        .subheader h2 {
            font-size: 1.5rem;
            font-weight: 400;
            color: #843409;
            letter-spacing: 1px;
        }

        /* CONTENEDOR PRINCIPAL*/
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
        }

        /* SECCIÓN DE BÚSQUEDA */
        .search-section {
            padding: 3rem 2rem 2rem 2rem;
            background: #FFFFFF;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ==========================
           GRUPOS DE FORMULARIO
           ========================== */
        .form-group {
            flex: 1;
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #000000;
            font-size: 0.95rem;
            background: #541C33;
            color: #FFFFFF;
            padding: 0.5rem 1rem;
        }

        .required {
            color: #FFFFFF;
            font-weight: 700;
        }

        .helper-text {
            display: block;
            font-size: 0.85rem;
            color: #7E7B7B;
            margin-top: 0.3rem;
        }

        /* ==========================
           CAMPOS DEL FORMULARIO
           ========================== */
        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #949494;
            border-radius: 0;
            font-size: 1rem;
            background: #FFFFFF;
            color: #000000;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #541C33;
        }

        input[type="text"]:disabled,
        input[type="number"]:disabled,
        input[type="email"]:disabled,
        input[type="password"]:disabled,
        select:disabled {
            background-color: #ffffffff;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* ==========================
           BOTONES
           ========================== */
        .btn {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 0;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
            font-family: inherit;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-primary {
            background: #541C33;
            color: #FFFFFF;
        }

        .btn-primary:hover:not(:disabled) {
            background: #4B0000;
        }

        .btn-submit {
            background: #541C33;
            color: #FFFFFF;
        }

        .btn-submit:hover:not(:disabled) {
            background: #4B0000;
        }

        .btn-reset {
            background: #7E7B7B;
            color: #FFFFFF;
        }

        .btn-reset:hover:not(:disabled) {
            background: #949494;
        }

        .btn-cancel {
            background: #949494;
            color: #FFFFFF;
        }

        .btn-cancel:hover:not(:disabled) {
            background: #7E7B7B;
        }

        /* ==========================
           SECCIÓN DEL FORMULARIO
           ========================== */
        .form-section {
            padding: 3rem 2rem;
            background: #FFFFFF;
            display: none;
        }

        .form-section.active {
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

        /* ==========================
           INFO BOX (Personal Asociado)
           ========================== */
        .info-box {
            background: #D9D9D9;
            border: 2px solid #541C33;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 0;
        }

        .info-box h3 {
            color: #541C33;
            margin-bottom: 1rem;
            font-size: 1rem;
            font-weight: 500;
        }

        .info-box p {
            color: #000000;
            margin: 0.5rem 0;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .info-box strong {
            color: #4B0000;
            font-weight: 600;
        }

        /* ==========================
           BOTONES DE ACCIÓN
           ========================== */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            padding-top: 2rem;
            border-top: 2px solid #541C33;
            margin-top: 2rem;
        }

        /* ==========================
           ALERTAS
           ========================== */
        .alert {
            padding: 1rem 1.5rem;
            margin: 0 2rem 1.5rem 2rem;
            border-radius: 0;
            font-size: 0.95rem;
            display: none;
            border: 2px solid #541C33;
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
            background: #DEC26F;
            color: #4B0000;
            border-color: #843409;
        }

        .alert-error {
            background: #4B0000;
            color: #FFFFFF;
            border-color: #541C33;
        }

        .alert-info {
            background: #D9D9D9;
            color: #000000;
            border-color: #949494;
        }

        /* ==========================
           ESTADO DE CARGA
           ========================== */
        .loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 30px;
            height: 30px;
            margin: -15px 0 0 -15px;
            border: 3px solid #D9D9D9;
            border-top: 3px solid #541C33;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ==========================
           RESPONSIVO
           ========================== */
        @media (max-width: 768px) {
            .main-header h1 {
                font-size: 1.3rem;
            }

            .logo {
                width: 50px;
                height: 50px;
            }

            .subheader {
                padding: 2rem 1rem;
            }

            .subheader h2 {
                font-size: 1.2rem;
            }

            .search-section {
                padding: 2rem 1rem;
            }

            .search-box {
                flex-direction: column;
                align-items: stretch;
            }

            .form-section {
                padding: 2rem 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .alert {
                margin: 0 1rem 1rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <h1>Gestión de usuarios</h1>
        <div class="logo">LOGO</div>
    </header>

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="#">🏠</a>
        <span>></span>
        <span>Actualización de usuario</span>
    </nav>

    <!-- Subheader -->
    <section class="subheader">
        <h2>Actualización de usuario</h2>
    </section>

    <div class="container">
        <!-- Sección de Búsqueda -->
        <section class="search-section">
            <div class="search-box">
                <div class="form-group">
                    <label for="curp_usuario">
                        Clave: <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="curp_usuario" 
                        placeholder="Clave de Identificación de Personal"
                        maxlength="18"
                        style="text-transform: uppercase;"
                        required>
                    <small class="helper-text">*Campos obligatorios</small>
                </div>
                <button type="button" class="btn btn-primary" onclick="buscarUsuario()">
                    Consultar
                </button>
            </div>
        </section>
    </div>

    <!-- Mensaje de alerta -->
    <div id="alertMessage" class="alert"></div>

    <!-- Formulario de Actualización (Inicialmente oculto) -->
    <section id="formSection" class="form-section">
        <form id="updateForm">
            <!-- Información del Usuario Encontrado -->
            <div class="info-box">
                <h3>Información del Usuario</h3>
                <p><strong>ID:</strong> <span id="info_id">-</span></p>
                <p><strong>Nombre de Usuario:</strong> <span id="info_nombre_usuario">-</span></p>
                <p><strong>Personal Asociado:</strong> <span id="info_personal">-</span></p>
                <p><strong>CURP:</strong> <span id="info_curp">-</span></p>
            </div>

            <!-- Campos Editables -->
            <div class="form-group">
                <label for="correo_electronico">
                    Correo Electrónico <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="correo_electronico" 
                    name="correo_electronico"
                    placeholder="ejemplo@correo.com"
                    required>
            </div>

            <div class="form-group">
                <label for="identificador_de_rh">
                    Personal Asociado <span class="required">*</span>
                </label>
                <select id="identificador_de_rh" name="identificador_de_rh" required>
                    <option value="">Seleccione una persona</option>
                </select>
            </div>

            <div class="form-group">
                <label for="contrasena">Nueva Contraseña</label>
                <input 
                    type="password" 
                    id="contrasena" 
                    name="contrasena"
                    placeholder="Dejar en blanco para mantener actual"
                    minlength="6">
                <small class="helper-text">Mínimo 6 caracteres. Opcional.</small>
            </div>

            <div class="form-group">
                <label for="contrasena_confirmar">Confirmar Contraseña</label>
                <input 
                    type="password" 
                    id="contrasena_confirmar"
                    placeholder="Confirme la nueva contraseña"
                    minlength="6">
            </div>

            <!-- Botones de Acción -->
            <div class="form-actions">
                <button type="submit" class="btn btn-submit">Actualizar</button>
                <button type="button" class="btn btn-reset" onclick="limpiarFormulario()">Limpiar</button>
                <button type="button" class="btn btn-cancel" onclick="cancelar()">Cancelar</button>
            </div>
        </form>
    </section>

    <script src="./js/actualizarUsuarios.js"></script>
</body>
</html>