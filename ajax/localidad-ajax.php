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
    case 'buscar-localidades':
        $texto = $_POST['busqueda'] ?? '';
        $controller = new LocalidadController();
        $resultados = $controller->buscarLocalidades($texto);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;
    case 'actualizar-localidad':
        // id_localidad, nombre_centro_trabajo, ubicacion_georeferenciada, poblacion, estado, tipo_instalacion, localidad
        $controller = new LocalidadController();
        echo $controller->actualizarLocalidad($_POST);
        break;
    default:
        echo "Acción no válida";
}