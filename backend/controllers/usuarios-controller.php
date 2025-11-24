<?php
require_once __DIR__ . "/../models/usuarios-model.php";

class UsuariosController
{

    public function registrarUsuario($data)
    {
        $nombre = trim($data['nombre_usuario']);
        $correo = trim($data['email']);
        $clave_personal = trim($data['clave_personal']);
        $password = trim($data['password']);

        $model = new UsuariosModel();

        // Validar si el correo ya existe
        $correo_existente = $model->validarCorreoUsuario($correo);

        if ($correo_existente !== null) {
            return "El correo ya está registrado. Intente con otro.";
        }

        // Validar que exista el personal
        $id_personal = $model->consultarIDCalaveParaUsuario($clave_personal);

        if ($id_personal === null) {
            return "La clave personal no existe o no está registrada.";
        }

        // Validar clave de personal no usada por otro usuario
        $personal_usado = $model->validarClavePersonalUsuario($id_personal);

        if ($personal_usado !== null) {
            return "La clave personal ya está asociada a otro usuario.";
        }

        // Registrar usuario
        $registro = $model->registrarUsuario($nombre, $correo, $id_personal, $password);

        if ($registro) {
            return "OK";
        } else {
            return "Ocurrió un error al registrar el usuario.";
        }
    }
}
