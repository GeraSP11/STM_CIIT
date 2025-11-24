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

    public function buscarLocalidades($texto)
    {
        global $pdo;
        $texto = trim($texto);
        if (strlen($texto) < 2)
            return [];

        $sql = "SELECT id_localidad, nombre_centro_trabajo AS nombre, localidad, ubicacion_georeferenciada AS ubicacion, 
                       poblacion, estado, tipo_instalacion
                FROM localidades
                WHERE nombre_centro_trabajo ILIKE :texto OR localidad ILIKE :texto
                ORDER BY nombre_centro_trabajo ASC
                LIMIT 10";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':texto' => "%$texto%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarLocalidad($id, $nombreCentro, $ubicacionGeo, $poblacion, $localidad, $estado, $tipoInstalacion)
    {
        global $pdo;

        $sql = "UPDATE localidades SET
                    nombre_centro_trabajo = :nombreCentro,
                    ubicacion_georeferenciada = :ubicacionGeo,
                    poblacion = :poblacion,
                    localidad = :localidad,
                    estado = :estado,
                    tipo_instalacion = :tipoInstalacion
                WHERE id_localidad = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':nombreCentro' => $nombreCentro,
            ':ubicacionGeo' => $ubicacionGeo,
            ':poblacion' => $poblacion,
            ':localidad' => $localidad,
            ':estado' => $estado,
            ':tipoInstalacion' => $tipoInstalacion,
            ':id' => $id
        ]);
    }
public function eliminarLocalidadConDependencias($id)
{
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Eliminar en orden inverso a las dependencias
        $pdo->prepare("DELETE FROM envios WHERE punto_verificacion = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM fleteros WHERE localidad_origen = ? OR localidad_destino = ?")->execute([$id, $id]);
        $pdo->prepare("DELETE FROM pedidos WHERE localidad_origen = ? OR localidad_destino = ?")->execute([$id, $id]);
        $pdo->prepare("DELETE FROM rutas WHERE localidad_origen = ? OR localidad_destino = ?")->execute([$id, $id]);
        $pdo->prepare("DELETE FROM maniobras WHERE localidad = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM carrocerias WHERE localidad_pertenece = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM transacciones_productos WHERE localidad = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM productos WHERE ubicacion_producto = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM personal WHERE afiliacion_laboral = ?")->execute([$id]);
        
        // Finalmente eliminar la localidad
        $pdo->prepare("DELETE FROM localidades WHERE id_localidad = ?")->execute([$id]);
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Localidad y todos sus registros relacionados eliminados correctamente'
        ];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'error' => 'ERROR_BD',
            'message' => 'Error al eliminar: ' . $e->getMessage()
        ];
    }
}
}