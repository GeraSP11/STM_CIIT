<?php
require_once __DIR__ . "../../backend/controllers/productos-controller.php";

$action = $_POST['action'] ?? '';

$controller = new ProductosController();

switch ($action) {

    case 'registrar':
        echo $controller->registrarProducto($_POST);
        break;

    case 'listar_localidades':
        echo json_encode($controller->listarLocalidades());
        break;

    case 'buscar_productos':
            // Buscar productos para sugerencias
            echo json_encode($controller->buscarProductos($_POST));
            break;
    
    case 'obtener_producto':
            // Obtener un producto completo por ID
            echo json_encode($controller->obtenerProductoPorId($_POST));
            break;
    
    case 'actualizar_producto':
            // Actualizar producto
            echo $controller->actualizarProducto($_POST);
            break;
    
    case 'eliminar_producto':
            // Eliminar producto
            echo $controller->eliminarProducto($_POST);
            break;

     case 'listar_todos_productos':
            echo json_encode($controller->listarTodosProductos());
            break;
    default:
        echo json_encode(["error" => "Acción no válida"]);
}
?>