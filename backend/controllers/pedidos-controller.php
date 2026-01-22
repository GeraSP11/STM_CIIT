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

    // Obteneer el id para seleccionar la localidad del usuario en sison
    public static function obtenerLocalidadUsuario()
    {
        session_start();

        if (!isset($_SESSION['id_personal'])) {
            echo json_encode(null);
            return;
        }

        $idPersonal = $_SESSION['id_personal'];

        $idLocalidad = PedidosModel::obtenerIdLocalidadUsuario($idPersonal);

        echo json_encode([
            'id_localidad' => $idLocalidad
        ]);
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
            $clavePedido = isset($data['clavePedido']) ? trim($data['clavePedido']) : null;
            $origen   = isset($data['origen']) ? trim($data['origen']) : null;
            $destino  = isset($data['destino']) ? trim($data['destino']) : null;

            // Validar que al menos haya un filtro
            if (empty($clavePedido) && empty($origen) && empty($destino)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Debe proporcionar al menos un filtro de búsqueda'
                ]);
                return;
            }

            $model = new PedidosModel();

            $pedidos = $model->obtenerPedidos($clavePedido, $origen, $destino);

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
            $model = new PedidosModel();

            $idPedido = $data['idPedido'] ?? null;
            $clavePedido = $data['clavePedido'] ?? null;

            // 1. Resolver pedido
            if ($idPedido) {
                $pedidoArray = $model->obtenerPedidos(null, null, null);
                foreach ($pedidoArray as $p) {
                    if ($p['id_pedido'] == $idPedido) {
                        $pedido = $p;
                        break;
                    }
                }
            } elseif ($clavePedido) {
                $pedidoArray = $model->obtenerPedidos($clavePedido);
                $pedido = $pedidoArray[0] ?? null;
            }

            if (empty($pedido)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Pedido no encontrado'
                ]);
                return;
            }

            // 2. Obtener detalles SIEMPRE por ID
            $detalles = $model->obtenerDetallesPorPedido($pedido['id_pedido']);

            echo json_encode([
                'success' => true,
                'pedido' => $pedido,
                'detalles' => $detalles
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }



    /*
        Funcionalida elimar pedidos
    */
    public function eliminarPedido($data)
    {
        header('Content-Type: application/json');

        $clavePedido = $data['clavePedido'] ?? null;

        if (!$clavePedido) {
            echo json_encode([
                'success' => false,
                'message' => 'Clave de pedido inválida'
            ]);
            return;
        }

        $model = new PedidosModel();
        $resultado = $model->eliminarPedidos($clavePedido);

        echo json_encode($resultado);
    }
}
