<!-- Archivo: login.php -->
<?php
$page_title = 'MARINA Corredor Interoceánico';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap local -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #541C33;
            --secondary: #4B0000;
        }

        body {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            min-height: 100vh;
        }

        .login-card {
            animation: fadeInUp 0.6s;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control {
            border: none;
            border-bottom: 2px solid #dee2e6;
            border-radius: 0;
            background: transparent;
        }

        .form-control:focus {
            border-bottom-color: var(--primary);
            box-shadow: none;
            background: transparent;
        }

        .form-floating>label {
            color: #6c757d;
        }

        .form-floating>.form-control:focus~label {
            color: var(--primary);
        }

        .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #6c757d;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(84, 28, 51, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
            background: var(--secondary);
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        #showPassword {
            display: none;
        }

        #showConfirmPassword {
            display: none;
        }

        .link-secondary {
            color: #6c757d;
            transition: color 0.3s;
        }

        .link-secondary:hover {
            color: var(--primary);
        }
    </style>
</head>

<body>
    <?php include('includes/header-login.php'); ?>

    <div class="container d-flex align-items-center justify-content-center py-3"
        style="min-height: calc(100vh - 100px);">
        <div class="login-card bg-white rounded p-5 w-100" style="max-width: 450px;">
            <h1 class="text-center fw-normal mb-4">Registro de Usuario</h1>

            <form action="procesar_login.php" method="POST">
                <div class="form-floating mb-4">
                    <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario"
                        placeholder="Usuario" required pattern="^[a-zA-Z0-9]{3,20}$"
                        title="3-20 caracteres, solo letras y números">
                    <label for="nombre_usuario">Nombre de usuario</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required
                        pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                        title="Introduce un correo electrónico válido">
                    <label for="email">Correo Electrónico</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="text" class="form-control" id="clave_personal" name="clave_personal"
                        placeholder="Clave" required pattern="^[a-zA-Z0-9]{3,20}$"
                        title="18 caracteres, solo letras y números">
                    <label for="clave_personal">Clave de Identificación de Personal</label>
                </div>

                <div class="position-relative mb-4">
                    <input type="checkbox" id="showPassword"
                        onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'">
                    <div class="form-floating">
                        <input type="password" class="form-control pe-5" id="password" name="password"
                            placeholder="Contraseña" required
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                            title="Mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo">
                        <label for="password">Contraseña</label>
                    </div>
                    <label for="showPassword" class="password-toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </label>
                </div>

                <div class="position-relative mb-4">
                    <input type="checkbox" id="showConfirmPassword"
                        onchange="document.getElementById('confirm_password').type = this.checked ? 'text' : 'confirm_password'">
                    <div class="form-floating">
                        <input type="password" class="form-control pe-5" id="confirm_password" name="confirm_password"
                            placeholder="Confirmar Contraseña" required
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                            title="Mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo">
                        <label for="confirm_password">Confirmar contraseña</label>
                    </div>
                    <label for="showConfirmPassword" class="password-toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </label>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">Registrar Usuario</button>
                    <button type="button" class="btn btn-primary w-100 py-3 fw-semibold">Cancelar</button>
                </div>

                <div class="d-flex justify-content-center pt-4 mt-3 border-top">
                    <a href="index.php" class="link-secondary text-decoration-none small">¿Ya tienes cuenta? Iniciar sesión</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS local -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>