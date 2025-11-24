<?php
require_once __DIR__ . "../../backend/controllers/usuarios-controller.php";

$action = $_POST['action'] ?? '';

$controller = new UsuariosController();

switch ($action) {

    case 'registrar':
        echo $controller->registrarUsuario($_POST);
        break;

    case 'consultar-usuario':
        echo json_encode($controller->consultarUsuario($_POST));
        break;

    default:
        echo "Acción no válida";
}
