<?php

class ProductosModel {

    public function registrarProducto($data) {
        global $pdo;

        $sql = "INSERT INTO productos (
            nombre_producto, peso, altura, cajas_por_cama,
            camas_por_pallet, peso_soportado, peso_volumetrico,
            ubicacion_producto, tipo_de_embalaje, tipo_mercancia, unidades_existencia
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $data["nombre_producto"],
            $data["peso_kg"],
            $data["altura_m"],
            $data["cajas_por_cama"],
            $data["cajas_por_pallet"],
            $data["peso_soportado_kg"],
            $data["peso_volumetrico_kg"],
            $data["id_localidad"],
            $data["tipo_embalaje"],
            $data["tipo_mercancia"],
            $data["unidad_existencia"]
        ]);
    }

    public function listarLocalidades() {
        global $pdo;

        $sql = "SELECT id_localidad, nombre_centro_trabajo, estado FROM localidades";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>