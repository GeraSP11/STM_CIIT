<?php
// =====================================================
//  ROUTER AJAX: CARROCERÍAS (6.1)
//  Recibe peticiones de carroceria.js y llama al controlador
// =====================================================

require_once __DIR__ . "/../../backend/controllers/carroceria-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'registrar-carroceria':
        $controller = new CarroceriaController();
        // Recibe $_POST con matricula, modalidad, tipo, ejes, contenedores, etc.
        echo $controller->registrarCarroceria($_POST);
        break;

    case 'consultar-carrocerias':
        $controller = new CarroceriaController();
        // Filtros opcionales en $_POST
        $resultados = $controller->consultarCarrocerias($_POST);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'buscar-carrocerias':
        $texto = $_POST['busqueda'] ?? '';
        $controller = new CarroceriaController();
        // Para el autocompletado del datalist en Actualizar
        $resultados = $controller->buscarCarrocerias($texto);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'actualizar-carroceria':
        $controller = new CarroceriaController();
        // Envía todos los datos modificados para el UPDATE
        echo $controller->actualizarCarroceria($_POST);
        break;

    case 'mostrar-eliminar-carroceria':
        $controller = new CarroceriaController();
        // Busca una carrocería específica (por matrícula o ID) antes de confirmar borrado
        $resultados = $controller->mostrarCarroceriaEliminar($_POST);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'eliminar-carroceria':
        $controller = new CarroceriaController();
        // Ejecuta el DELETE (previa validación de que no esté 'Ensamblada')
        echo $controller->eliminarCarroceria($_POST);
        break;

    default:
        header('HTTP/1.1 400 Bad Request');
        echo "Acción no válida o no especificada en el módulo de Carrocerías.";
        break;
}