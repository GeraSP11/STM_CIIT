<?php
require_once __DIR__ . "/../config/conexion.php";

class UsuariosModel
{

    public function registrarUsuario($usuario, $correo, $clave_personal, $pass)
    {
        global $pdo;

        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre_usuario, contrasena, correo_electronico, identificador_de_rh)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$usuario, $pass, $correo, $clave_personal]);
    }

    public function validarCorreoUsuario($correo)
    {
        global $pdo;

        $sql = "SELECT id_usuario FROM usuarios WHERE correo_electronico = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si existe, devuelve el ID, si no, devuelve null
        return $resultado ? $resultado['id_usuario'] : null;
    }


    public function consultarIDCalaveParaUsuario($clave_personal)
    {
        global $pdo;

        $sql = "SELECT id_personal FROM personal WHERE curp = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clave_personal]);

        // Obtener el registro
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si existe, devuelve el id_personal, si no, devuelve null
        return $resultado ? $resultado['id_personal'] : null;
    }

    public function validarClavePersonalUsuario($id_personal)
    {
        global $pdo;

        $sql = "SELECT id_usuario FROM usuarios WHERE identificador_de_rh = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_personal]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['id_usuario'] : null;
    }
}
