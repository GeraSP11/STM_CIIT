<?php
// =====================================================
//  MODELO DE CARROCERÍAS (6.1) - ACTUALIZADO RF-GV-CC-01
//  Interactúa con las tablas 'carrocerias' y 'carrocerias_detalle'
// =====================================================

require_once __DIR__ . "/../config/conexion.php";

class CarroceriaModel
{
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
                     peso_vehicular, numero_ejes_vehiculares, tipo_carroceria, modalidad_carroceria, estatus_carroceria)
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
                $d['modalidad'], 
                $d['estatus']
            ]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['id_carroceria'];
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Paso 2: Registrar los detalles de cada contenedor.
     */
    public function insertarDetalleCarroceria($id_carroceria, $num_con, $largo, $ancho, $alto)
    {
        global $pdo;
        try {
            $sql = "INSERT INTO carrocerias_detalle 
                    (identificador_carroceria, numero_contenedor, longitud, anchura, altura)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$id_carroceria, $num_con, $largo, $ancho, $alto]);
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
        $sql = "SELECT c.*, l.nombre_centro_trabajo AS localidad_nombre, 
                       (p.nombre_personal || ' ' || p.apellido_paterno) AS responsable_nombre
                FROM carrocerias c
                LEFT JOIN localidades l ON c.localidad_pertenece = l.id_localidad
                LEFT JOIN personal p ON c.responsable_carroceria = p.id_personal
                WHERE 1=1";

        $params = [];
        if (!empty($filtros['modalidad'])) {
            $sql .= " AND c.modalidad_carroceria = ?";
            $params[] = $filtros['modalidad'];
        }
        if (!empty($filtros['matricula'])) {
            $sql .= " AND c.matricula ILIKE ?";
            $params[] = "%" . $filtros['matricula'] . "%";
        }

        $sql .= " ORDER BY c.id_carroceria DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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