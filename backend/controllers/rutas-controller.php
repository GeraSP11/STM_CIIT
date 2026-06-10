<?php
require_once __DIR__ . '/../models/rutas-model.php';
class RutasController
{

    private RutasModel $model;

    public function __construct()
    {
        $this->model = new RutasModel();
    }

    // ------------------------------------------------------------------
    // REGISTRAR
    // ------------------------------------------------------------------

    /**
     * Valida y registra una nueva ruta.
     * Retorna "OK" en éxito o un mensaje de error descriptivo.
     */
    public function registrarRuta(array $datos): string
    {

        // Localidad origen obligatoria
        if (empty($datos["localidad_origen"])) {
            return "La localidad de origen es obligatoria.";
        }

        // Localidad destino obligatoria
        if (empty($datos["localidad_destino"])) {
            return "La localidad de destino es obligatoria.";
        }

        // Origen y destino deben ser distintos
        if ($datos["localidad_origen"] === $datos["localidad_destino"]) {
            return "La localidad de destino debe ser distinta a la de origen.";
        }

        // Modalidad obligatoria y dentro de valores permitidos
        $modalidadesPermitidas = ["Carretera", "Ferroviaria", "Marítima", "Aérea"];
        if (empty($datos["modalidad_ruta"]) || !in_array($datos["modalidad_ruta"], $modalidadesPermitidas)) {
            return "Debe seleccionar una modalidad válida.";
        }

        // Tipo de ruta obligatorio para modalidad Carretera
        if ($datos["modalidad_ruta"] === "Carretera" && empty($datos["tipo_ruta"])) {
            return "El tipo de ruta es obligatorio para modalidad Carretera.";
        }

        // Si no es Carretera, el tipo de ruta no aplica
        if ($datos["modalidad_ruta"] !== "Carretera") {
            $datos["tipo_ruta"] = null;
        }

        // Distancia: si se ingresa, debe ser numérica y mayor a 0
        if ($datos["distancia"] !== "" && $datos["distancia"] !== null) {
            if (!is_numeric($datos["distancia"]) || (float)$datos["distancia"] <= 0) {
                return "La distancia debe ser un número mayor a 0.";
            }
        }

        // Peso soportado: si se ingresa, debe ser numérico y mayor a 0
        if ($datos["peso_soportado"] !== "" && $datos["peso_soportado"] !== null) {
            if (!is_numeric($datos["peso_soportado"]) || (float)$datos["peso_soportado"] <= 0) {
                return "El peso soportado debe ser un valor válido mayor a 0.";
            }

            // Validar límites de peso según tipo de ruta (normativa)
            $peso     = (float)$datos["peso_soportado"];
            $tipoRuta = $datos["tipo_ruta"] ?? "";

            if ($tipoRuta === "B" && $peso > 38) {
                return "El peso del vehículo excede el límite permitido para la ruta seleccionada. Rutas tipo B permiten un máximo de 38 toneladas.";
            }
            if ($tipoRuta === "C" && $peso > 25.5) {
                return "El peso del vehículo excede el límite permitido para la ruta seleccionada. Rutas tipo C permiten un máximo de 25.5 toneladas.";
            }
        }

        // Verificar que la combinación origen-destino-modalidad no exista ya
        if ($this->model->existeRuta(
            $datos["localidad_origen"],
            $datos["localidad_destino"],
            $datos["modalidad_ruta"]
        )) {
            return "Ya existe una ruta registrada con el mismo origen, destino y modalidad.";
        }

        $ok = $this->model->registrarRuta($datos);
        return $ok ? "OK" : "No se pudo registrar la ruta. Intente nuevamente.";
    }


    // ------------------------------------------------------------------
    // CONSULTAR
    // ------------------------------------------------------------------

    /**
     * Devuelve todas las localidades para poblar los selects del filtro.
     */
    public function obtenerLocalidades(): array
    {
        return $this->model->obtenerLocalidades();
    }

    /**
     * Busca rutas filtrando por localidad origen y/o destino.
     * Al menos uno de los dos parámetros debe estar presente.
     */
    public function buscarRutasConsulta(string $idOrigen, string $idDestino): array
    {
        if ($idOrigen === "" && $idDestino === "") {
            return ["error" => "Seleccione al menos una localidad para filtrar."];
        }
        return $this->model->buscarRutasConsulta($idOrigen, $idDestino);
    }

    /**
     * Obtiene el detalle completo de una ruta por su id_ruta.
     */
    public function obtenerRutaDetalle(string $idRuta): array
    {
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

    public function buscarRutas(string $idRuta): array
    {
        return $this->model->buscarRutas($idRuta);
    }

    public function obtenerRuta(string $idRuta): array
    {
        if ($idRuta === "") return [];
        return $this->model->obtenerRuta($idRuta) ?? [];
    }

    public function actualizarRuta(array $datos): string
    {
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
