<?php
// =====================================================
//  MODELO DE CARROCERÍAS (6.1) - ACTUALIZADO RF-GV-CC-01
//  Interactúa con las tablas 'carrocerias' y 'carrocerias_detalle'
// =====================================================

require_once __DIR__ . "/../config/conexion.php";

class CarroceriaModel
{
    /**
     * Valida si una matrícula ya existe en la base de datos.
     * Retorna true si existe, false si no.
     */
    public function matriculaExiste($matricula)
    {
        global $pdo;
        try {
            $sql = "SELECT COUNT(*) FROM carrocerias WHERE matricula = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([strtoupper(trim($matricula))]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Paso 1: Registrar los datos principales de la carrocería.
     * Retorna el ID generado para ser usado en los detalles.
     */
    public function insertarCarroceria($d)
    {
        global $pdo;
        try {
            // Nota: Se usa RETURNING id_carroceria para obtener el ID en PostgreSQL
            $sql = "INSERT INTO carrocerias 
                    (matricula, localidad_pertenece, responsable_carroceria, numero_contenedores, 
                     peso_vehicular, numero_ejes_vehiculares, tipo_carroceria, estatus_carroceria, modalidad_carroceria)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id_carroceria";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $d['matricula'], 
                $d['localidad'], 
                $d['responsable'], 
                $d['contenedores'], 
                $d['peso'], 
                $d['ejes'], 
                $d['tipo'], 
                $d['estatus'], 
                $d['modalidad']
            ]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['id_carroceria'];
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Paso 2: Registrar los detalles de cada contenedor.
     * Recibe un array asociativo con los datos.
     */
    public function insertarDetalleCarroceria($d)
    {
        global $pdo;
        try {
            $sql = "INSERT INTO carrocerias_detalle 
                    (identificador_carroceria, numero_contenedor, longitud, anchura, altura)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $d['id_carroceria'], 
                $d['numero_contenedor'], // <--- Esto es lo que acabamos de arreglar en el controlador
                $d['longitud'], 
                $d['anchura'], 
                $d['altura']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Consulta carrocerías con filtros dinámicos y JOINs.
     */
    public function listarCarrocerias($filtros = [])
    {
        global $pdo;

        // 1. Base de la consulta con los ALIAS para que el JS los reconozca
        $sql = "SELECT c.*, 
                    l.nombre_centro_trabajo AS nombre_localidad, 
                    (p.nombre_personal || ' ' || p.apellido_paterno) AS nombre_responsable
                FROM carrocerias c
                LEFT JOIN localidades l ON c.localidad_pertenece = l.id_localidad
                LEFT JOIN personal p ON c.responsable_carroceria = p.id_personal
                WHERE 1=1";

        $params = [];

        // 2. Mapeo de filtros dinámicos (Igual que en tu ejemplo de Localidades)
        // Estos nombres ('matricula', 'modalidad', etc.) deben coincidir con el 'name' de tus inputs en el JS
        if (!empty($filtros['matricula'])) {
            $sql .= " AND c.matricula ILIKE :matricula";
            $params[':matricula'] = "%" . $filtros['matricula'] . "%";
        }

        if (!empty($filtros['modalidad_carroceria'])) {
            $sql .= " AND c.modalidad_carroceria = :modalidad";
            $params[':modalidad'] = $filtros['modalidad_carroceria'];
        }

        if (!empty($filtros['estatus_carroceria'])) {
            $sql .= " AND c.estatus_carroceria = :estatus";
            $params[':estatus'] = $filtros['estatus_carroceria'];
        }

        $sql .= " ORDER BY c.matricula ASC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /**
     * CORRECCIÓN: Método para actualizar los datos.
     * Nombre sincronizado con CarroceriaController.
     */
    public function updateCarroceria($data)
    {
        global $pdo;
        try {
            $sql = "UPDATE carrocerias SET
                        matricula = :matricula,
                        localidad_pertenece = :localidad,
                        responsable_carroceria = :responsable,
                        numero_contenedores = :contenedores,
                        peso_vehicular = :peso,
                        numero_ejes_vehiculares = :ejes,
                        tipo_carroceria = :tipo,
                        estatus_carroceria = :estatus
                    WHERE id_carroceria = :id";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':matricula'    => strtoupper(trim($data['matricula'])),
                ':localidad'    => $data['localidad_pertenece'],
                ':responsable'  => $data['responsable_carroceria'],
                ':contenedores' => $data['numero_contenedores'] ?? 0,
                ':peso'         => $data['peso_vehicular'],
                ':ejes'         => $data['numero_ejes_vehiculares'] ?? 0,
                ':tipo'         => $data['tipo_carroceria'],
                ':estatus'      => $data['estatus_carroceria'],
                ':id'           => $data['id_carroceria']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * CORRECCIÓN: Obtiene una carrocería por matrícula con JOIN de localidad.
     * Nombre sincronizado con CarroceriaController.
     */
    public function obtenerPorMatricula($matricula)
    {
        global $pdo;
        $sql = "SELECT c.*, l.nombre_centro_trabajo AS nombre_localidad 
                FROM carrocerias c
                LEFT JOIN localidades l ON c.localidad_pertenece = l.id_localidad
                WHERE c.matricula = :matricula";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':matricula' => $matricula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    /**
     * Valida si la carrocería puede eliminarse (RF-GV-CC-04).
     */
    public function deleteCarroceria($id)
    {
        global $pdo;
        $check = $this->obtenerCarroceriaPorId($id);
        
        if ($check && $check['estatus_carroceria'] === 'Ensamblada') {
            return "No se puede eliminar una carrocería que está 'Ensamblada' en un vehículo.";
        }

        $sql = "DELETE FROM carrocerias WHERE id_carroceria = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]) ? true : "Error al intentar eliminar el registro.";
    }

    public function obtenerCarroceriaPorId($id)
    {
        global $pdo;
        $sql = "SELECT * FROM carrocerias WHERE id_carroceria = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorMatricula($texto)
    {
        global $pdo;
        $sql = "SELECT * FROM carrocerias WHERE matricula ILIKE ? LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$texto%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}