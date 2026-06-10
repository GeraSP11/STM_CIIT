<?php
require_once __DIR__ . "../../backend/controllers/rutas-controller.php";

header("Content-Type: application/json; charset=UTF-8");

$action = $_POST["action"] ?? "";

$controller = new RutasController();

switch ($action) {

    case "obtener_localidades":
        echo json_encode($controller->obtenerLocalidades());
        break;

    case "buscar_rutas_consulta":
        $idOrigen  = $_POST["id_origen"]  ?? "";
        $idDestino = $_POST["id_destino"] ?? "";
        echo json_encode($controller->buscarRutasConsulta($idOrigen, $idDestino));
        break;

    case "obtener_ruta_detalle":
        $idRuta = $_POST["id_ruta"] ?? "";
        echo json_encode($controller->obtenerRutaDetalle($idRuta));
        break;

    // ------ Acciones de otros módulos (actualizar, eliminar, etc.) ------

    case "buscar_rutas":
        $idRuta = $_POST["id_ruta"] ?? "";
        echo json_encode($controller->buscarRutas($idRuta));
        break;

    case "obtener_ruta":
        $idRuta = $_POST["id_ruta"] ?? "";
        echo json_encode($controller->obtenerRuta($idRuta));
        break;

    case "actualizar_ruta":
        $datos = [
            "id_ruta"           => $_POST["id_ruta"]           ?? "",
            "localidad_origen"  => $_POST["localidad_origen"]  ?? "",
            "localidad_destino" => $_POST["localidad_destino"] ?? "",
            "modalidad_ruta"    => $_POST["modalidad_ruta"]    ?? "",
            "tipo_ruta"         => $_POST["tipo_ruta"]         ?? "",
            "distancia"         => $_POST["distancia"]         ?? null,
            "peso_soportado"    => $_POST["peso_soportado"]    ?? null,
            "descripcion"       => $_POST["descripcion"]       ?? "",
        ];
        header("Content-Type: text/plain; charset=UTF-8");
        echo $controller->actualizarRuta($datos);
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Acción no reconocida."]);
        break;
}