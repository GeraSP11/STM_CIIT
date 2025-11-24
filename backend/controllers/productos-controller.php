<?php
require_once __DIR__ . "/../models/productos-model.php";

class ProductosController
{

    public function registrarProducto($data)
    {
        $model = new ProductosModel();

        // Validar que no exista un producto igual en esa ubicación
        $productoExistente = $model->validarProductoExistente(
            $data["nombre_producto"],
            $data["id_localidad"]
        );

        if ($productoExistente) {
            return "Este producto ya está registrado en esta localidad.";
        }

        $ok = $model->registrarProducto($data);

        return $ok ? "OK" : "No se pudo registrar el producto";
    }

    public function listarLocalidades()
    {
        $model = new ProductosModel();
        return $model->listarLocalidades();
    }

    public function buscarProductos($data)
{
    $termino = $data["termino"] ?? "";
    
    if (strlen($termino) < 2) {
        return ["error" => "El término de búsqueda debe tener al menos 2 caracteres"];
    }
    
    $model = new ProductosModel();
    return $model->buscarProductos($termino);
}

public function obtenerProductoPorId($data)
{
    $idProducto = $data["id_producto"] ?? 0;
    
    if (!$idProducto) {
        return ["error" => "ID de producto no válido"];
    }
    
    $model = new ProductosModel();
    $producto = $model->obtenerProductoPorId($idProducto);
    
    if (!$producto) {
        return ["error" => "Producto no encontrado"];
    }
    
    return $producto;
}

public function actualizarProducto($data)
{
    $idProducto = $data["id_producto"] ?? 0;
    
    if (!$idProducto) {
        return "ID de producto no válido";
    }
    
    $model = new ProductosModel();
    
    // Verificar que el producto existe
    $productoExiste = $model->obtenerProductoPorId($idProducto);
    if (!$productoExiste) {
        return "El producto no existe";
    }
    
    $ok = $model->actualizarProducto($data);
    
    return $ok ? "OK" : "No se pudo actualizar el producto";
}

public function eliminarProducto($data)
{
    $idProducto = $data["id_producto"] ?? 0;
    
    if (!$idProducto) {
        return "ID de producto no válido";
    }
    
    $model = new ProductosModel();
    
    // Verificar que el producto existe
    $productoExiste = $model->obtenerProductoPorId($idProducto);
    if (!$productoExiste) {
        return "El producto no existe";
    }
    
    $ok = $model->eliminarProducto($idProducto);
    
    return $ok ? "OK" : "No se pudo eliminar el producto";
}

public function listarTodosProductos()
{
    $model = new ProductosModel();
    return $model->listarTodosProductos();
}
}
