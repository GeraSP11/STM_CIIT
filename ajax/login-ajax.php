<?php
session_start();
require_once __DIR__ . "/../backend/controllers/login-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'login':
        $controller = new LoginController();
        echo $controller->procesarLogin($_POST);
        break;

    case 'logout':
        session_destroy();
        echo "OK";
        break;

    default:
        echo "Acción no válida";
}
?>