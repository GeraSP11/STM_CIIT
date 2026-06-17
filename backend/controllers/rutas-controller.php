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
        $error = $this->validarCamposBase($datos) ?? $this->validarPorModalidad($datos);
        if ($error) return $error;

        $ok = $this->model->actualizarRuta($datos);
        return $ok ? "OK" : "No se pudo actualizar la ruta. Intente nuevamente.";
    }

    // ── Validaciones base ──────────────────────────────────────────────

    private function validarCamposBase(array $datos): ?string
    {
        if (empty($datos["id_ruta"])) {
            return "El identificador de ruta es requerido.";
        }
        if (empty($datos["localidad_origen"]) || empty($datos["localidad_destino"])) {
            return "Las localidades de origen y destino son requeridas.";
        }
        if ($datos["localidad_origen"] === $datos["localidad_destino"]) {
            return "La localidad de origen y destino no pueden ser la misma.";
        }
        if (empty($datos["modalidad_ruta"])) {
            return "La modalidad es requerida.";
        }
        return null;
    }

    // ── Validaciones por modalidad ─────────────────────────────────────

    private function validarPorModalidad(array $datos): ?string
    {
        $modalidad = $datos["modalidad_ruta"];

        $validadores = [
            "Carretera"   => fn() => $this->validarCarretera($datos),
            "Ferroviaria" => fn() => $this->validarDistanciaYPeso($datos),
            "Marítima"    => fn() => $this->validarDistanciaYPeso($datos),
            "Aérea"       => fn() => $this->validarDistancia($datos),
        ];

        return isset($validadores[$modalidad]) ? $validadores[$modalidad]() : null;
    }

    private function validarCarretera(array $datos): ?string
    {
        if (empty($datos["tipo_ruta"])) {
            return "El tipo de ruta es requerido para modalidad Carretera.";
        }

        $errorDistancia = $this->validarDistancia($datos);
        if ($errorDistancia) return $errorDistancia;

        return $this->validarPesoCarretera($datos);
    }

    private function validarDistanciaYPeso(array $datos): ?string
    {
        return $this->validarDistancia($datos) ?? $this->validarPeso($datos);
    }

    private function validarDistancia(array $datos): ?string
    {
        if (!$this->esPositivo($datos["distancia"] ?? "")) {
            return "La distancia es requerida y debe ser un valor positivo.";
        }
        return null;
    }

    private function validarPeso(array $datos): ?string
    {
        $peso = $this->normalizarNumerico($datos["peso_soportado"] ?? "");
        if ($peso === null) return null;

        if (!$this->esPositivo($peso)) {
            return "El peso soportado debe ser un valor positivo.";
        }
        return null;
    }

    private function validarPesoCarretera(array $datos): ?string
    {
        $peso     = $this->normalizarNumerico($datos["peso_soportado"] ?? "");
        $tipoRuta = $datos["tipo_ruta"];

        if ($peso === null) return null;

        if (!$this->esPositivo($peso)) {
            return "El peso soportado debe ser un valor positivo.";
        }

        $limites = ["B" => 38, "C" => 25.5];

        if (isset($limites[$tipoRuta]) && $peso > $limites[$tipoRuta]) {
            return "Para tipo {$tipoRuta} el peso soportado no puede exceder {$limites[$tipoRuta]} toneladas.";
        }

        return null;
    }

    // ── Helpers ────────────────────────────────────────────────────────

    private function esPositivo(mixed $valor): bool
    {
        return $valor !== "" && is_numeric($valor) && (float)$valor > 0;
    }

    private function normalizarNumerico(mixed $valor): ?float
    {
        if ($valor === "" || $valor === null) return null;
        return is_numeric($valor) ? (float)$valor : null;
    }

// ------------------------------------------------------------------
// ELIMINAR
// ------------------------------------------------------------------

/**
 * Valida y elimina una o varias rutas.
 * $idsRaw es la cadena "1,2,3" que viene del POST.
 */
public function eliminarRutas(string $idsRaw): string
{
    if (trim($idsRaw) === '') {
        return 'Debe indicar al menos un ID de ruta a eliminar.';
    }

    // Filtrar solo enteros positivos
    $ids = array_filter(
        array_map('intval', explode(',', $idsRaw)),
        fn($id) => $id > 0
    );

    if (empty($ids)) {
        return 'Los identificadores de ruta no son válidos.';
    }

    $ok = $this->model->eliminarRutas(array_values($ids));
    return $ok ? 'OK' : 'No se pudo eliminar la(s) ruta(s). Intente nuevamente.';
}
}
