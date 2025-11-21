<?php
require_once __DIR__ . "../../backend/controllers/personal-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'registrar':
        $controller = new PersonalController();
        echo $controller->registrarPersonal($_POST);
        break;
    case 'obtener-localidades':  // nuevo case para traer las localidades
        $controller = new PersonalController();
        $localidades = $controller->obtenerLocalidades();
        header('Content-Type: application/json');
        echo json_encode($localidades);
        break;

    default:
        echo "Acción no válida";
}
