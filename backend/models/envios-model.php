<?php
/**
 * TransporteModel.php
 *
 * Encapsula todo el acceso a datos necesario para:
 *  1) Listar pedidos con estatus_pedido = 'En preparación' filtrados por
 *     rango de fechas (hoy, semana actual o un rango personalizado).
 *  2) Construir la estructura de un problema de transporte (orígenes,
 *     destinos, oferta, demanda y matriz de costos/distancias) a partir
 *     de un conjunto de pedidos seleccionados por el usuario.
 *
 * Reglas de negocio aplicadas (definidas junto con el usuario):
 *
 *  - Orígenes/Destinos: las localidades de origen y destino que aparecen
 *    en los pedidos seleccionados (sin duplicar).
 *  - Costo de cada celda origen-destino: la distancia MÁXIMA (en km)
 *    encontrada en "rutas" para ese par de localidades. Si no existe
 *    ninguna ruta registrada para el par, la celda se marca como
 *    "sin ruta" y se penaliza con una constante muy alta (COSTO_SIN_RUTA)
 *    para que el solucionador evite asignarla salvo que sea inevitable.
 *  - Oferta de un origen: suma de unidades_existencia de TODOS los
 *    productos registrados en esa localidad (tabla productos).
 *  - Demanda de un destino: suma de cantidad_producto (pedidos_detalles)
 *    de TODOS los pedidos seleccionados cuyo destino sea esa localidad.
 *
 * La matriz de costos se construye sobre el producto cartesiano de
 * orígenes x destinos, por lo que también quedan representadas las
 * combinaciones origen-destino para las que no existe ningún pedido
 * entre ellas (su costo se busca igualmente en "rutas").
 */

declare(strict_types=1);

require_once __DIR__ . "/../config/conexion.php";

class TransporteModel
{
    /** Penalización aplicada cuando no existe ninguna ruta registrada entre un par de localidades. */
    public const COSTO_SIN_RUTA = 999999;

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

    private PDO $db;

    public function __construct()
    {
        $this->db = self::getConnection();
    }

