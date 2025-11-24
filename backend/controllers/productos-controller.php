<?php
class ProductosController {

    public function registrarProducto($data) {
        $model = new ProductosModel();

        $ok = $model->registrarProducto($data);

        return $ok
            ? ["success" => true]
            : ["error" => "No se pudo registrar el producto"];
    }

    public function listarLocalidades() {
        $model = new ProductosModel();
        return $model->listarLocalidades();
    }
}
?>