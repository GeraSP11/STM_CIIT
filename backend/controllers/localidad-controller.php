<?php
require_once __DIR__ . "/../models/localidad-model.php";

class LocalidadController
{
    public function registrarLocalidad($data)
    {
        $nombreCentro = trim($data['nombre_centro']);
        $ubicacionGeo = trim($data['ubicacionGeo']);
        $poblacion = trim($data['poblacion']);
        $localidad = trim($data['localidad']);
        $estado = trim($data['estado']);
        $tipoInstalacion = trim($data['tipoInstalacion']);

        $model = new LocalidadlModel();
        // Verifica si la CURP ya existe
        if ($model->ubicacionGeoExiste($ubicacionGeo)) {
            return "La ubicación ya está registrada en el sistema.";
        }
        // Registrar
        $resultado = $model->registrarLocalidad(
            $nombreCentro,
            $ubicacionGeo,
            $poblacion,
            $localidad,
            $estado,
            $tipoInstalacion
        );
        return $resultado ? "OK" : "Error al registrar localidad";
    }
    public function consultarLocalidad($data)
    {
        $nombre = isset($data['nombre_centro_trabajo']) ? trim($data['nombre_centro_trabajo']) : null;
        $localidad = isset($data['localidad']) ? trim($data['localidad']) : null;
        $poblacion = isset($data['poblacion']) ? trim($data['poblacion']) : null;
        $estado = isset($data['estado']) ? trim($data['estado']) : null;


        $model = new LocalidadlModel();
        // ORDEN CORRECTO: nombre, localidad, poblacion, estado
        $resultado = $model->consultarLocalidad($nombre, $localidad, $poblacion, $estado);

        return $resultado ?: [];
    }

    public function buscarLocalidades($texto)
    {
        $texto = trim($texto);
        if (strlen($texto) < 2)
            return [];

        $model = new LocalidadlModel();
        $resultado = $model->buscarLocalidades($texto); // Método en el model que devuelva coincidencias
        return $resultado ?: [];
    }
    public function actualizarLocalidad($data)
    {
        $id = $data['id_localidad'] ?? null;
        if (!$id)
            return "ID de localidad inválido";

        $nombreCentro = trim($data['nombre_centro_trabajo']);
        $ubicacionGeo = trim($data['ubicacion_georeferenciada']);
        $poblacion = trim($data['poblacion']);
        $localidad = trim($data['localidad']);
        $estado = trim($data['estado']);
        $tipoInstalacion = trim($data['tipo_instalacion']);

        $model = new LocalidadlModel();
        $resultado = $model->actualizarLocalidad(
            $id,
            $nombreCentro,
            $ubicacionGeo,
            $poblacion,
            $localidad,
            $estado,
            $tipoInstalacion
        );

        return $resultado ? "OK" : "Error al actualizar localidad";
    }
public function eliminarLocalidad($data)
{
    $id = $data['id_localidad'] ?? null;
    
    if (!$id) {
        return "ID de localidad inválido";
    }
    
    $model = new LocalidadlModel();
    $resultado = $model->eliminarLocalidad($id);
    
    if ($resultado === "ERROR_DEPENDENCIAS") {
        return "No se puede eliminar: existen registros de personal vinculados a esta localidad.";
    }
    
    return $resultado ? "OK" : "Error al eliminar localidad";
}
}