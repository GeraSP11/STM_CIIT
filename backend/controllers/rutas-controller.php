<?php
require_once __DIR__ . '/../models/rutas-model.php';
class RutasController {

    private RutasModel $model;

    public function __construct() {
        $this->model = new RutasModel();
    }

    // ------------------------------------------------------------------
    // CONSULTAR
    // ------------------------------------------------------------------

    /**
     * Devuelve todas las localidades para poblar los selects del filtro.
     */
    public function obtenerLocalidades(): array {
        return $this->model->obtenerLocalidades();
    }

    /**
     * Busca rutas filtrando por localidad origen y/o destino.
     * Al menos uno de los dos parámetros debe estar presente.
     */
    public function buscarRutasConsulta(string $idOrigen, string $idDestino): array {
        if ($idOrigen === "" && $idDestino === "") {
            return ["error" => "Seleccione al menos una localidad para filtrar."];
        }
        return $this->model->buscarRutasConsulta($idOrigen, $idDestino);
    }

    /**
     * Obtiene el detalle completo de una ruta por su id_ruta.
     */
    public function obtenerRutaDetalle(string $idRuta): array {
        if ($idRuta === "") {
            return ["error" => "ID de ruta requerido."];
        }
        $ruta = $this->model->obtenerRutaDetalle($idRuta);
        if (!$ruta) {
            return ["error" => "No se encontró la ruta especificada."];
        }
        return $ruta;
    }

    // ------------------------------------------------------------------
    // ACTUALIZAR
    // ------------------------------------------------------------------

    public function buscarRutas(string $idRuta): array {
        return $this->model->buscarRutas($idRuta);
    }

    public function obtenerRuta(string $idRuta): array {
        if ($idRuta === "") return [];
        return $this->model->obtenerRuta($idRuta) ?? [];
    }

    public function actualizarRuta(array $datos): string {
        // Validaciones básicas en backend
        if (empty($datos["id_ruta"])) {
            return "El identificador de ruta es requerido.";
        }
        if (empty($datos["localidad_origen"]) || empty($datos["localidad_destino"])) {
            return "Las localidades de origen y destino son requeridas.";
        }
        if (empty($datos["modalidad_ruta"])) {
            return "La modalidad es requerida.";
        }
        if ($datos["distancia"] !== null && $datos["distancia"] !== "" && !is_numeric($datos["distancia"])) {
            return "La distancia debe ser un valor numérico.";
        }
        if ($datos["peso_soportado"] !== null && $datos["peso_soportado"] !== "" && !is_numeric($datos["peso_soportado"])) {
            return "El peso soportado debe ser un valor numérico.";
        }

        $ok = $this->model->actualizarRuta($datos);
        return $ok ? "OK" : "No se pudo actualizar la ruta. Intente nuevamente.";
    }
}