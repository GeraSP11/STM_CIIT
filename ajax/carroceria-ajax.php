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
        // 1. REQUERIMIENTO: Mensajes de error claros y validaciones específicas
        if (empty($_POST['matricula'])) {
            echo "Error: La matrícula es un campo obligatorio.";
            exit;
        }

        if (empty($_POST['responsable_carroceria'])) {
            echo "Error: Debe asignar un responsable (Jefe de Almacén).";
            exit;
        }

        // 2. REQUERIMIENTO: Modalidad ferroviaria no permite tipo mixto
        $modalidad = $_POST['modalidad_carroceria'] ?? '';
        $tipo = $_POST['tipo_carroceria'] ?? '';

        if ($modalidad === 'Ferroviario' && $tipo === 'Mixta') {
            echo "Error de Validación: No se permite seleccionar el tipo de carrocería 'Mixto' para la modalidad Ferroviaria.";
            exit;
        }

        // Asegurar valores por defecto para campos numéricos
        $_POST['numero_ejes_vehiculares'] = !empty($_POST['numero_ejes_vehiculares']) ? $_POST['numero_ejes_vehiculares'] : 0;
        $_POST['numero_contenedores'] = !empty($_POST['numero_contenedores']) ? $_POST['numero_contenedores'] : 0;
        
        $controller = new CarroceriaController();
        echo $controller->registrarCarroceria($_POST);
        break;
    case 'consultar-carrocerias':
        header('Content-Type: application/json');
        $controller = new CarroceriaController();
        $carrocerias = $controller->consultarCarrocerias($_POST);

        global $pdo; 
        
        foreach ($carrocerias as &$carro) {
            // Ajuste: Las columnas reales en la tabla 'carrocerias' son estas:
            $idLoc = $carro['localidad_pertenece'] ?? null;
            $idPers = $carro['responsable_carroceria'] ?? null;

            // 1. Buscar y concatenar Localidad
            if ($idLoc) {
                $stmtL = $pdo->prepare("SELECT (nombre_centro_trabajo || ' - ' || poblacion) as display FROM localidades WHERE id_localidad = ?");
                $stmtL->execute([$idLoc]);
                $loc = $stmtL->fetch(PDO::FETCH_ASSOC);
                $carro['nombre_display_localidad'] = $loc ? $loc['display'] : "Localidad no encontrada";
            } else {
                $carro['nombre_display_localidad'] = "No asignada";
            }

            // 2. Buscar y concatenar Responsable
            if ($idPers) {
                $stmtP = $pdo->prepare("SELECT (nombre_personal || ' ' || apellido_paterno || ' (' || cargo || ')') as completo FROM personal WHERE id_personal = ?");
                $stmtP->execute([$idPers]);
                $pers = $stmtP->fetch(PDO::FETCH_ASSOC);
                $carro['nombre_completo_personal'] = $pers ? $pers['completo'] : "Personal no encontrado";
            } else {
                $carro['nombre_completo_personal'] = "Sin responsable";
            }
        }
        echo json_encode($carrocerias);
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
            // Filtramos por el cargo específico
            $sql = "SELECT id_personal, 
                    (nombre_personal || ' ' || apellido_paterno) as nombre_completo 
                    FROM personal 
                    WHERE cargo = 'Jefe de Almacén' 
                    ORDER BY nombre_personal ASC";
            
            $stmt = $pdo->query($sql);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($resultados);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    exit;

    default:
        header('HTTP/1.1 400 Bad Request');
        echo "Acción no válida en el módulo de Carrocerías.";
        break;
}