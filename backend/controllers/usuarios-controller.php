<?php
require_once __DIR__ . "/../models/usuarios-model.php";

class UsuariosController
{
    // Registrar un Usuario
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

    // Consultar un Usuario
    public function consultarUsuario($data)
    {
        $criterio = trim($data['criterio'] ?? '');

        if ($criterio === '') {
            return [
                "error" => true,
                "message" => "Debes ingresar una Clave de Personal para buscar."
            ];
        }

        $model = new UsuariosModel();

        // Buscar usuario por Clave de Personal
        $usuario = $model->consultarUsuarioPorCriterio($criterio);

        if (!$usuario) {
            return [
                "error" => true,
                "message" => "No se encontró ningún usuario con ese criterio."
            ];
        }

        // Construir nombre completo desde datos del personal
        $nombreCompleto = $usuario['nombre_personal'] . " " . $usuario['apellido_paterno'] . " " . $usuario['apellido_materno'];

        // Formato JSON que tu JS espera
        return [
            "usuario"        => $usuario['nombre_usuario'],
            "nombre_completo" => $nombreCompleto,
            "correo"         => $usuario['correo_electronico'],
            "clave_personal" => $usuario['curp']
        ];
    }
    //Eliminar Usuarios
    public function eliminarUsuario($data)
{
    $curp = trim($data['curp'] ?? '');
    
    if (empty($curp)) {
        return "La CURP es obligatoria";
    }
    
    $model = new UsuariosModel();
    
    // Verificar si existe el usuario
    $usuario = $model->consultarUsuarioPorCriterio($curp);
    
    if (!$usuario) {
        return "No se encontró ningún usuario con esa CURP";
    }
    
    // Eliminar usuario
    if ($model->eliminarUsuario($curp)) {
        return "OK";
    } else {
        return "Error al eliminar el usuario";
    }
}
}
