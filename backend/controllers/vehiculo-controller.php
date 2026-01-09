<?php
require_once __DIR__ . "/../models/vehiculo-model.php";

class VehiculoController
{
    /**
     * Registra un nuevo vehículo en el sistema.
     */
    public function registrarVehiculo($data)
    {
        $placas = strtoupper(trim($data['placas'])); // Placas siempre en mayúsculas
        $marca = trim($data['marca']);
        $modelo = trim($data['modelo']);
        $capacidadCarga = trim($data['capacidad_carga']);
        $tipoVehiculo = trim($data['tipo_vehiculo']);
        $idCarroceria = trim($data['id_carroceria']); // Llave foránea

        $model = new VehiculoModel();

        // Validar si la placa ya existe
        if ($model->placaExiste($placas)) {
            return "Las placas ya están registradas en el sistema.";
        }

        // Registrar
        $resultado = $model->registrarVehiculo(
            $placas,
            $marca,
            $modelo,
            $capacidadCarga,
            $tipoVehiculo,
            $idCarroceria
        );

        return $resultado ? "OK" : "Error al registrar el vehículo";
    }

    /**
     * Consulta vehículos basados en filtros dinámicos.
     */
    public function consultarVehiculos($data)
    {
        $placas = isset($data['placas']) ? trim($data['placas']) : null;
        $marca = isset($data['marca']) ? trim($data['marca']) : null;
        $modelo = isset($data['modelo']) ? trim($data['modelo']) : null;
        $tipo = isset($data['tipo_vehiculo']) ? trim($data['tipo_vehiculo']) : null;

        $model = new VehiculoModel();
        $resultado = $model->consultarVehiculos($placas, $marca, $modelo, $tipo);

        return $resultado ?: [];
    }

    /**
     * Busca coincidencias de placas para el autocompletado (datalist).
     */
    public function buscarVehiculoPorPlaca($texto)
    {
        $texto = trim($texto);
        if (strlen($texto) < 2) return [];

        $model = new VehiculoModel();
        $resultado = $model->buscarPorPlaca($texto);
        return $resultado ?: [];
    }

    /**
     * Actualiza la información de un vehículo existente.
     */
    public function actualizarVehiculo($data)
    {
        $id = $data['id_vehiculo'] ?? null;
        if (!$id) return "ID de vehículo inválido";

        $placas = strtoupper(trim($data['placas']));
        $marca = trim($data['marca']);
        $modelo = trim($data['modelo']);
        $capacidad = trim($data['capacidad_carga']);
        $tipo = trim($data['tipo_vehiculo']);
        $carroceria = trim($data['id_carroceria']);

        $model = new VehiculoModel();

        // Verificar que las nuevas placas no las tenga otro vehículo diferente
        $vehiculoExistente = $model->obtenerVehiculoPorPlaca($placas);
        if ($vehiculoExistente && $vehiculoExistente['id_vehiculo'] != $id) {
            return "Las placas ya pertenecen a otro vehículo registrado.";
        }

        $resultado = $model->actualizarVehiculo(
            $id,
            $placas,
            $marca,
            $modelo,
            $capacidad,
            $tipo,
            $carroceria
        );

        return $resultado ? "OK" : "Error al actualizar el vehículo";
    }

    /**
     * Obtiene datos de un vehículo específico para la vista previa de eliminación.
     */
    public function obtenerDatosVehiculo($data)
    {
        $model = new VehiculoModel();
        $resultado = $model->obtenerDatosVehiculo($data);
        return $resultado;
    }

    /**
     * Elimina un vehículo del sistema.
     */
    public function eliminarVehiculo($data)
    {
        $id = $data['id_vehiculo'] ?? null;
        if (!$id) return "ID de vehículo inválido";

        $model = new VehiculoModel();
        
        // El modelo debería verificar si hay fleteros o viajes asociados antes de borrar
        $resultado = $model->eliminarVehiculo($id);

        if ($resultado === "ERROR_DEPENDENCIAS") {
            return "No se puede eliminar: existen fletes o registros asociados a este vehículo.";
        }

        return $resultado ? "OK" : "Error al eliminar el vehículo";
    }
}