<?php
require_once __DIR__ . "/../config/conexion.php";

class PersonalModel
{
    // Verificar CURP duplicada
    public function curpExiste($curp)
    {
        global $pdo;

        $sql = "SELECT id_personal FROM personal WHERE curp = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$curp]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Registrar personal
    public function registrarPersonal($nombre, $apellidoPaterno, $apellidoMaterno, $curp, $cargo, $afiliacionLaboral)
    {
        global $pdo;

        // Verifica si ya existe
        $sql = "SELECT id_personal FROM personal WHERE curp = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$curp]);

        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Insertar
        $sql = "INSERT INTO personal 
                (nombre_personal, apellido_paterno, apellido_materno, curp, cargo, afiliacion_laboral) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $nombre,
            $apellidoPaterno,
            $apellidoMaterno,
            $curp,
            $cargo,
            $afiliacionLaboral
        ]);
    }

    // Traer todas las localidades
    public function obtenerLocalidades()
    {
        global $pdo;

        $sql = "SELECT id_localidad, nombre_centro_trabajo FROM localidades ORDER BY nombre_centro_trabajo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
