<?php
require_once __DIR__ . "../../backend/controllers/productos-controller.php";

$action = $_POST['action'] ?? '';

$controller = new ProductosController();

switch ($action) {

    case 'registrar':
        echo json_encode($controller->registrarProducto($_POST));
        break;

    case 'listar_localidades':
        echo json_encode($controller->listarLocalidades());
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
}
