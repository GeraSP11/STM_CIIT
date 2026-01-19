<?php
require_once __DIR__ . "/../config/conexion.php";

class PedidosModel
{

    /**
     * Obtener la conexión a la base de datos
     */
    private static function getConnection()
    {
        global $pdo;
        if ($pdo === null) {
            throw new Exception("No hay conexión a la base de datos");
        }
        return $pdo;
    }

    // Registrar pedido
    public static function registrarPedido($data, $productos)
    {
        $conn = self::getConnection();

        try {
            $conn->beginTransaction();

            /* ===========================
           1. Obtener claves de centros
        ============================ */

            $sqlCentro = "
            SELECT clave_centro_trabajo
            FROM localidades
            WHERE id_localidad = :id
        ";

            $stmtCentro = $conn->prepare($sqlCentro);

            // Origen
            $stmtCentro->execute([':id' => $data['localidad_origen']]);
            $claveOrigen = $stmtCentro->fetchColumn();

            // Destino
            $stmtCentro->execute([':id' => $data['localidad_destino']]);
            $claveDestino = $stmtCentro->fetchColumn();

            if (!$claveOrigen || !$claveDestino) {
                throw new Exception('Claves de centro no encontradas');
            }

            /* ===========================
           2. Generar clave del pedido
        ============================ */

            $fecha = date('Ymd');
            $hash  = self::generarHashPedido();

            $clavePedido = "PED-$fecha-$claveOrigen-$claveDestino-$hash";

            /* ===========================
           3. Insertar pedido
        ============================ */

            $sqlPedido = "
            INSERT INTO pedidos (
                clave_pedido,
                localidad_origen,
                localidad_destino,
                estatus_pedido,
                fecha_solicitud,
                fecha_entrega
            ) VALUES (
                :clave,
                :origen,
                :destino,
                'En captura',
                CURRENT_DATE,
                :entrega
            )
            RETURNING id_pedido
        ";

            $stmtPedido = $conn->prepare($sqlPedido);
            $stmtPedido->execute([
                ':clave'   => $clavePedido,
                ':origen'  => $data['localidad_origen'],
                ':destino' => $data['localidad_destino'],
                ':entrega' => $data['fecha_entrega']
            ]);

            $idPedido = $stmtPedido->fetchColumn();

            /* ===========================
           4. Insertar detalles
        ============================ */

            $sqlDetalle = "
                INSERT INTO pedidos_detalles 
                (
                    pedido,
                    identificador_producto,
                    cantidad_producto,
                    observaciones
                ) VALUES (
                    :pedido,
                    :producto,
                    :cantidad,
                    :observaciones
                )
            ";


            $stmtDetalle = $conn->prepare($sqlDetalle);

            

            foreach ($productos as $p) {
                $stmtDetalle->execute([
                    ':pedido'        => $idPedido,
                    ':producto'      => $p['id_producto'],
                    ':cantidad'      => $p['cantidad'],
                    ':observaciones' => $p['observaciones']
                ]);
            }

            /* ===========================
           5. Cambiar estatus
        ============================ */

            $conn->prepare("
            UPDATE pedidos
            SET estatus_pedido = 'En preparación'
            WHERE id_pedido = :id
        ")->execute([':id' => $idPedido]);

            $conn->commit();

            return [
                'success'       => true,
                'id_pedido'     => $idPedido,
                'clave_pedido'  => $clavePedido
            ];
        } catch (Exception $e) {
            $conn->rollBack();

            return [
                'success' => false,
                'message' => 'Error al registrar el pedido',
                // útil en desarrollo:
                // 'error' => $e->getMessage()
            ];
        }
    }

    private static function generarHashPedido(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }


    // Obtener los productos
    public static function obtenerProductos($busqueda, $destino)
    {
        $sql = "
        SELECT
            p.id_producto,
            p.nombre_producto,
            p.peso,
            p.unidades_existencia,
            l.nombre_centro_trabajo AS localidad
        FROM productos p
        JOIN localidades l
            ON l.id_localidad = p.ubicacion_producto
        WHERE p.nombre_producto ILIKE :busqueda
          AND p.ubicacion_producto = :destino
        ORDER BY p.nombre_producto
        LIMIT 50
    ";

        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
        $stmt->bindValue(':destino', $destino, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtener todas las localidades
     */
    public static function obtenerLocalidades()
    {
        try {
            $conn = self::getConnection();

            $query = "SELECT 
                        id_localidad, 
                        nombre_centro_trabajo,
                        clave_centro_trabajo,
                        poblacion,
                        localidad,
                        estado,
                        CONCAT(nombre_centro_trabajo, ' - ', poblacion, ', ', estado) as nombre_completo
                     FROM localidades 
                     ORDER BY nombre_centro_trabajo ASC";

            $stmt = $conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener localidades: " . $e->getMessage());
        }
    }

    /**
     * Buscar pedidos por diferentes criterios
     */
    public static function buscarPedidos($clavePedido = null, $localidadOrigen = null, $localidadDestino = null)
    {
        try {
            $conn = self::getConnection();

            $conditions = [];
            $params = [];

            // Construir condiciones dinámicamente
            if (!empty($clavePedido)) {
                $conditions[] = "p.clave_pedido LIKE :clave_pedido";
                $params[':clave_pedido'] = '%' . $clavePedido . '%';
            }

            if (!empty($localidadOrigen)) {
                $conditions[] = "p.localidad_origen = :localidad_origen";
                $params[':localidad_origen'] = $localidadOrigen;
            }

            if (!empty($localidadDestino)) {
                $conditions[] = "p.localidad_destino = :localidad_destino";
                $params[':localidad_destino'] = $localidadDestino;
            }

            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

            $query = "SELECT 
                        p.id_pedido,
                        p.clave_pedido,
                        p.estatus_pedido,
                        p.fecha_solicitud,
                        p.fecha_entrega,
                        CONCAT(lo.nombre_centro_trabajo, ' - ', lo.poblacion, ', ', lo.estado) as localidad_origen_nombre,
                        CONCAT(ld.nombre_centro_trabajo, ' - ', ld.poblacion, ', ', ld.estado) as localidad_destino_nombre
                     FROM pedidos p
                     LEFT JOIN localidades lo ON p.localidad_origen = lo.id_localidad
                     LEFT JOIN localidades ld ON p.localidad_destino = ld.id_localidad
                     {$whereClause}
                     ORDER BY p.id_pedido DESC";

            $stmt = $conn->prepare($query);

            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al buscar pedidos: " . $e->getMessage());
        }
    }

    /**
     * Obtener un pedido por su ID con toda la información
     */
    public static function obtenerPedidoPorId($idPedido)
    {
        try {
            $conn = self::getConnection();

            $query = "SELECT 
                        p.id_pedido,
                        p.clave_pedido,
                        p.localidad_origen,
                        p.localidad_destino,
                        p.estatus_pedido,
                        p.fecha_solicitud,
                        p.fecha_entrega,
                        p.observaciones,
                        CONCAT(lo.nombre_centro_trabajo, ' - ', lo.poblacion, ', ', lo.estado) as localidad_origen_nombre,
                        CONCAT(ld.nombre_centro_trabajo, ' - ', ld.poblacion, ', ', ld.estado) as localidad_destino_nombre
                     FROM pedidos p
                     LEFT JOIN localidades lo ON p.localidad_origen = lo.id_localidad
                     LEFT JOIN localidades ld ON p.localidad_destino = ld.id_localidad
                     WHERE p.id_pedido = :id_pedido";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pedido) {
                // Formatear fechas si existen
                if ($pedido['fecha_solicitud']) {
                    $pedido['fecha_solicitud'] = date('Y-m-d', strtotime($pedido['fecha_solicitud']));
                }
                if ($pedido['fecha_entrega']) {
                    $pedido['fecha_entrega'] = date('Y-m-d', strtotime($pedido['fecha_entrega']));
                }
            }

            return $pedido;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pedido por ID: " . $e->getMessage());
        }
    }

    /**
     * Actualizar un pedido
     */
    public static function actualizarPedido($idPedido, $datos)
    {
        try {
            $conn = self::getConnection();

            $query = "UPDATE pedidos 
                     SET estatus_pedido = :estatus_pedido,
                         fecha_solicitud = :fecha_solicitud,
                         fecha_entrega = :fecha_entrega,
                         observaciones = :observaciones
                     WHERE id_pedido = :id_pedido";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':estatus_pedido', $datos['estatus_pedido'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_solicitud', $datos['fecha_solicitud'], PDO::PARAM_STR);

            // Para fecha_entrega y observaciones, permitir NULL
            if ($datos['fecha_entrega']) {
                $stmt->bindParam(':fecha_entrega', $datos['fecha_entrega'], PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':fecha_entrega', null, PDO::PARAM_NULL);
            }

            if ($datos['observaciones']) {
                $stmt->bindParam(':observaciones', $datos['observaciones'], PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':observaciones', null, PDO::PARAM_NULL);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar pedido: " . $e->getMessage());
        }
    }

    /**
     * Verificar si existe un pedido
     */
    public static function existePedido($idPedido)
    {
        try {
            $conn = self::getConnection();

            $query = "SELECT COUNT(*) as total FROM pedidos WHERE id_pedido = :id_pedido";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar existencia del pedido: " . $e->getMessage());
        }
    }







    // FUNCIONALIDAD PARA CONSULTAR PEDIDOS
    /**
     * Obtener pedidos con filtros opcionales
     */

    public function obtenerPedidos($clavePedido = null, $origen = null, $destino = null)
    {
        global $pdo;

        if (!$pdo) {
            throw new Exception("No hay conexión a la base de datos");
        }

        $query = "SELECT 
                    p.id_pedido, 
                    p.clave_pedido, 
                    p.estatus_pedido, 
                    p.fecha_solicitud, 
                    p.fecha_entrega, 
                    o.nombre_centro_trabajo AS origen, 
                    d.nombre_centro_trabajo AS destino, 
                    p.observaciones
                FROM pedidos p
                LEFT JOIN localidades o ON p.localidad_origen = o.id_localidad
                LEFT JOIN localidades d ON p.localidad_destino = d.id_localidad
                WHERE 1=1";

        $params = [];

        if ($clavePedido) {
            $query .= " AND p.clave_pedido ILIKE :clavePedido";
            $params[':clavePedido'] = "%$clavePedido%";
        }
        if ($origen) {
            $query .= " AND o.nombre_centro_trabajo ILIKE :origen";
            $params[':origen'] = "%$origen%";
        }
        if ($destino) {
            $query .= " AND d.nombre_centro_trabajo ILIKE :destino";
            $params[':destino'] = "%$destino%";
        }

        $query .= " ORDER BY p.fecha_solicitud DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallesPorPedido($idPedido)
    {

        global $pdo;

        if (!$pdo) {
            throw new Exception("No hay conexión a la base de datos");
        }

        $query = "SELECT 
                    pd.id_pedido_detalles, 
                    pr.nombre_producto AS producto, 
                    pd.cantidad_producto AS cantidad, 
                    pr.unidades_existencia AS unidad,
                    pd.observaciones
                FROM pedidos_detalles pd
                LEFT JOIN productos pr ON pd.identificador_producto = pr.id_producto
                WHERE pd.pedido = :idPedido
                ORDER BY pd.id_pedido_detalles ASC";

        $stmt = $pdo->prepare($query);
        $stmt->execute([':idPedido' => $idPedido]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Función eliminar pedidos */
    public function eliminarPedidos($clavePedido)
    {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // Obtener ID del pedido
            $stmt = $pdo->prepare("
                SELECT id_pedido 
                FROM pedidos 
                WHERE clave_pedido = ?
            ");
            $stmt->execute([$clavePedido]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pedido) {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'El pedido no existe'
                ];
            }

            $idPedido = $pedido['id_pedido'];

            // Eliminar dependencias (orden inverso)
            $pdo->prepare("DELETE FROM envios WHERE identificador_pedido = ?")
                ->execute([$idPedido]);

            $pdo->prepare("DELETE FROM fleteros_detalle 
                        WHERE identificador_flete IN (
                            SELECT id_fletero FROM fleteros
                        )")->execute();

            $pdo->prepare("DELETE FROM fleteros 
                        WHERE id_fletero IN (
                            SELECT identificador_flete 
                            FROM envios 
                            WHERE identificador_pedido = ?
                        )")->execute([$idPedido]);

            $pdo->prepare("DELETE FROM pedidos_detalles WHERE pedido = ?")
                ->execute([$idPedido]);

            // Eliminar pedido
            $pdo->prepare("DELETE FROM pedidos WHERE id_pedido = ?")
                ->execute([$idPedido]);

            $pdo->commit();

            return [
                'success' => true,
                'message' => 'Pedido y registros relacionados eliminados correctamente'
            ];

        } catch (PDOException $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'error' => 'ERROR_BD',
                'message' => 'Error al eliminar pedido: ' . $e->getMessage()
            ];
        }
    }

}
