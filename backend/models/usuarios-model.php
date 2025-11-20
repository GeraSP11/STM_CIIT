<?php
require_once __DIR__ . "/../config/conexion.php";

class UsuariosModel {

    public function registrarUsuario($usuario, $correo, $clave_personal, $passHash) {
        global $pdo;

        // Verificar correo existente
        $sql = "SELECT id_usuario FROM usuarios WHERE correo_electronico = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);

        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre_usuario, contrasena, correo_electronico, identificador_de_rh)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$usuario, $passHash, $correo, $clave_personal]);
    }
}
