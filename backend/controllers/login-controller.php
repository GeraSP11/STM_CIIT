<?php
require_once __DIR__ . "/../models/login-model.php";

class LoginController
{
    public function procesarLogin($data)
    {
        $nombre_usuario = trim($data['nombre_usuario'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        // Validaciones básicas
        if (empty($nombre_usuario) || empty($email) || empty($password)) {
            return "Todos los campos son obligatorios.";
        }

        $model = new LoginModel();

        // Buscar usuario por nombre de usuario y email
        $usuario = $model->buscarUsuarioPorCredenciales($nombre_usuario, $email);

        if ($usuario === null) {
            return "Usuario o correo electrónico incorrecto.";
        }

        // COMPARACIÓN DIRECTA (para contraseñas en texto plano)
        if ($password === $usuario['contrasena']) {
            // Login exitoso - guardar datos en sesión
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['correo_electronico'] = $usuario['correo_electronico'];
            $_SESSION['id_personal'] = $usuario['identificador_de_rh'];
            $_SESSION['cargo'] = $usuario['cargo'];
            $_SESSION['nombre_completo'] = trim($usuario['nombre_personal'] . ' ' . 
                                                  $usuario['apellido_paterno'] . ' ' . 
                                                  $usuario['apellido_materno']);
            
            return "OK";
        } else {
            return "Contraseña incorrecta.";
        }
    }
}
?>