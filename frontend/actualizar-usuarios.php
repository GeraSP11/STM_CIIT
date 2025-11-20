<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Usuarios';
$seccion = 'Consulta de Usuarios';
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f5f5;
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
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .main-content h2 {
            color: #8b4513;
            font-size: 1.8rem;
            font-weight: 400;
            padding: 30px 0;
            text-align: center;
            background-color: white;
        }
        
        .search-section {
            background-color: #f5f5f5;
            padding: 40px;
            margin-bottom: 0;
        }
        
        .search-box {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }
        
        .search-title {
            background-color: #5c2e3e;
            color: white;
            padding: 10px 20px;
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 30px;
            display: inline-block;
        }
        
        .search-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .search-label {
            color: #333;
            font-size: 0.95rem;
            font-weight: 600;
        }
        
        .search-input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            text-transform: uppercase;
        }
        
        .search-input::placeholder {
            color: #999;
            text-transform: none;
        }
        
        .search-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }
        
        .search-button {
            background-color: #5c2e3e;
            color: white;
            padding: 10px 35px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .search-button:hover {
            background-color: #4b0000;
        }
        
        .search-hint {
            color: #999;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .update-form {
            background-color: white;
            padding: 40px;
            margin: 40px auto;
            max-width: 1200px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
        }
        
        .update-form.active {
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
        
        .info-box {
            background-color: #e8e8e8;
            border: 2px solid #5c2e3e;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .info-box h3 {
            color: #5c2e3e;
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .info-box p {
            color: #333;
            margin: 8px 0;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .info-box strong {
            color: #4b0000;
            font-weight: 600;
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
        
        .form-group.full-width {
            grid-column: 1 / -1;
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
        
        .form-group input:disabled,
        .form-group select:disabled {
            background-color: #d0d0d0;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .helper-text {
            color: #999;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .form-footer {
            color: #999;
            font-size: 0.9rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #5c2e3e;
        }
        
        .btn {
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-primary {
            background-color: #5c2e3e;
            color: white;
        }
        
        .btn-primary:hover:not(:disabled) {
            background-color: #4b0000;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover:not(:disabled) {
            background-color: #5a6268;
        }
        
        .btn-cancel {
            background-color: #999;
            color: white;
        }
        
        .btn-cancel:hover:not(:disabled) {
            background-color: #777;
        }
        
        .alert {
            padding: 15px 20px;
            margin: 20px auto;
            max-width: 1200px;
            border-radius: 4px;
            font-size: 0.95rem;
            display: none;
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
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        @media (max-width: 1024px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .header-logo {
                width: 70px;
                height: 70px;
            }
            
            .breadcrumb-nav {
                padding: 15px 20px;
            }
            
            .main-content {
                margin: 20px;
                padding: 20px;
            }
            
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input {
                flex: 1;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb -->
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

    <main class="main-content">
        <h2>Actualizar Usuarios</h2>
        
        <div class="search-section">
            <div class="search-box">
                <div class="search-title">Actualización de Usuarios:</div>
                
                <div class="search-container">
                    <div class="search-input-group">
                        <label class="search-label">CURP del Usuario <span style="color: red;">*</span></label>
                        <input 
                            type="text" 
                            id="curp_busqueda" 
                            class="search-input"
                            maxlength="18" 
                            placeholder="Ingrese la CURP del usuario">
                        <div class="search-hint">Presione ENTER o clic en Buscar</div>
                    </div>
                    
                    <div class="search-actions">
                        <button type="button" class="search-button" onclick="buscarUsuario()">
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="alertMessage" class="alert"></div>
        
        <form id="updateForm" class="update-form">
            <input type="hidden" id="id_usuario" name="id_usuario" value="">
            
            <div class="info-box">
                <h3>Información del Usuario</h3>
                <p><strong>ID:</strong> <span id="info_id">-</span></p>
                <p><strong>Nombre de Usuario:</strong> <span id="info_nombre_usuario">-</span></p>
                <p><strong>Personal Asociado:</strong> <span id="info_personal">-</span></p>
                <p><strong>CURP:</strong> <span id="info_curp">-</span></p>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="curp">CURP (Editable): <span class="required">*</span></label>
                    <input type="text" id="curp" name="curp" maxlength="18" required>
                </div>
                
                <div class="form-group">
                    <label for="nombre_usuario">Nombre de Usuario: <span class="required">*</span></label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" required>
                </div>
                
                <div class="form-group">
                    <label for="correo_electronico">Correo Electrónico: <span class="required">*</span></label>
                    <input type="email" id="correo_electronico" name="correo_electronico" placeholder="ejemplo@correo.com" required>
                </div>
                
                <div class="form-group">
                    <label for="identificador_de_rh">Personal Asociado: <span class="required">*</span></label>
                    <select id="identificador_de_rh" name="identificador_de_rh" required>
                        <option value="">Seleccione una persona</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="contrasena">Nueva Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" minlength="6" placeholder="Dejar en blanco para mantener actual">
                    <small class="helper-text">Mínimo 6 caracteres. Opcional.</small>
                </div>
                
                <div class="form-group">
                    <label for="contrasena_confirmar">Confirmar Contraseña:</label>
                    <input type="password" id="contrasena_confirmar" placeholder="Confirme la nueva contraseña" minlength="6">
                </div>
            </div>
            
            <div class="form-footer">
                *Campos obligatorios
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <button type="reset" class="btn btn-secondary" onclick="limpiarFormulario()">Limpiar</button>
                <button type="button" class="btn btn-cancel" onclick="cancelar()">Cancelar</button>
            </div>
        </form>
    </main>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./js/actualizarUsuarios.js"></script>
</body>
</html>