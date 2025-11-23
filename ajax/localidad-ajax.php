<?php
require_once __DIR__ . "../../backend/controllers/localidad-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'registrar-localidad':
        $controller = new LocalidadController();
        echo $controller->registrarLocalidad($_POST);
        break;
    case 'consultar-localidades':
        $controller = new LocalidadController();
        $localidades = $controller->consultarLocalidad($_POST);
        header('Content-Type: application/json');
        echo json_encode($localidades);
        break;
    default:
        echo "Acción no válida";
}