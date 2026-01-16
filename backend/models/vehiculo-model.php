<?php
require_once __DIR__ . "/../config/conexion.php";

class VehiculoModel
{
    /**
     * Verifica si una clave de vehículo ya existe.
     * Basado en ubicacionGeoExiste de localidades.
     */
    public function claveExiste($clave)
    {
        global $pdo;
        $sql = "SELECT id_vehiculo FROM vehiculos WHERE clave_vehiculo = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clave]);

        return ($stmt->rowCount() > 0);
    }

    /**
     * Registra un nuevo vehículo con los campos técnicos de la NOM-012.
     */
    public function registrarVehiculo(
        $clave,
        $modalidad,
        $descripcion,
        $chofer,
        $clase,
        $nomenclatura,
        $ejes,
        $llantas,
        $peso
    ) {
        global $pdo;

        $sql = "INSERT INTO vehiculos 
            (clave_vehiculo, modalidad_vehiculo, descripcion_vehiculo, chofer_asignado, clase, nomenclatura, numero_de_ejes, numero_de_llantas, peso_bruto_vehicular)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $clave,
            $modalidad,
            $descripcion,
            $chofer,
            $clase,
            $nomenclatura,
            $ejes,
            $llantas,
            $peso
        ]);
    }

    /**
     * Consulta vehículos con filtros dinámicos (Clave, Modalidad, Nomenclatura).
     * Sigue la estructura lógica de consultarLocalidad.
     */
    public function consultarVehiculos($clave = null, $modalidad = null, $nomenclatura = null)
    {
        global $pdo;

        $sql = "SELECT v.*, p.nombre_personal, p.apellido_paterno 
                FROM vehiculos v
                LEFT JOIN personal p ON v.chofer_asignado = p.id_personal";
        
        $conditions = [];
        $params = [];

        if ($clave) {
            $conditions[] = "v.clave_vehiculo ILIKE :clave";
            $params['clave'] = "%$clave%";
        }

        if ($modalidad) {
            $conditions[] = "v.modalidad_vehiculo = :modalidad";
            $params['modalidad'] = $modalidad;
        }

        if ($nomenclatura) {
            $conditions[] = "v.nomenclatura ILIKE :nomenclatura";
            $params['nomenclatura'] = "%$nomenclatura%";
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        try {
            $stmt = $pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Error en la consulta: ' . $e->getMessage()];
        }
    }

    /**
     * Busca vehículos para el datalist de actualización.
     */
    public function buscarVehiculos($texto)
    {
        global $pdo;
        $sql = "SELECT * FROM vehiculos WHERE clave_vehiculo ILIKE :texto OR descripcion_vehiculo ILIKE :texto LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':texto', "%$texto%");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un vehículo por su clave única.
     */
    public function obtenerVehiculoPorClave($clave)
    {
        global $pdo;
        $sql = "SELECT * FROM vehiculos WHERE clave_vehiculo = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clave]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de un vehículo existente.
     */
    public function actualizarVehiculo($id, $clave, $modalidad, $descripcion, $chofer, $clase, $nomenclatura, $ejes, $llantas, $peso)
    {
        global $pdo;
        $sql = "UPDATE vehiculos SET 
                clave_vehiculo = ?, modalidad_vehiculo = ?, descripcion_vehiculo = ?, 
                chofer_asignado = ?, clase = ?, nomenclatura = ?, 
                numero_de_ejes = ?, numero_de_llantas = ?, peso_bruto_vehicular = ?
                WHERE id_vehiculo = ?";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$clave, $modalidad, $descripcion, $chofer, $clase, $nomenclatura, $ejes, $llantas, $peso, $id]);
    }

    /**
     * Obtiene datos para la vista de eliminación por ID.
     */
    public function obtenerDatosVehiculoPorId($id)
    {
        global $pdo;
        $sql = "SELECT * FROM vehiculos WHERE id_vehiculo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene datos para la vista de eliminación por Clave.
     */
    public function obtenerDatosVehiculoPorClave($clave)
    {
        global $pdo;
        $sql = "SELECT * FROM vehiculos WHERE clave_vehiculo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clave]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un vehículo verificando si está siendo usado en fletes.
     */
    public function eliminarVehiculo($id)
    {
        global $pdo;

        // Validación de integridad: ¿Está el vehículo en la tabla 'fleteros'?
        $checkSql = "SELECT id_fletero FROM fleteros WHERE identificador_vehiculo = ? LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() > 0) {
            return "ERROR_DEPENDENCIAS";
        }

        $sql = "DELETE FROM vehiculos WHERE id_vehiculo = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}