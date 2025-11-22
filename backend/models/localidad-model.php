<?php
require_once __DIR__ . "/../config/conexion.php";

class LocalidadlModel
{
    // Verificar localidad duplicada
    public function ubicacionGeoExiste($ubicacionGeo)
    {
        global $pdo;

        $sql = "SELECT id_localidad FROM localidades WHERE ubicacion_georeferenciada = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ubicacionGeo]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function registrarLocalidad(
        $nombreCentro,
        $ubicacionGeo,
        $poblacion,
        $localidad,
        $estado,
        $tipoInstalacion
    ) {
        global $pdo;

        $sql = "INSERT INTO localidades 
            (nombre_centro_trabajo, ubicacion_georeferenciada, poblacion, localidad, estado, tipo_instalacion)
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $nombreCentro,
            $ubicacionGeo,
            $poblacion,
            $localidad,
            $estado,
            $tipoInstalacion
        ]);
    }

}