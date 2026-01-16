<?php
/**
 * AJAX Controller para Vehículos
 * Integrado con lógica de Carrocerías y Personal
 */

require_once __DIR__ . "/../backend/config/conexion.php"; 
require_once __DIR__ . "/../backend/controllers/vehiculo-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'registrar-vehiculo':
        // --- 1. CAPTURA DE DATOS (ESTO FALTABA PARA EVITAR LOS WARNINGS) ---
        $carroceriasIds = $_POST['ids_carrocerias'] ?? [];
        $modalidad = $_POST['modalidad_vehiculo'] ?? '';
        $clave = $_POST['clave_vehiculo'] ?? '';
        $descripcion = $_POST['descripcion_vehiculo'] ?? '';
        $chofer = $_POST['chofer_asignado'] ?? null;
        $clase = $_POST['clase'] ?? '';
        $nomenclatura = $_POST['nomenclatura'] ?? '';
        $ejes = $_POST['numero_de_ejes'] ?? 0;
        $llantas = $_POST['numero_de_llantas'] ?? 0;
        $peso = $_POST['peso_bruto_vehicular'] ?? 0;

        // --- 2. TUS VALIDACIONES ORIGINALES (SIN CAMBIOS) ---
        if (empty($carroceriasIds)) {
            echo "Error: Debe seleccionar al menos una carrocería.";
            break;
        }

        // Obtener detalles de las carrocerías seleccionadas para validar reglas
        $placeholders = implode(',', array_fill(0, count($carroceriasIds), '?'));
        $stmt = $pdo->prepare("SELECT tipo_carroceria FROM carrocerias WHERE id_carroceria IN ($placeholders)");
        $stmt->execute($carroceriasIds);
        $detalles = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $conteoTipos = array_count_values($detalles);
        $arrastre = $conteoTipos['Unidad de arrastre'] ?? 0;
        $carga = $conteoTipos['Unidad de carga'] ?? 0;
        $mixta = $conteoTipos['Unidad mixta'] ?? 0;
        $total = count($detalles);

        // Regla 1: Marítimo/Aéreo solo 1 MIXTA
        if (($modalidad === 'Marítimo' || $modalidad === 'Aéreo')) {
            if ($total !== 1 || $mixta !== 1) {
                echo "Error: Para esta modalidad solo se permite una unidad de tipo Mixta.";
                break;
            }
        }

        // Regla 3 y 4: Carretero y Ferroviario
        if ($modalidad === 'Carretero' || $modalidad === 'Ferroviario') {
            if ($modalidad === 'Carretero' && $total === 1 && $mixta === 0) {
                echo "Error: Si selecciona una sola carrocería para Carretero, debe ser Mixta.";
                break;
            }
            if ($mixta === 0 && ($arrastre === 0 || $carga === 0)) {
                echo "Error: Debe incluir al menos una unidad de carga y una de arrastre.";
                break;
            }
        }

        // --- 3. TU LÓGICA DE INSERCIÓN Y UNIÓN (ORDEN MANTENIDO) ---
        try {
            $pdo->beginTransaction();

            // 1. Insertar el Vehículo
            $sqlVehiculo = "INSERT INTO vehiculos (clave_vehiculo, modalidad_vehiculo, descripcion_vehiculo, chofer_asignado, clase, nomenclatura, numero_de_ejes, numero_de_llantas, peso_bruto_vehicular) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sqlVehiculo);
            $stmt->execute([$clave, $modalidad, $descripcion, $chofer, $clase, $nomenclatura, $ejes, $llantas, $peso]);
            
            $idVehiculo = $pdo->lastInsertId();

            // 2. Insertar la relación en vehiculos_carrocerias
            $sqlRelacion = "INSERT INTO vehiculos_carrocerias (id_vehiculo, id_carroceria) VALUES (?, ?)";
            $stmtRel = $pdo->prepare($sqlRelacion);

            // CORRECCIÓN MENOR: Usamos $carroceriasIds que es la variable definida arriba
            foreach ($carroceriasIds as $idCarroceria) {
                $stmtRel->execute([$idVehiculo, $idCarroceria]);
                
                // OPCIONAL: Actualizar estado de la carrocería a 'En Uso'
                // Cambiamos "En Uso" por "Ensamblada" para respetar el CHECK de tu BD
                $pdo->prepare("UPDATE carrocerias SET estatus_carroceria = 'Ensamblada' WHERE id_carroceria = ?")
                    ->execute([$idCarroceria]);
            }

            $pdo->commit();
            echo "OK";

        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
        break;
    case 'consultar-carrocerias-por-modalidad':
        $modalidad = $_POST['modalidad'] ?? '';
        header('Content-Type: application/json');
        
        try {
            // Ajustado a los nombres de tu tabla 'carrocerias'
            $sql = "SELECT id_carroceria, tipo_carroceria, matricula 
                    FROM carrocerias 
                    WHERE modalidad_carroceria = ? 
                    AND estatus_carroceria = 'Disponible'";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$modalidad]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'consultar-personal-chofer':
        header('Content-Type: application/json');
        global $pdo; 
        try {
            // Ajustado a los nombres exactos de init.sql: nombre_personal, apellido_paterno, apellido_materno
            // Nota: En SQL el cargo está definido como 'Chófer' (con acento) en el CHECK de init.sql
            $sql = "SELECT id_personal, nombre_personal, apellido_paterno, apellido_materno 
                    FROM personal 
                    WHERE cargo = 'Chófer' 
                    ORDER BY nombre_personal ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($resultados);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'consultar-vehiculos':
        $sql = "SELECT v.*, (p.nombre_personal || ' ' || p.apellido_paterno) as nombre_chofer 
                FROM vehiculos v
                LEFT JOIN personal p ON v.chofer_asignado = p.id_personal
                WHERE 1=1";
        $params = [];

        if (!empty($_POST['clave_vehiculo'])) {
            $sql .= " AND v.clave_vehiculo LIKE ?";
            $params[] = "%" . $_POST['clave_vehiculo'] . "%";
        }
        if (!empty($_POST['modalidad_vehiculo'])) {
            $sql .= " AND v.modalidad_vehiculo = ?";
            $params[] = $_POST['modalidad_vehiculo'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'actualizar-vehiculo':
        $id = $_POST['id_vehiculo'];
        $clave = $_POST['clave_vehiculo'];
        $modalidad = $_POST['modalidad_vehiculo'];
        $descripcion = $_POST['descripcion_vehiculo'];
        $chofer = $_POST['chofer_asignado'];
        $clase = $_POST['clase'];
        $nomenclatura = $_POST['nomenclatura'];
        $ejes = $_POST['numero_de_ejes'];
        $llantas = $_POST['numero_de_llantas'];
        $peso = $_POST['peso_bruto_vehicular'];

        $sql = "UPDATE vehiculos SET 
                clave_vehiculo=?, modalidad_vehiculo=?, descripcion_vehiculo=?, 
                chofer_asignado=?, clase=?, nomenclatura=?, 
                numero_de_ejes=?, numero_de_llantas=?, peso_bruto_vehicular=? 
                WHERE id_vehiculo=?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$clave, $modalidad, $descripcion, $chofer, $clase, $nomenclatura, $ejes, $llantas, $peso, $id])) {
            echo "OK";
        } else {
            echo "Error al actualizar la unidad.";
        }
        break;

    case 'eliminar-vehiculo':
        $id = $_POST['id_vehiculo'];
        $sql = "DELETE FROM vehiculos WHERE id_vehiculo = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            echo "OK";
        } else {
            echo "Error: No se puede eliminar el vehículo.";
        }
        break;

    default:
        echo "Acción no válida.";
        break;
}