<?php
require_once __DIR__ . "../../backend/controllers/usuarios-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'registrar':
        $controller = new UsuariosController();
        echo $controller->registrarUsuario($_POST);
        break;

    default:
        echo "Acción no válida";
}
