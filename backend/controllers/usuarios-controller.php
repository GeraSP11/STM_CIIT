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
    return json_encode([
        "usuario"         => $usuario['nombre_usuario'],
        "nombre_completo" => $nombreCompleto,
        "correo"          => $usuario['correo_electronico'],
        "clave_personal"  => $usuario['curp']
    ]);
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

public function buscarUsuario($data)
{
    $curp = trim($data['curp'] ?? '');
    
    if (empty($curp)) {
        return json_encode([
            "error" => true,
            "message" => "La CURP es obligatoria"
        ]);
    }
    
    $model = new UsuariosModel();
    $usuario = $model->buscarUsuarioPorCurp($curp);
    
    if (!$usuario) {
        return json_encode([
            "error" => true,
            "message" => "No se encontró ningún usuario con esa CURP"
        ]);
    }
    
    $nombreCompleto = $usuario['nombre_personal'] . " " . 
                      $usuario['apellido_paterno'] . " " . 
                      $usuario['apellido_materno'];
    
    return json_encode([
        "error" => false,
        "id_usuario" => $usuario['id_usuario'],
        "nombre_usuario" => $usuario['nombre_usuario'],
        "correo_electronico" => $usuario['correo_electronico'],
        "curp_actual" => $usuario['curp'],
        "id_personal" => $usuario['identificador_de_rh'],
        "nombre_completo" => $nombreCompleto
    ]);
}

public function actualizarUsuario($data)
{
    $id_usuario = trim($data['id_usuario'] ?? '');
    $curp = trim($data['curp'] ?? '');
    $nombre_usuario = trim($data['nombre_usuario'] ?? '');
    $correo = trim($data['correo_electronico'] ?? '');
    $id_personal = trim($data['identificador_de_rh'] ?? '');
    $password = trim($data['contrasena'] ?? '');
    
    if (empty($id_usuario) || empty($curp) || empty($nombre_usuario) || 
        empty($correo) || empty($id_personal)) {
        return "Todos los campos obligatorios deben completarse";
    }
    
    if (strlen($curp) !== 18) {
        return "La CURP debe tener exactamente 18 caracteres";
    }
    
    $model = new UsuariosModel();
    
    $correo_existente = $model->validarCorreoUsuario($correo);
    if ($correo_existente !== null && $correo_existente != $id_usuario) {
        return "El correo ya está registrado por otro usuario";
    }
    
    $passwordHash = null;
    if (!empty($password)) {
        if (strlen($password) < 6) {
            return "La contraseña debe tener al menos 6 caracteres";
        }
        $passwordHash = $password;
    }
    
    $resultado = $model->actualizarUsuario(
        $id_usuario, 
        $curp, 
        $nombre_usuario, 
        $correo, 
        $id_personal, 
        $passwordHash
    );
    
    if ($resultado) {
        return "OK";
    } else {
        return "Error al actualizar el usuario";
    }
}

public function obtenerPersonal()
{
    $model = new UsuariosModel();
    return json_encode($model->obtenerTodoPersonal());
}
}
