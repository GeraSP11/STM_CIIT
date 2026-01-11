<?php
// =====================================================
//  ROUTER AJAX: CARROCERÍAS (6.1) - ACTUALIZADO INTEGRAL
//  Maneja validaciones, CRUD y Carga de Catálogos
// =====================================================

// Ajuste de rutas: Subimos un nivel para entrar a backend
require_once __DIR__ . "/../backend/controllers/carroceria-controller.php";
require_once __DIR__ . "/../backend/config/conexion.php";

// Aceptamos tanto POST (para CRUD) como GET (para carga de selects)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'registrar-carroceria':
        // Validación de seguridad: Si no hay matrícula, no procesar
        if (empty($_POST['matricula'])) {
            echo "Error: La matrícula es obligatoria.";
            exit;
        }

        // Asegurar valores por defecto para campos que podrían estar bloqueados
        $_POST['numero_ejes_vehiculares'] = !empty($_POST['numero_ejes_vehiculares']) ? $_POST['numero_ejes_vehiculares'] : 0;
        $_POST['numero_contenedores'] = !empty($_POST['numero_contenedores']) ? $_POST['numero_contenedores'] : 0;
        
        $controller = new CarroceriaController();
        echo $controller->registrarCarroceria($_POST);
        break;
    case 'consultar-carrocerias':
        $controller = new CarroceriaController();
        $resultados = $controller->consultarCarrocerias($_POST);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'buscar-carrocerias':
        $texto = $_POST['busqueda'] ?? '';
        $controller = new CarroceriaController();
        $resultados = $controller->buscarCarrocerias($texto);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'actualizar-carroceria':
        if (empty($_POST['id_carroceria'])) {
            echo "ID de carrocería no especificado.";
            exit;
        }
        $controller = new CarroceriaController();
        echo $controller->actualizarCarroceria($_POST);
        break;

    case 'mostrar-eliminar-carroceria':
        $controller = new CarroceriaController();
        $resultados = $controller->mostrarCarroceriaEliminar($_POST);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'eliminar-carroceria':
        if (empty($_POST['id_carroceria'])) {
            echo "Error: ID de carrocería faltante.";
            exit;
        }
        $controller = new CarroceriaController();
        echo $controller->eliminarCarroceria($_POST);
        break;

    // --- NUEVOS CASOS PARA LLENAR SELECTS ---

    case 'obtener-localidades':
    global $pdo;
    header('Content-Type: application/json');
    try {
        // Consultamos las localidades registradas
        $stmt = $pdo->query("SELECT id_localidad, (nombre_centro_trabajo || ' - ' || poblacion) as nombre_display FROM localidades ORDER BY nombre_centro_trabajo ASC");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si no hay registros, el fetchAll devuelve un array vacío, lo cual es correcto
        echo json_encode($resultados);
    } catch (Exception $e) {
        echo json_encode([]); 
    }
    exit;

case 'obtener-personal':
    global $pdo;
    header('Content-Type: application/json');
    try {
        // Eliminamos "WHERE estatus_personal = 'Activo'" ya que la columna no existe
        $sql = "SELECT id_personal, 
                (nombre_personal || ' ' || apellido_paterno || ' (' || cargo || ')') as nombre_completo 
                FROM personal 
                ORDER BY nombre_personal ASC";
        
        $stmt = $pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultados);
    } catch (Exception $e) {
        // Es vital enviar el error para saber si algo más falla
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;

    default:
        header('HTTP/1.1 400 Bad Request');
        echo "Acción no válida en el módulo de Carrocerías.";
        break;
}