<?php
require_once __DIR__ . "/../models/personal-model.php";

class PersonalController
{
    public function registrarPersonal($data)
    {
        $nombre = trim($data['nombre_personal']);
        $apellidoPaterno = trim($data['apellido_paterno']);
        $apellidoMaterno = trim($data['apellido_materno']);
        $curp = trim($data['curp']);
        $cargo = trim($data['cargo']);
        $afiliacionLaboral = trim($data['afiliacion_laboral']);

        $model = new PersonalModel();

        // Verifica si la CURP ya existe
        if ($model->curpExiste($curp)) {
            return "La CURP ya está registrada en el sistema.";
        }

        // Registrar
        $resultado = $model->registrarPersonal(
            $nombre,
            $apellidoPaterno,
            $apellidoMaterno,
            $curp,
            $cargo,
            $afiliacionLaboral
        );

        return $resultado ? "OK" : "Error al registrar personal";
    }

    public function obtenerLocalidades()
    {
        $model = new PersonalModel();
        return $model->obtenerLocalidades();
    }

    public function listarPersonal($data)
    {
        $curp = trim($data['curp']);
        $model = new PersonalModel();

        // Verifica si la CURP existe
        if ($model->curpExiste($curp)) {
            // Devuelve un array con la información del personal
            return $model->listarPersonal($curp); // asegurarte de que listarPersonal acepte CURP como filtro
        } else {
            // Devuelve un array vacío para que el frontend maneje "no hay resultados"
            return [];
        }
    }

}