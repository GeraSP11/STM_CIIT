<?php
require_once __DIR__ . "/../backend/controllers/vehiculo-controller.php";

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'registrar-vehiculo':
        $controller = new VehiculoController();
        // Se pasa $_POST que contiene placas, marca, modelo, capacidad, etc.
        echo $controller->registrarVehiculo($_POST);
        break;

    case 'consultar-vehiculos':
        $controller = new VehiculoController();
        // Filtros dinámicos recibidos desde el JS
        $vehiculos = $controller->consultarVehiculos($_POST);
        header('Content-Type: application/json');
        echo json_encode($vehiculos);
        break;

    case 'buscar-vehiculo-placa':
        // Utilizado por el datalist en la sección de actualizar
        $placas = $_POST['placas'] ?? '';
        $controller = new VehiculoController();
        $resultados = $controller->buscarVehiculoPorPlaca($placas);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'actualizar-vehiculo':
        $controller = new VehiculoController();
        echo $controller->actualizarVehiculo($_POST);
        break;

    case 'mostrar-eliminar-vehiculo':
        // Busca el vehículo específico para mostrar la vista previa antes de borrar
        $controller = new VehiculoController();
        $resultados = $controller->obtenerDatosVehiculo($_POST);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        break;

    case 'eliminar-vehiculo':
        $controller = new VehiculoController();
        // Requiere el id_vehiculo
        echo $controller->eliminarVehiculo($_POST);
        break;

    default:
        http_response_code(400);
        echo "Acción no válida";
        break;
}