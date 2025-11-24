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
    case 'consultar-personal':
        $controller = new PersonalController();
        $localidades = $controller->listarPersonal($_POST);
        header('Content-Type: application/json');
        echo json_encode($localidades);
        break;
    case 'actualizar-personal':
        $controller = new PersonalController();
        echo $controller->actualizarPersonal($_POST);
        break;
    case 'eliminar-personal':
        $controller = new PersonalController();
        echo $controller->eliminarPersonal($_POST);
        break;
    default:
        echo "Acción no válida";
}
