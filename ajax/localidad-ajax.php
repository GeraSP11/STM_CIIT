<?php
require_once __DIR__ . "../../backend/controllers/localidad-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'registrar-localidad':
        $controller = new LocalidadController();
        echo $controller->registrarPersonal($_POST);
        break;
    default:
        echo "Acción no válida";
}