    /**
     * Devuelve los pedidos cuya fecha_solicitud cae dentro del rango indicado,
     * incluyendo el nombre de sus localidades y el total de unidades pedidas.
     *
     * Solo se devuelven pedidos con estatus_pedido = 'En preparación', ya que
     * son los únicos elegibles para ser incluidos en una programación de envíos.
     */
    public function obtenerPedidosPorRangoFechas(string $fechaInicio, string $fechaFin): array
    {
        $sql = "
            SELECT
                p.id_pedido,
                p.clave_pedido,
                p.localidad_origen,
                lo.nombre_centro_trabajo AS nombre_origen,
                p.localidad_destino,
                ld.nombre_centro_trabajo AS nombre_destino,
                p.estatus_pedido,
                p.fecha_solicitud,
                p.fecha_entrega,
                COALESCE(SUM(pd.cantidad_producto), 0) AS total_unidades
            FROM pedidos p
            INNER JOIN localidades lo ON lo.id_localidad = p.localidad_origen
            INNER JOIN localidades ld ON ld.id_localidad = p.localidad_destino
            LEFT JOIN pedidos_detalles pd ON pd.pedido = p.id_pedido
            WHERE p.fecha_solicitud BETWEEN :fecha_inicio AND :fecha_fin
              AND p.estatus_pedido = 'En preparación'
            GROUP BY
                p.id_pedido, p.clave_pedido, p.localidad_origen, lo.nombre_centro_trabajo,
                p.localidad_destino, ld.nombre_centro_trabajo, p.estatus_pedido,
                p.fecha_solicitud, p.fecha_entrega
            ORDER BY p.fecha_solicitud DESC, p.id_pedido DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Construye la estructura completa del problema de transporte a partir
     * de los IDs de pedidos seleccionados por el usuario (mínimo 3).
     */
    public function construirProblemaTransporte(array $idsPedidos): array
    {
        $idsPedidos = array_values(array_unique(array_map('intval', $idsPedidos)));

        if (count($idsPedidos) < 3) {
            throw new InvalidArgumentException(
                'Se requieren al menos 3 pedidos para generar la programación de envíos.'
            );
        }

        $pedidos = $this->obtenerPedidosPorIds($idsPedidos);

        if (count($pedidos) !== count($idsPedidos)) {
            throw new RuntimeException('Uno o más pedidos seleccionados ya no existen en el sistema.');
        }

        // Orígenes y destinos únicos presentes en los pedidos seleccionados.
        $origenesIds = array_values(array_unique(array_map(
            'intval',
            array_column($pedidos, 'localidad_origen')
        )));
        $destinosIds = array_values(array_unique(array_map(
            'intval',
            array_column($pedidos, 'localidad_destino')
        )));

        $nombresLocalidades = $this->obtenerNombresLocalidades(array_merge($origenesIds, $destinosIds));
        $ofertaPorOrigen = $this->obtenerOfertaPorLocalidad($origenesIds);
        $demandaPorDestino = $this->obtenerDemandaPorDestino($idsPedidos);
        $distanciasPorPar = $this->obtenerDistanciasMaximas($origenesIds, $destinosIds);

        $origenes = array_map(function (int $id) use ($nombresLocalidades) {
            return ['id' => $id, 'nombre' => $nombresLocalidades[$id] ?? "Localidad #{$id}"];
        }, $origenesIds);

        $destinos = array_map(function (int $id) use ($nombresLocalidades) {
            return ['id' => $id, 'nombre' => $nombresLocalidades[$id] ?? "Localidad #{$id}"];
        }, $destinosIds);

        $oferta = array_map(
            fn (int $id) => (float) ($ofertaPorOrigen[$id] ?? 0),
            $origenesIds
        );

        $demanda = array_map(
            fn (int $id) => (float) ($demandaPorDestino[$id] ?? 0),
            $destinosIds
        );

        $costos = [];
        $rutasDisponibles = [];

        foreach ($origenesIds as $idOrigen) {
            $filaCostos = [];
            $filaDisponible = [];

            foreach ($destinosIds as $idDestino) {
                $clave = $idOrigen . '-' . $idDestino;

                if ((int) $idOrigen === (int) $idDestino) {
                    $filaCostos[] = 0.0;
                    $filaDisponible[] = true;
                } elseif (isset($distanciasPorPar[$clave])) {
                    $filaCostos[] = (float) $distanciasPorPar[$clave];
                    $filaDisponible[] = true;
                } else {
                    $filaCostos[] = (float) self::COSTO_SIN_RUTA;
                    $filaDisponible[] = false;
                }
            }

            $costos[] = $filaCostos;
            $rutasDisponibles[] = $filaDisponible;
        }   

        $pedidosSeleccionados = array_map(function (array $pedido) use ($nombresLocalidades) {
            return [
                'id_pedido' => (int) $pedido['id_pedido'],
                'clave_pedido' => $pedido['clave_pedido'],
                'origen_id' => (int) $pedido['localidad_origen'],
                'origen_nombre' => $nombresLocalidades[(int) $pedido['localidad_origen']] ?? '',
                'destino_id' => (int) $pedido['localidad_destino'],
                'destino_nombre' => $nombresLocalidades[(int) $pedido['localidad_destino']] ?? '',
            ];
        }, $pedidos);

        return [
            'pedidos_seleccionados' => $pedidosSeleccionados,
            'origenes' => $origenes,
            'destinos' => $destinos,
            'oferta' => $oferta,
            'demanda' => $demanda,
            'costos' => $costos,
            'rutas_disponibles' => $rutasDisponibles,
            'total_oferta' => array_sum($oferta),
            'total_demanda' => array_sum($demanda),
            'costo_sin_ruta' => self::COSTO_SIN_RUTA,
        ];
    }

    /**
     * Cambia el estatus de los pedidos indicados a 'En recolección' una vez que
     * la programación de envíos fue confirmada por el usuario.
     *
     * Solo se actualizan pedidos que sigan en 'En preparación' (evita pisar
     * un cambio de estatus hecho por otro proceso/usuario entre la consulta
     * y la confirmación). Se ejecuta dentro de una transacción.
     *
     * @return int Número de pedidos efectivamente actualizados.
     */
    public function actualizarEstatusARecoleccion(array $idsPedidos): int
    {
        $idsPedidos = array_values(array_unique(array_map('intval', $idsPedidos)));

        if (count($idsPedidos) < 3) {
            throw new InvalidArgumentException(
                'Se requieren al menos 3 pedidos para confirmar la programación de envíos.'
            );
        }

        [$inSql, $params] = $this->construirClausulaIn($idsPedidos, 'conf');

        $this->db->beginTransaction();
        try {
            $sql = "
                UPDATE pedidos
                SET estatus_pedido = 'En recolección'
                WHERE id_pedido IN ($inSql)
                  AND estatus_pedido = 'En preparación'
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $filasActualizadas = $stmt->rowCount();

            if ($filasActualizadas !== count($idsPedidos)) {
                $this->db->rollBack();
                throw new InvalidArgumentException(
                    'Uno o más pedidos seleccionados ya no están en estatus "En preparación" ' .
                    'y no pudieron pasar a "En recolección". Vuelve a generar la programación.'
                );
            }

            $this->db->commit();
            return $filasActualizadas;
        } catch (Throwable $excepcion) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $excepcion;
        }
    }

