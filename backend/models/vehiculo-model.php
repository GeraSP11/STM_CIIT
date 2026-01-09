<?php
require_once __DIR__ . "/../config/conexion.php";

class VehiculoModel
{
    // Verificar si la placa ya existe
    public function placaExiste($placas)
    {
        global $pdo;
        $sql = "SELECT id_vehiculo FROM vehiculos WHERE placas = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$placas]);

        return $stmt->rowCount() > 0;
    }

    // Registrar un nuevo vehículo
    public function registrarVehiculo($placas, $marca, $modelo, $capacidad, $tipo, $idCarroceria)
    {
        global $pdo;
        $sql = "INSERT INTO vehiculos (placas, marca, modelo, capacidad_carga, tipo_vehiculo, id_carroceria) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$placas, $marca, $modelo, $capacidad, $tipo, $idCarroceria]);
    }

    // Consultar con filtros dinámicos
    public function consultarVehiculos($placas = null, $marca = null, $modelo = null, $tipo = null)
    {
        global $pdo;

        $sql = "SELECT id_vehiculo, placas, marca, modelo, capacidad_carga, tipo_vehiculo 
                FROM vehiculos WHERE 1=1";
        $params = [];

        if (!empty($placas)) {
            $sql .= " AND placas ILIKE :placas";
            $params[':placas'] = "%$placas%";
        }
        if (!empty($marca)) {
            $sql .= " AND marca ILIKE :marca";
            $params[':marca'] = "%$marca%";
        }
        if (!empty($modelo)) {
            $sql .= " AND modelo ILIKE :modelo";
            $params[':modelo'] = "%$modelo%";
        }
        if (!empty($tipo)) {
            $sql .= " AND tipo_vehiculo ILIKE :tipo";
            $params[':tipo'] = "%$tipo%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar para autocompletado (datalist)
    public function buscarPorPlaca($texto)
    {
        global $pdo;
        $sql = "SELECT id_vehiculo, placas, marca, modelo, capacidad_carga, tipo_vehiculo, id_carroceria
                FROM vehiculos 
                WHERE placas ILIKE :texto 
                ORDER BY placas ASC LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':texto' => "%$texto%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un vehículo específico por placa (para validaciones)
    public function obtenerVehiculoPorPlaca($placas)
    {
        global $pdo;
        $sql = "SELECT id_vehiculo FROM vehiculos WHERE placas = :placas LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":placas" => $placas]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar vehículo
    public function actualizarVehiculo($id, $placas, $marca, $modelo, $capacidad, $tipo, $carroceria)
    {
        global $pdo;
        $sql = "UPDATE vehiculos SET 
                    placas = :placas, 
                    marca = :marca, 
                    modelo = :modelo, 
                    capacidad_carga = :capacidad, 
                    tipo_vehiculo = :tipo, 
                    id_carroceria = :carroceria 
                WHERE id_vehiculo = :id";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':placas' => $placas,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':capacidad' => $capacidad,
            ':tipo' => $tipo,
            ':carroceria' => $carroceria,
            ':id' => $id
        ]);
    }

    // Buscar datos para la vista previa de eliminación
    public function obtenerDatosVehiculo($filtros)
    {
        global $pdo;
        $sql = "SELECT * FROM vehiculos";
        $params = [];
        $conditions = [];

        if (!empty($filtros['placas'])) {
            $conditions[] = "placas ILIKE :placas";
            $params['placas'] = "%" . $filtros['placas'] . "%";
        }
        if (!empty($filtros['id'])) {
            $conditions[] = "id_vehiculo = :id";
            $params['id'] = $filtros['id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar vehículo con gestión de integridad referencial
    public function eliminarVehiculo($id)
    {
        global $pdo;
        try {
            $pdo->beginTransaction();

            // Eliminar registros que dependen del vehículo (ejemplo: fleteros)
            // Ajusta los nombres de las tablas según tu base de datos real
            $pdo->prepare("DELETE FROM fleteros WHERE id_vehiculo = ?")->execute([$id]);
            
            // Finalmente eliminar el vehículo
            $stmt = $pdo->prepare("DELETE FROM vehiculos WHERE id_vehiculo = ?");
            $stmt->execute([$id]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            // Si el error es por llave foránea en otras tablas no contempladas
            if ($e->getCode() == '23503') { 
                return "ERROR_DEPENDENCIAS";
            }
            return false;
        }
    }
}