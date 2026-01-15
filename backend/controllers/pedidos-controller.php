<?php
require_once __DIR__ . '/../models/pedidos-model.php';

class PedidosController
{
    //Registrar pedido
    public static function registrarPedido($data, $productos)
    {
        try {
            return PedidosModel::registrarPedido($data, $productos);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error interno al registrar el pedido'
            ];
        }
    }

    // Obtener productos
    public static function listarProductos($busqueda, $destino)
    {
        try {
            if (!$destino) {
                return [];
            }

            return PedidosModel::obtenerProductos($busqueda, $destino);
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Obtener todas las localidades
     */
    public function obtenerLocalidades()
    {
        try {
            return PedidosModel::obtenerLocalidades();
        } catch (Exception $e) {
            throw new Exception("Error en el controlador al obtener localidades: " . $e->getMessage());
        }
    }

    /**
     * Buscar pedidos por diferentes criterios
     */
    public function buscarPedidos($clavePedido = null, $localidadOrigen = null, $localidadDestino = null)
    {
        try {
            return PedidosModel::buscarPedidos($clavePedido, $localidadOrigen, $localidadDestino);
        } catch (Exception $e) {
            throw new Exception("Error en el controlador al buscar pedidos: " . $e->getMessage());
        }
    }

    /**
     * Obtener un pedido por su ID con toda la información
     */
    public function obtenerPedidoPorId($idPedido)
    {
        try {
            if (!is_numeric($idPedido) || $idPedido <= 0) {
                throw new Exception("ID de pedido inválido");
            }

            $pedido = PedidosModel::obtenerPedidoPorId($idPedido);

            if (!$pedido) {
                return null;
            }

            return $pedido;
        } catch (Exception $e) {
            throw new Exception("Error en el controlador al obtener pedido: " . $e->getMessage());
        }
    }

    /**
     * Actualizar un pedido
     */
    public function actualizarPedido($idPedido, $estatusPedido, $fechaSolicitud, $fechaEntrega = null, $observaciones = null)
    {
        try {
            // Validaciones
            if (!is_numeric($idPedido) || $idPedido <= 0) {
                throw new Exception("ID de pedido inválido");
            }

            if (empty($estatusPedido)) {
                throw new Exception("El estatus es requerido");
            }

            if (empty($fechaSolicitud)) {
                throw new Exception("La fecha de solicitud es requerida");
            }

            // Validar formato de fechas
            if (!$this->validarFecha($fechaSolicitud)) {
                throw new Exception("Formato de fecha de solicitud inválido");
            }

            if ($fechaEntrega && !$this->validarFecha($fechaEntrega)) {
                throw new Exception("Formato de fecha de entrega inválido");
            }

            // Validar que la fecha de entrega sea posterior a la de solicitud
            if ($fechaEntrega && strtotime($fechaEntrega) < strtotime($fechaSolicitud)) {
                throw new Exception("La fecha de entrega debe ser posterior a la fecha de solicitud");
            }

            $datos = [
                'estatus_pedido' => $estatusPedido,
                'fecha_solicitud' => $fechaSolicitud,
                'fecha_entrega' => $fechaEntrega,
                'observaciones' => $observaciones
            ];

            return PedidosModel::actualizarPedido($idPedido, $datos);
        } catch (Exception $e) {
            throw new Exception("Error en el controlador al actualizar pedido: " . $e->getMessage());
        }
    }

    /**
     * Validar formato de fecha (YYYY-MM-DD)
     */
    private function validarFecha($fecha)
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }


    // FUNCIONALIDAD CONSULTAR PEDIDO.
    /**
     * Consultar pedidos con filtros
     * Acción: consultar-pedidos
     */
    public function consultarPedido($data)
    {
        try {
            $idPedido = isset($data['idPedido']) ? trim($data['idPedido']) : null;
            $origen   = isset($data['origen']) ? trim($data['origen']) : null;
            $destino  = isset($data['destino']) ? trim($data['destino']) : null;

            // Validar que al menos haya un filtro
            if (empty($idPedido) && empty($origen) && empty($destino)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Debe proporcionar al menos un filtro de búsqueda'
                ]);
                return;
            }

            $model = new PedidosModel();
            $pedidos = $model->obtenerPedidos($idPedido, $origen, $destino);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'pedidos' => $pedidos
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al consultar pedidos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalle completo de un pedido
     * Acción: detalle-pedido
     * 
     * WORKAROUND: Como pedidos_detalles no tiene id_pedido,
     * retornamos todos los detalles disponibles
     */
    public function obtenerDetallePedido($data)
    {
        try {
            $idPedido = isset($data['idPedido']) ? intval($data['idPedido']) : 0;

            if (!$idPedido) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de pedido inválido'
                ]);
                return;
            }

            $model = new PedidosModel();

            // Obtener información general del pedido
            $pedidos = $model->obtenerPedidos(null, null, null);

            // Buscar el pedido específico por id_pedido
            $pedido = null;
            foreach ($pedidos as $p) {
                if ($p['id_pedido'] == $idPedido) {
                    $pedido = $p;
                    break;
                }
            }

            if (!$pedido) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Pedido no encontrado'
                ]);
                return;
            }

            // WORKAROUND: Como no hay relación, obtenemos todos los detalles
            // En producción esto mostrará productos que pueden no ser del pedido
            $detalles = $model->obtenerTodosLosDetalles();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'pedido' => $pedido,
                'detalles' => $detalles,
                'advertencia' => 'Los productos mostrados pueden no corresponder específicamente a este pedido debido a limitaciones en la estructura de la BD'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }
}
