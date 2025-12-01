<?php
require_once __DIR__ . "/../config/conexion.php";
class ProductosModel
{

    public function registrarProducto($data)
    {
        global $pdo;

        try {

            $pdo->beginTransaction();

            // Insertar en productos
            $sql = "INSERT INTO productos (
            nombre_producto, peso, altura, largo, ancho, cajas_por_cama,
            camas_por_pallet, peso_soportado, peso_volumetrico,
            ubicacion_producto, tipo_de_embalaje, tipo_de_mercancia,
            unidades_existencia, observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data["nombre_producto"],
                $data["peso_kg"],
                $data["altura_cm"],
                $data["largo_cm"],
                $data["ancho_cm"],
                $data["cajas_por_cama"],
                $data["camas_por_pallet"],
                $data["peso_soportado_kg"],
                $data["peso_volumetrico_kg"],
                $data["id_localidad"],
                $data["tipo_embalaje"],
                $data["tipo_mercancia"],
                $data["unidades_existencia"],
                $data["observaciones_producto"]
            ]);

            // ID seguro dentro de la misma transacción
            $id_producto = $pdo->lastInsertId();

            // Insertar relación
            $sql2 = "INSERT INTO productos_localidades (id_producto, id_localidad)
                 VALUES (?, ?)";

            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$id_producto, $data["id_localidad"]]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return "Error: " . $e->getMessage();
        }
    }


    public function validarProductoExistente($nombre_producto, $id_localidad)
    {
        global $pdo;

        $sql = "SELECT id_producto 
            FROM productos 
            WHERE nombre_producto = ? 
              AND ubicacion_producto = ?
            LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_producto, $id_localidad]);

        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve registro o false
    }


    public function listarLocalidades()
    {
        global $pdo;

        $sql = "SELECT id_localidad, nombre_centro_trabajo, estado FROM localidades";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProductos($termino)
    {
        global $pdo;

        $sql = "SELECT id_producto, nombre_producto, ubicacion_producto
            FROM productos 
            WHERE nombre_producto LIKE ? 
            ORDER BY nombre_producto 
            LIMIT 10";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$termino%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductoPorId($idProducto)
    {
        global $pdo;

        $sql = "SELECT * FROM productos WHERE id_producto = ? LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idProducto]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function consultarProductosPorFiltros($data)
    {
        global $pdo;

        $sql = "
        SELECT 
            p.*, 
            l.nombre_centro_trabajo,
            l.tipo_instalacion,
            l.estado
        FROM productos p
        INNER JOIN localidades l ON l.id_localidad = p.ubicacion_producto
        WHERE 1=1
    ";

        $params = [];

        /* -------------------------------
       FILTRO: NOMBRE DEL PRODUCTO  
    --------------------------------*/
        if (!empty($data["nombre_producto"])) {
            $sql .= " AND LOWER(p.nombre_producto) LIKE LOWER(?)";
            $params[] = "%" . $data["nombre_producto"] . "%";
        }

        /* --------------------------------
       FILTRO SELECCIONADO POR EL USUARIO
    ---------------------------------*/
        if (!empty($data["filtro"])) {

            switch ($data["filtro"]) {

                case "ubicacion":
                    if (!empty($data["id_localidad"])) {
                        $sql .= " AND p.ubicacion_producto = ?";
                        $params[] = $data["id_localidad"];
                    }
                    break;

                case "tipo_mercancia":
                    if (!empty($data["tipo_mercancia"])) {
                        $sql .= " AND p.tipo_de_mercancia = ?";
                        $params[] = $data["tipo_mercancia"];
                    }
                    break;

                case "peso":
                    if (!empty($data["rango_peso"])) {
                        list($min, $max) = explode("-", $data["rango_peso"]);

                        $sql .= " AND p.peso >= ? AND p.peso <= ?";
                        $params[] = floatval($min);
                        $params[] = floatval($max);
                    }
                    break;

                case "existencia":
                    if (!empty($data["cantidad_existencia"])) {
                        $sql .= " AND p.unidades_existencia >= ?";
                        $params[] = intval($data["cantidad_existencia"]);
                    }
                    break;
            }
        }

        /* -------------------------------
       ORDENAR POR NOMBRE SIEMPRE
    --------------------------------*/
        $sql .= " ORDER BY p.nombre_producto ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function actualizarProducto($data)
    {
        global $pdo;
        
        // SQL CON largo, ancho y observaciones
        $sql = "UPDATE productos SET 
                nombre_producto = ?,
                peso = ?,
                altura = ?,
                largo = ?,
                ancho = ?,
                cajas_por_cama = ?,
                camas_por_pallet = ?,
                peso_soportado = ?,
                peso_volumetrico = ?,
                ubicacion_producto = ?,
                tipo_de_embalaje = ?,
                tipo_de_mercancia = ?,
                unidades_existencia = ?,
                observaciones = ?
                WHERE id_producto = ?";
        
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute([
            $data["nombre_producto"],
            $data["peso"] ?? null,
            $data["altura"] ?? null,
            $data["largo"] ?? null,  // NUEVO - Ahora SÍ se guarda
            $data["ancho"] ?? null,  // NUEVO - Ahora SÍ se guarda
            $data["cajas_por_cama"] ?? null,
            $data["camas_por_pallet"] ?? null,
            $data["peso_soportado"] ?? null,
            $data["peso_volumetrico"] ?? null,
            $data["ubicacion_producto"],
            $data["tipo_de_embalaje"],
            $data["tipo_de_mercancia"],
            $data["unidades_existencia"] ?? null,
            $data["observaciones"] ?? null,  // NUEVO
            $data["id_producto"]
        ]);
    }

    public function eliminarProducto($idProducto)
    {
        global $pdo;

        $sql = "DELETE FROM productos WHERE id_producto = ?";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$idProducto]);
    }

    public function listarTodosProductos()
    {
        global $pdo;

        $sql = "SELECT id_producto, nombre_producto 
            FROM productos 
            ORDER BY nombre_producto ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
