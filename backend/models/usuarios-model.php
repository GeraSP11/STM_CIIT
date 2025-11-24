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

    public function consultarUsuarioPorCriterio($curp)
    {
        global $pdo;

        // 1. Buscar el id del personal por CURP
        $sqlPersonal = "SELECT id_personal, nombre_personal, apellido_paterno, apellido_materno, curp
                    FROM personal
                    WHERE curp = ?";

        $stmt = $pdo->prepare($sqlPersonal);
        $stmt->execute([$curp]);

        $personal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$personal) {
            return false; // No existe el personal con esa CURP
        }

        $idPersonal = $personal['id_personal'];

        // 2. Buscar usuario asociado a ese id_personal
        $sqlUsuario = "SELECT id_usuario, nombre_usuario, correo_electronico, identificador_de_rh
                   FROM usuarios
                   WHERE identificador_de_rh = ?";

        $stmt = $pdo->prepare($sqlUsuario);
        $stmt->execute([$idPersonal]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false; // No existe un usuario ligado a ese id_personal
        }

        // 3. Combinar datos del usuario + personal
        return array_merge($usuario, $personal);
    }
public function eliminarUsuario($curp)
{
    global $pdo;
    
    // 1. Buscar el id_personal por CURP
    $sqlPersonal = "SELECT id_personal FROM personal WHERE curp = ?";
    $stmt = $pdo->prepare($sqlPersonal);
    $stmt->execute([$curp]);
    $personal = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$personal) {
        return false;
    }
    
    // 2. Eliminar usuario asociado (CASCADE eliminará automáticamente por la FK)
    $sqlDelete = "DELETE FROM usuarios WHERE identificador_de_rh = ?";
    $stmt = $pdo->prepare($sqlDelete);
    
    return $stmt->execute([$personal['id_personal']]);
}
}
