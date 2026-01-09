<?php
// =====================================================
//  MODELO DE CARROCERÍAS (6.1)
//  Interactúa directamente con la tabla 'carrocerias'
// =====================================================

require_once __DIR__ . "/../config/conexion.php";

class CarroceriaModel
{
    /**
     * Verifica si una matrícula ya existe en la base de datos.
     */
    public function matriculaExiste($matricula)
    {
        global $pdo;
        $sql = "SELECT id_carroceria FROM carrocerias WHERE matricula = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$matricula]);
        return $stmt->rowCount() > 0;
    }

    /**
     * 6.1.1 Registrar una nueva carrocería.
     */
    public function registrarCarroceria($matricula, $localidad, $responsable, $contenedores, $peso, $ejes, $tipo, $modalidad)
    {
        global $pdo;
        $sql = "INSERT INTO carrocerias 
                (matricula, localidad_pertenece, responsable_carroceria, numero_contenedores, 
                 peso_vehicular, numero_ejes_vehiculares, tipo_carroceria, modalidad_carroceria, estatus_carroceria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Disponible')";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $matricula, $localidad, $responsable, $contenedores, 
            $peso, $ejes, $tipo, $modalidad
        ]);
    }

    /**
     * 6.1.2 Consultar carrocerías con filtros dinámicos.
     */
    public function consultarCarrocerias($filtros = [])
    {
        global $pdo;

        // Base de la consulta con JOINs para traer nombres de localidad y personal
        $sql = "SELECT c.*, l.nombre_centro_trabajo AS localidad, 
                       (p.nombre_personal || ' ' || p.apellido_paterno) AS responsable
                FROM carrocerias c
                LEFT JOIN localidades l ON c.localidad_pertenece = l.id_localidad
                LEFT JOIN personal p ON c.responsable_carroceria = p.id_personal
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['modalidad'])) {
            $sql .= " AND c.modalidad_carroceria = :modalidad";
            $params[':modalidad'] = $filtros['modalidad'];
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND c.tipo_carroceria = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['estatus'])) {
            $sql .= " AND c.estatus_carroceria = :estatus";
            $params[':estatus'] = $filtros['estatus'];
        }
        if (!empty($filtros['matricula'])) {
            $sql .= " AND c.matricula ILIKE :matricula";
            $params[':matricula'] = "%" . $filtros['matricula'] . "%";
        }

        $sql .= " ORDER BY c.id_carroceria DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca coincidencias para el autocompletado del Actualizar/Eliminar.
     */
    public function buscarCarrocerias($texto)
    {
        global $pdo;
        $sql = "SELECT id_carroceria, matricula, modalidad_carroceria, tipo_carroceria, 
                       peso_vehicular, estatus_carroceria, localidad_pertenece, responsable_carroceria
                FROM carrocerias
                WHERE matricula ILIKE :texto
                LIMIT 10";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':texto' => "%$texto%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 6.1.3 Actualizar carrocería.
     */
    public function actualizarCarroceria($id, $data)
    {
        global $pdo;
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
            ':id'           => $id
        ]);
    }

    /**
     * 6.1.4 Eliminar carrocería.
     */
    public function eliminarCarroceria($id)
    {
        global $pdo;
        // Solo eliminamos si no hay dependencias activas (el controlador ya valida el estatus 'Ensamblada')
        $sql = "DELETE FROM carrocerias WHERE id_carroceria = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Obtiene una carrocería por ID para validaciones previas.
     */
    public function obtenerCarroceriaPorId($id)
    {
        global $pdo;
        $sql = "SELECT * FROM carrocerias WHERE id_carroceria = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una carrocería por Matrícula (Útil para validar duplicados en actualización).
     */
    public function obtenerCarroceriaPorMatricula($matricula)
    {
        global $pdo;
        $sql = "SELECT id_carroceria FROM carrocerias WHERE matricula = :mat LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":mat" => $matricula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una carrocería específica para mostrar antes de eliminar.
     */
    public function mostrarCarroceriaEliminar($filtros)
    {
        global $pdo;
        $sql = "SELECT c.*, l.nombre_centro_trabajo AS nombre_localidad 
                FROM carrocerias c
                LEFT JOIN localidades l ON c.localidad_pertenece = l.id_localidad";
        
        if (isset($filtros['matricula'])) {
            $sql .= " WHERE c.matricula = :matricula";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':matricula' => $filtros['matricula']]);
        } else {
            $sql .= " WHERE c.id_carroceria = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $filtros['id_carroceria']]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}