    private function obtenerPedidosPorIds(array $ids): array
    {
        [$inSql, $params] = $this->construirClausulaIn($ids, 'pid');

        $sql = "
            SELECT id_pedido, clave_pedido, localidad_origen, localidad_destino
            FROM pedidos
            WHERE id_pedido IN ($inSql)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    private function obtenerNombresLocalidades(array $ids): array
    {
        $ids = array_values(array_unique($ids));
        if (empty($ids)) {
            return [];
        }

        [$inSql, $params] = $this->construirClausulaIn($ids, 'loc');
        $sql = "
            SELECT id_localidad, nombre_centro_trabajo
            FROM localidades
            WHERE id_localidad IN ($inSql)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $resultado = [];
        foreach ($stmt->fetchAll() as $fila) {
            $resultado[(int) $fila['id_localidad']] = $fila['nombre_centro_trabajo'];
        }

        return $resultado;
    }

    /**
     * Oferta de cada localidad de origen = suma de unidades_existencia
     * de todos los productos registrados ahí (tabla productos).
     */
    private function obtenerOfertaPorLocalidad(array $origenesIds): array
    {
        if (empty($origenesIds)) {
            return [];
        }

        [$inSql, $params] = $this->construirClausulaIn($origenesIds, 'org');
        $sql = "
            SELECT ubicacion_producto, COALESCE(SUM(unidades_existencia), 0) AS oferta
            FROM productos
            WHERE ubicacion_producto IN ($inSql)
            GROUP BY ubicacion_producto
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $resultado = [];
        foreach ($stmt->fetchAll() as $fila) {
            $resultado[(int) $fila['ubicacion_producto']] = (float) $fila['oferta'];
        }

        return $resultado;
    }

    /**
     * Demanda de cada localidad de destino = suma de cantidad_producto
     * (pedidos_detalles) de todos los pedidos SELECCIONADOS cuyo destino
     * sea esa localidad.
     */
    private function obtenerDemandaPorDestino(array $idsPedidos): array
    {
        if (empty($idsPedidos)) {
            return [];
        }

        [$inSql, $params] = $this->construirClausulaIn($idsPedidos, 'ped');
        $sql = "
            SELECT p.localidad_destino, COALESCE(SUM(pd.cantidad_producto), 0) AS demanda
            FROM pedidos p
            INNER JOIN pedidos_detalles pd ON pd.pedido = p.id_pedido
            WHERE p.id_pedido IN ($inSql)
            GROUP BY p.localidad_destino
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $resultado = [];
        foreach ($stmt->fetchAll() as $fila) {
            $resultado[(int) $fila['localidad_destino']] = (float) $fila['demanda'];
        }

        return $resultado;
    }

    /**
     * Para cada par (origen, destino) presente en "rutas", obtiene la
     * distancia MÁXIMA registrada (puede haber varios registros de ruta
     * /modalidades entre el mismo par de localidades).
     *
     * Se consulta sobre el producto cartesiano completo de orígenes x
     * destinos, por lo que también cubre combinaciones para las que no
     * existe ningún pedido entre esas localidades.
     */
    private function obtenerDistanciasMaximas(array $origenesIds, array $destinosIds): array
    {
        if (empty($origenesIds) || empty($destinosIds)) {
            return [];
        }

        [$inSqlOrigenes, $paramsOrigenes] = $this->construirClausulaIn($origenesIds, 'ro');
        [$inSqlDestinos, $paramsDestinos] = $this->construirClausulaIn($destinosIds, 'rd');

        $sql = "
            SELECT localidad_origen, localidad_destino, MAX(distancia) AS distancia
            FROM rutas
            WHERE localidad_origen IN ($inSqlOrigenes)
              AND localidad_destino IN ($inSqlDestinos)
            GROUP BY localidad_origen, localidad_destino
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge($paramsOrigenes, $paramsDestinos));

        $resultado = [];
        foreach ($stmt->fetchAll() as $fila) {
            $clave = ((int) $fila['localidad_origen']) . '-' . ((int) $fila['localidad_destino']);
            $resultado[$clave] = (float) $fila['distancia'];
        }

        return $resultado;
    }

    /**
     * Construye una cláusula IN parametrizada (con placeholders nombrados
     * únicos) para evitar inyección SQL al filtrar por listas de IDs.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function construirClausulaIn(array $valores, string $prefijo): array
    {
        $placeholders = [];
        $params = [];
        foreach (array_values($valores) as $idx => $valor) {
            $nombre = ":{$prefijo}{$idx}";
            $placeholders[] = $nombre;
            $params[$nombre] = $valor;
        }

        return [implode(',', $placeholders), $params];
    }
}
