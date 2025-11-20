<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuarios</title>
    <style>
        /* ==========================
           RESET Y ESTILOS GENERALES
           ========================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #FFFFFF;
            color: #000000;
        }

        /* ==========================
           HEADER PRINCIPAL
           ========================== */
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
            font-size: 0.7rem;
        }

        /* ==========================
           BREADCRUMB
           ========================== */
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

        /* ==========================
           SUBHEADER
           ========================== */
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

        /* ==========================
           CONTENEDOR PRINCIPAL
           ========================== */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0;
        }

        /* ==========================
           SECCIÓN DE BÚSQUEDA
           ========================== */
        .search-section {
            padding: 3rem 2rem 2rem 2rem;
            background: #D9D9D9;
            text-align: center;
        }

        .search-box {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            align-items: center;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ==========================
           GRUPOS DE FORMULARIO
           ========================== */
        .form-group {
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #FFFFFF;
            font-size: 0.95rem;
            background: #541C33;
            padding: 0.5rem 1rem;
            text-align: left;
        }

        .required {
            color: #FFFFFF;
            font-weight: 700;
        }

        .helper-text {
            display: none;
        }

        /* ==========================
           CAMPOS DEL FORMULARIO
           ========================== */
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #949494;
            border-radius: 0;
            font-size: 1rem;
            background: #FFFFFF;
            color: #7E7B7B;
            font-family: inherit;
            transition: border-color 0.3s;
            text-align: center;
        }

        input[type="text"] {
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #541C33;
        }

        input[type="text"]:disabled,
        input[type="number"]:disabled {
            background-color: #D9D9D9;
            cursor: not-allowed;
            opacity: 0.6;
        }

        input[type="text"]::placeholder {
            text-transform: none;
            letter-spacing: normal;
        }

        /* ==========================
           BOTONES
           ========================== */
        .btn {
            padding: 0.7rem 2rem;
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

        .btn-danger {
            background: #4B0000;
            color: #FFFFFF;
        }

        .btn-danger:hover:not(:disabled) {
            background: #cc0000;
        }

        .btn-secondary {
            background: #7E7B7B;
            color: #FFFFFF;
        }

        .btn-secondary:hover:not(:disabled) {
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
           SECCIÓN DE INFORMACIÓN
           ========================== */
        .user-info-section {
            padding: 3rem 2rem;
            background: #FFFFFF;
            display: none;
        }

        .user-info-section.active {
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
           INFO BOX
           ========================== */
        .info-box {
            background: #D9D9D9;
            border: 2px solid #541C33;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 0;
        }

        .info-box h3 {
            color: #541C33;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .info-item {
            padding: 0.75rem;
            background: #FFFFFF;
            border: 1px solid #949494;
        }

        .info-item strong {
            display: block;
            color: #4B0000;
            font-weight: 600;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .info-item span {
            color: #000000;
            font-size: 0.95rem;
        }

        .info-item span.curp-display {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            font-weight: 500;
        }

        /* ==========================
           WARNING BOX
           ========================== */
        .warning-box {
            background: #4B0000;
            color: #FFFFFF;
            border: 2px solid #541C33;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .warning-box p {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .warning-box strong {
            font-weight: 700;
        }

        /* ==========================
           BOTONES DE ACCIÓN
           ========================== */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            padding-top: 1.5rem;
        }

        /* ==========================
           MODAL DE CONFIRMACIÓN
           ========================== */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            animation: fadeIn 0.3s ease-in;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #FFFFFF;
            border: 3px solid #541C33;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content h3 {
            font-size: 1.3rem;
            font-weight: 500;
            margin-bottom: 1rem;
            text-align: center;
            color: #541C33;
        }

        .modal-content p {
            font-size: 1rem;
            margin-bottom: 1rem;
            text-align: center;
            line-height: 1.6;
            color: #000000;
        }

        .modal-warning {
            color: #4B0000;
            font-weight: 600;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
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
                font-size: 0.6rem;
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

            .user-info-section {
                padding: 2rem 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .form-actions,
            .modal-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .alert {
                margin: 0 1rem 1rem 1rem;
            }

            .modal-content {
                padding: 1.5rem;
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <h1>Gestión de Usuario</h1>
        <div class="logo">CORREDOR<br>INTEROCEÁNICO</div>
    </header>

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="#">🏠</a>
        <span>></span>
        <span>Eliminar Usuario</span>
    </nav>

    <!-- Subheader -->
    <section class="subheader">
        <h2>Eliminar Usuario</h2>
    </section>

    <div class="container">
        <!-- Sección de Búsqueda -->
        <section class="search-section">
            <div class="search-box">
                <div class="form-group">
                    <label for="curp_usuario">
                        Filtro de búsqueda: <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="curp_usuario" 
                        placeholder="GOML920715HMCLRS08"
                        maxlength="18"
                        required>
                </div>
                <button type="button" class="btn btn-primary" id="btnBuscar">
                    Buscar
                </button>
            </div>
        </section>

        <!-- Mensaje de alerta -->
        <div id="alertMessage" class="alert"></div>

        <!-- Información del Usuario (Inicialmente oculto) -->
        <section id="userInfoSection" class="user-info-section">
            <div class="info-box">
                <h3>Información del Usuario a Eliminar</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>ID:</strong> <span id="info_id">-</span>
                    </div>
                    <div class="info-item">
                        <strong>Nombre de Usuario:</strong> <span id="info_nombre_usuario">-</span>
                    </div>
                    <div class="info-item">
                        <strong>Correo Electrónico:</strong> <span id="info_correo">-</span>
                    </div>
                    <div class="info-item">
                        <strong>CURP:</strong> <span id="info_curp" class="curp-display">-</span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <strong>Personal Asociado:</strong> <span id="info_personal">-</span>
                    </div>
                </div>
            </div>

            <!-- Advertencia -->
            <div class="warning-box">
                <p><strong>ADVERTENCIA:</strong> Esta acción es irreversible. El usuario será eliminado permanentemente del sistema.</p>
            </div>

            <!-- Botones de Acción -->
            <div class="form-actions">
                <button type="button" class="btn btn-danger" id="btnEliminar">
                    Eliminar Usuario
                </button>
                <button type="button" class="btn btn-cancel" id="btnCancelar">
                    Cancelar
                </button>
            </div>
        </section>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Confirmar Eliminación</h3>
            <p>¿Está seguro que desea eliminar al usuario <strong id="modal_nombre_usuario"></strong>?</p>
            <p class="modal-warning">Esta acción no se puede deshacer.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    Confirmar Eliminación
                </button>
                <button type="button" class="btn btn-secondary" id="btnCancelarModal">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <script src="js/eliminarUsuario.js"></script>
</body>
</html>