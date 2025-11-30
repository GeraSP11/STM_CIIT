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

    case 'obtener-personal':
        $controller = new UsuariosController();
        echo $controller->obtenerPersonal();
        break;

    case 'buscar-usuario':
        $controller = new UsuariosController();
        echo $controller->buscarUsuario($_POST);
        break;

    case 'verificar-password':
        $controller = new UsuariosController();
        echo $controller->verificarPassword($_POST);
        break;

    case 'actualizar-usuario':
        $controller = new UsuariosController();
        echo $controller->actualizarUsuario($_POST);
        break;

    case 'eliminar-usuario':
        $controller = new UsuariosController();
        echo $controller->eliminarUsuario($_POST);
        break;

    default:
        echo "Acción no válida";
}
?>