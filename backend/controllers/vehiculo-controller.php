<?php
require_once __DIR__ . "/../models/vehiculo-model.php";

class VehiculoController
{
    /**
     * Registro de vehículos con validaciones de negocio.
     */
    public function registrarVehiculo($data)
    {
        $clave = trim($data['clave_vehiculo']);
        $modalidad = $data['modalidad_vehiculo'];
        $descripcion = trim($data['descripcion_vehiculo']);
        $chofer = $data['chofer_asignado'] ?? null;
        $clase = trim($data['clase'] ?? '');
        $nomenclatura = trim($data['nomenclatura'] ?? '');
        $peso = floatval($data['peso_bruto_vehicular'] ?? 0);

        // --- VALIDACIONES ---
        $model = new VehiculoModel();

        // 1. Verificar duplicidad de Clave (Regla de Integridad)
        if ($model->claveExiste($clave)) {
            return "La clave de vehículo ya está registrada en el sistema.";
        }

        // 2. Nomenclatura Obligatoria (Regla 7)
        if (in_array($modalidad, ['Carretero', 'Ferroviario']) && empty($nomenclatura)) {
            return "La nomenclatura es obligatoria para las modalidades Carretero y Ferroviario.";
        }

        // 3. Chofer Asignado (Regla 4: No unidades huérfanas)
        if (empty($chofer)) {
            return "El vehículo debe tener un chofer asignado obligatoriamente.";
        }

        // 4. Lógica de Ejes y Llantas (Regla 6: Solo carretero)
        $ejes = ($modalidad === 'Carretero') ? intval($data['numero_de_ejes']) : 0;
        $llantas = ($modalidad === 'Carretero') ? intval($data['numero_de_llantas']) : 0;

        if ($modalidad === 'Carretero' && ($ejes <= 0 || $llantas <= 0)) {
            return "Para la modalidad Carretero, el número de ejes y llantas debe ser mayor a cero.";
        }

        // --- REGISTRO ---
        $resultado = $model->registrarVehiculo(
            $clave,
            $modalidad,
            $descripcion,
            $chofer,
            $clase,
            $nomenclatura,
            $ejes,
            $llantas,
            $peso
        );

        return $resultado ? "OK" : "Error al registrar el vehículo";
    }

    /**
     * Consulta vehículos con filtros dinámicos.
     */
    public function consultarVehiculo($data)
    {
        $clave = isset($data['clave_vehiculo']) ? trim($data['clave_vehiculo']) : null;
        $modalidad = isset($data['modalidad_vehiculo']) ? trim($data['modalidad_vehiculo']) : null;
        $nomenclatura = isset($data['nomenclatura']) ? trim($data['nomenclatura']) : null;

        $model = new VehiculoModel();
        return $model->consultarVehiculos($clave, $modalidad, $nomenclatura);
    }

    /**
     * Búsqueda para autocompletado (datalist).
     */
    public function buscarVehiculos($texto)
    {
        $model = new VehiculoModel();
        return $model->buscarVehiculos($texto);
    }

    /**
     * Actualización de datos del vehículo.
     */
    public function actualizarVehiculo($data)
    {
        $id = $data['id_vehiculo'] ?? null;
        $clave = trim($data['clave_vehiculo']);
        $modalidad = $data['modalidad_vehiculo'];
        $descripcion = trim($data['descripcion_vehiculo']);
        $chofer = $data['chofer_asignado'];
        $clase = trim($data['clase']);
        $nomenclatura = trim($data['nomenclatura']);
        $peso = floatval($data['peso_bruto_vehicular']);

        if (!$id) return "ID de vehículo inválido";

        $model = new VehiculoModel();

        // Verificar si la clave ya existe en otro vehículo
        $vehiculoExistente = $model->obtenerVehiculoPorClave($clave);
        if ($vehiculoExistente && $vehiculoExistente['id_vehiculo'] != $id) {
            return "La clave de vehículo ya pertenece a otra unidad.";
        }

        // Lógica de Ejes y Llantas (Consistencia)
        $ejes = ($modalidad === 'Carretero') ? intval($data['numero_de_ejes']) : 0;
        $llantas = ($modalidad === 'Carretero') ? intval($data['numero_de_llantas']) : 0;

        $resultado = $model->actualizarVehiculo(
            $id,
            $clave,
            $modalidad,
            $descripcion,
            $chofer,
            $clase,
            $nomenclatura,
            $ejes,
            $llantas,
            $peso
        );

        return $resultado ? "OK" : "Error al actualizar el vehículo";
    }

    /**
     * Obtiene los datos para la confirmación de eliminación.
     */
    public function mostrarVehiculoEliminar($data)
    {
        $model = new VehiculoModel();
        // Puede buscar por ID o por Clave según lo enviado desde el AJAX
        if (isset($data['id'])) {
            return $model->obtenerDatosVehiculoPorId($data['id']);
        } else {
            return $model->obtenerDatosVehiculoPorClave($data['clave']);
        }
    }

    /**
     * Eliminación de vehículo con verificación de dependencias.
     */
    public function eliminarVehiculo($data)
    {
        $id = $data['id_vehiculo'] ?? null;
        if (!$id) return "ID de vehículo inválido";

        $model = new VehiculoModel();
        
        // El modelo debe checar si el vehículo está en uso en la tabla 'fleteros'
        $resultado = $model->eliminarVehiculo($id);

        if ($resultado === "ERROR_DEPENDENCIAS") {
            return "No se puede eliminar: el vehículo tiene viajes o fletes registrados.";
        }

        return $resultado ? "OK" : "Error al eliminar el vehículo";
    }
}