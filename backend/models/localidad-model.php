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
    public function consultarLocalidad($nombreCentro = null, $localidad = null, $poblacion = null, $estado = null)
    {
        global $pdo;
       

        $sql = "SELECT nombre_centro_trabajo AS nombre,
               ubicacion_georeferenciada AS ubicacion,
               poblacion,
               localidad,
               estado,
               tipo_instalacion
        FROM localidades
        WHERE 1=1";

        $params = [];

        // Normalizar: convertir strings vacíos a null
        $nombreCentro = !empty($nombreCentro) ? $nombreCentro : null;
        $localidad = !empty($localidad) ? $localidad : null;
        $poblacion = !empty($poblacion) ? $poblacion : null;
        $estado = !empty($estado) ? $estado : null;

        if ($nombreCentro) {
            $sql .= " AND nombre_centro_trabajo ILIKE :nombreCentro";
            $params[':nombreCentro'] = "%$nombreCentro%";
        }

        if ($localidad) {
            $sql .= " AND localidad ILIKE :localidad";
            $params[':localidad'] = "%$localidad%";
        }

        if ($poblacion) {
            $sql .= " AND poblacion ILIKE :poblacion";
            $params[':poblacion'] = "%$poblacion%";
        }

        if ($estado) {
            $sql .= " AND estado ILIKE :estado";
            $params[':estado'] = "%$estado%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}