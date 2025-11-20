<?php
require_once __DIR__ . "/../models/usuarios-model.php";

class UsuariosController {

    public function registrarUsuario($data) {
        $nombre = trim($data['nombre_usuario']);
        $correo = trim($data['email']);
        $clave_personal = trim($data['clave_personal']);
        $password = trim($data['password']);
        $confirm = trim($data['confirm_password']);

        if ($password !== $confirm) {
            return "Las contraseñas no coinciden";
        }

        // Hash de la contraseña
        //$passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $model = new UsuariosModel();
        $resultado = $model->registrarUsuario($nombre, $correo, $clave_personal, $password);

        return $resultado ? "OK" : "Error al registrar usuario";
    }
}
