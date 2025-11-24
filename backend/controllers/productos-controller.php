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
}
