<?php
require_once __DIR__ . '/../models/pedidos-model.php';

class PedidosController
{

    // Obtener productos
    public static function listarProductos($busqueda)
    {

        try {
            $productos = PedidosModel::obtenerProductos($busqueda);

            return [
                'success' => true,
                'data' => $productos
            ];
        } catch (Exception $e) {

            return [
                'success' => false,
                'message' => 'Error al obtener productos'
            ];
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


   public function consultarPedido($data) {
        $idPedido = isset($data['idPedido']) ? trim($data['idPedido']) : '';
        $origen   = isset($data['origen']) ? trim($data['origen']) : '';
        $destino  = isset($data['destino']) ? trim($data['destino']) : '';

        $model = new PedidosModel();
        $pedidos = $model->obtenerPedidos($idPedido, $origen, $destino);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'pedidos' => $pedidos
        ]);
    }





}
