<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MARINA Corredor Interoceánico</title>
     <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #541C33 0%, #4B0000 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    width: 100%;
    max-width: 1200px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: #FFFFFF;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    padding: 50px 60px;
    width: 100%;
    max-width: 450px;
    position: relative;
}

.login-box::before {
    content: '';
    position: absolute;
    top: -120px;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    height: 100px;
    background-image: url('path/to/logos.png'); /* Aquí va la ruta de tu logo */
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}

h1 {
    color: #000000;
    font-size: 28px;
    font-weight: 400;
    text-align: center;
    margin-bottom: 40px;
    margin-top: 20px;
}

.input-group {
    position: relative;
    margin-bottom: 30px;
}

.input-group input {
    width: 100%;
    padding: 12px 0;
    font-size: 16px;
    color: #000000;
    border: none;
    border-bottom: 2px solid #D9D9D9;
    outline: none;
    background: transparent;
    transition: border-color 0.3s;
}

.input-group input:focus {
    border-bottom-color: #541C33;
}

.input-group input:focus ~ label,
.input-group input:valid ~ label {
    top: -20px;
    font-size: 12px;
    color: #541C33;
}

.input-group label {
    position: absolute;
    top: 12px;
    left: 0;
    font-size: 16px;
    color: #949494;
    pointer-events: none;
    transition: all 0.3s ease;
}

.input-group:last-of-type {
    position: relative;
}

.input-group .toggle-password {
    position: absolute;
    right: 10px;
    top: 12px;
    cursor: pointer;
    color: #949494;
    transition: color 0.3s;
}

.input-group .toggle-password:hover {
    color: #541C33;
}

.btn-login {
    width: 100%;
    padding: 14px;
    background: #541C33;
    border: none;
    border-radius: 4px;
    color: #FFFFFF;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 30px;
    position: relative;
    overflow: hidden;
}

.btn-login:hover {
    background: #4B0000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(84, 28, 51, 0.3);
}

.btn-login:active {
    transform: translateY(0);
}

.btn-login .spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 3px solid #FFFFFF;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.btn-login.loading .btn-text {
    visibility: hidden;
}

.btn-login.loading .spinner {
    display: block;
}

@keyframes spin {
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

.footer-links {
    display: flex;
    justify-content: space-between;
    margin-top: 25px;
    padding-top: 20px;
}

.footer-links a {
    color: #7E7B7B;
    font-size: 14px;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #541C33;
    text-decoration: underline;
}

/* Responsivo */
@media (max-width: 768px) {
    .login-box {
        padding: 40px 30px;
        margin-top: 100px;
    }

    h1 {
        font-size: 24px;
    }

    .footer-links {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}

/* Animación de entrada */
.login-box {
    animation: fadeInUp 0.6s ease-out;
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

/* Efectos de validación */
.input-group input:invalid:not(:placeholder-shown) {
    border-bottom-color: #843409;
}

.input-group input:valid:not(:placeholder-shown) {
    border-bottom-color: #DEC26F;
}

/* Estilos para el ícono del ojo */
.input-group .eye-icon {
    width: 20px;
    height: 20px;
    cursor: pointer;
}
 </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>Inicio de sesión</h1>
            <form id="loginForm">
                <div class="input-group">
                    <input type="text" id="nombre_usuario" required>
                    <label for="nombre_usuario">Nombre de usuario</label>
                </div>
                
                <div class="input-group">
                    <input type="email" id="email" required>
                    <label for="email">Correo Electrónico</label>
                </div>
                
                <div class="input-group">
                    <input type="password" id="password" required>
                    <label for="password">Contraseña</label>
                    <span class="toggle-password" onclick="togglePassword()">
                        <svg class="eye-icon" id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>

                <button type="submit" class="btn-login">
                    <span class="btn-text">Entrar</span>
                    <div class="spinner"></div>
                </button>
            </form>
            
            <div class="footer-links">
                <a href="#">¿Olvidaste tu contraseña?</a>
                <a href="registro_usuario.html">Registrarte</a>
            </div>
        </div>
    </div>


</body>
</html>