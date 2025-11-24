<?php
session_start();
require_once __DIR__ . "/../backend/controllers/usuarios-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'registrar':
        $controller = new UsuariosController();
        echo $controller->registrarUsuario($_POST);
        break;

    case 'consultar-usuario':
        $controller = new UsuariosController();
        echo $controller->consultarUsuario($_POST);
        break;

    case 'eliminar':
        $controller = new UsuariosController();
        echo $controller->eliminarUsuario($_POST);
        break;

    default:
        echo "Acción no válida";
}
?>