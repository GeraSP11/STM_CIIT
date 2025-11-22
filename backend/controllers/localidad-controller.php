<?php
require_once __DIR__ . "/../models/localidad-model.php";

class LocalidadController
{
    public function registrarPersonal($data)
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
}