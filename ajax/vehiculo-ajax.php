<?php
require_once '../config/db.php'; // Ajusta la ruta según tu estructura

// Recibir la acción del JavaScript
$action = $_POST['action'] ?? '';

// Objeto de respuesta para peticiones que esperan JSON (Consultas)
// Para Registro/Actualización/Eliminación responderemos con texto plano "OK" o el error
switch ($action) {

    case 'registrar-vehiculo':
        $placas = $_POST['placas'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $capacidad = $_POST['capacidad_carga'];
        $tipo = $_POST['tipo_vehiculo'];
        $id_carroceria = $_POST['id_carroceria'];

        $sql = "INSERT INTO vehiculos (placas, marca, modelo, capacidad_carga, tipo_vehiculo, id_carroceria) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$placas, $marca, $modelo, $capacidad, $tipo, $id_carroceria])) {
            echo "OK";
        } else {
            echo "Error al registrar el vehículo.";
        }
        break;

    case 'consultar-vehiculos':
        // Filtros dinámicos
        $sql = "SELECT * FROM vehiculos WHERE 1=1";
        $params = [];

        if (!empty($_POST['placas'])) {
            $sql .= " AND placas LIKE ?";
            $params[] = "%" . $_POST['placas'] . "%";
        }
        if (!empty($_POST['marca'])) {
            $sql .= " AND marca LIKE ?";
            $params[] = "%" . $_POST['marca'] . "%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'listar-placas':
        $sql = "SELECT placas FROM vehiculos";
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'obtener-vehiculo-unico':
        $campo = $_POST['campo']; // puede ser 'id' o 'placas'
        $valor = $_POST['valor'];
        
        $columna = ($campo === 'id') ? 'id_vehiculo' : 'placas';
        
        $sql = "SELECT * FROM vehiculos WHERE $columna = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$valor]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        break;

    case 'actualizar-vehiculo':
        $id = $_POST['id_vehiculo'];
        $placas = $_POST['placas'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $capacidad = $_POST['capacidad_carga'];
        $tipo = $_POST['tipo_vehiculo'];
        $id_carroceria = $_POST['id_carroceria'];

        $sql = "UPDATE vehiculos SET placas=?, marca=?, modelo=?, capacidad_carga=?, tipo_vehiculo=?, id_carroceria=? 
                WHERE id_vehiculo=?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$placas, $marca, $modelo, $capacidad, $tipo, $id_carroceria, $id])) {
            echo "OK";
        } else {
            echo "Error al actualizar.";
        }
        break;

    case 'eliminar-vehiculo':
        $id = $_POST['id_vehiculo'];
        
        $sql = "DELETE FROM vehiculos WHERE id_vehiculo = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            echo "OK";
        } else {
            echo "Error al eliminar.";
        }
        break;

    default:
        echo "Acción no reconocida.";
        break;
}