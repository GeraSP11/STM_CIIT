<?php
require_once __DIR__ . "/../config/conexion.php";

class LoginModel
{

    public function buscarUsuarioPorCredenciales($nombre_usuario, $email)
    {
        global $pdo;

        // Buscar usuario con JOIN a personal para obtener información completa
        $sql = "SELECT 
                    u.id_usuario,
                    u.nombre_usuario,
                    u.contrasena,
                    u.correo_electronico,
                    u.identificador_de_rh,
                    p.nombre_personal,
                    p.apellido_paterno,
                    p.apellido_materno,
                    p.cargo
                FROM usuarios u
                INNER JOIN personal p ON u.identificador_de_rh = p.id_personal
                WHERE u.nombre_usuario = ? AND u.correo_electronico = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_usuario, $email]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si existe, devuelve el registro completo, si no, devuelve null
        return $resultado ? $resultado : null;
    }
}