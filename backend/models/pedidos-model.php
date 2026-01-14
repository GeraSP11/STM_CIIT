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

    // Obtener los productos
    public static function obtenerProductos($busqueda)
    {

        $sql = "
            SELECT
                p.id_producto,
                p.nombre_producto,
                p.peso,
                l.nombre_centro_trabajo AS localidad
            FROM productos p
            JOIN localidades l
                ON l.id_localidad = p.ubicacion_producto
            WHERE p.nombre_producto ILIKE :busqueda
            ORDER BY p.nombre_producto
            LIMIT 50
        ";
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
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
     * Retorna información general del pedido (sin productos)
     */
    public function obtenerPedidos($idPedido = null, $origen = null, $destino = null) {
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

        if ($idPedido) {
            $query .= " AND p.clave_pedido = :idPedido";
            $params[':idPedido'] = $idPedido;
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

    /**
     * Obtener TODOS los detalles de productos
     * WORKAROUND: Como pedidos_detalles NO tiene id_pedido,
     * retornamos todos los detalles y los filtramos en el controlador
     */
    public function obtenerTodosLosDetalles() {
        global $pdo;

        if (!$pdo) {
            throw new Exception("No hay conexión a la base de datos");
        }

        $query = "SELECT 
                    pd.id_pedido_detalles, 
                    pr.nombre_producto AS producto, 
                    pd.cantidad_producto AS cantidad, 
                    pr.tipo_de_embalaje AS unidad,
                    pd.observaciones
                FROM pedidos_detalles pd
                LEFT JOIN productos pr ON pd.identificador_producto = pr.id_producto
                ORDER BY pd.id_pedido_detalles ASC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}