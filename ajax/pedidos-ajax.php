<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../backend/controllers/pedidos-controller.php';

// Obtener la acción solicitada
$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : '');

try {
    $controller = new PedidosController();
    
    switch ($accion) {
        case 'listarProductos':
            $busqueda = $_POST['busqueda'] ?? '';
            echo json_encode($controller::listarProductos($busqueda));
            break;

        case 'obtener_localidades':
            obtenerLocalidades($controller);
            break;
            
        case 'buscar':
            buscarPedidos($controller);
            break;
            
        case 'obtener':
            obtenerPedido($controller);
            break;
            
        case 'actualizar':
            actualizarPedido($controller);
            break;
        case 'consultar-pedidos':
            $controller->consultarPedido($_POST);
            break;
        
        case 'detalle-pedido':
            $controller->obtenerDetallePedido($_POST);
            break;


        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}

// Finalizar el buffer de salida
exit;

/**
 * Obtener todas las localidades
 */
function obtenerLocalidades($controller) {
    try {
        $localidades = $controller->obtenerLocalidades();
        
        echo json_encode([
            'success' => true,
            'localidades' => $localidades
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener localidades: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Buscar pedidos por criterios
 */
function buscarPedidos($controller) {
    try {
        $clavePedido = isset($_POST['clave_pedido']) ? trim($_POST['clave_pedido']) : null;
        $localidadOrigen = isset($_POST['localidad_origen']) ? intval($_POST['localidad_origen']) : null;
        $localidadDestino = isset($_POST['localidad_destino']) ? intval($_POST['localidad_destino']) : null;
        
        // Validar que al menos un criterio esté presente
        if (empty($clavePedido) && empty($localidadOrigen) && empty($localidadDestino)) {
            echo json_encode([
                'success' => false,
                'message' => 'Debe proporcionar al menos un criterio de búsqueda'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $pedidos = $controller->buscarPedidos($clavePedido, $localidadOrigen, $localidadDestino);
        
        echo json_encode([
            'success' => true,
            'pedidos' => $pedidos
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al buscar pedidos: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Obtener un pedido por ID
 */
function obtenerPedido($controller) {
    try {
        $idPedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($idPedido <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de pedido inválido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $pedido = $controller->obtenerPedidoPorId($idPedido);
        
        if ($pedido) {
            echo json_encode([
                'success' => true,
                'pedido' => $pedido
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener pedido: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Actualizar un pedido
 */
function actualizarPedido($controller) {
    try {
        $idPedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;
        $estatusPedido = isset($_POST['estatus_pedido']) ? trim($_POST['estatus_pedido']) : '';
        $fechaSolicitud = isset($_POST['fecha_solicitud']) ? trim($_POST['fecha_solicitud']) : '';
        $fechaEntrega = isset($_POST['fecha_entrega']) ? trim($_POST['fecha_entrega']) : null;
        $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : null;
        
        // Validaciones
        if ($idPedido <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de pedido inválido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (empty($estatusPedido)) {
            echo json_encode([
                'success' => false,
                'message' => 'El estatus es requerido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if (empty($fechaSolicitud)) {
            echo json_encode([
                'success' => false,
                'message' => 'La fecha de solicitud es requerida'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        // Validar estatus
        $estatusValidos = ['En captura', 'En preparación', 'En recolección', 'Enviado', 'En tránsito', 'En reparto', 'Entregado'];
        if (!in_array($estatusPedido, $estatusValidos)) {
            echo json_encode([
                'success' => false,
                'message' => 'Estatus inválido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $resultado = $controller->actualizarPedido($idPedido, $estatusPedido, $fechaSolicitud, $fechaEntrega, $observaciones);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Pedido actualizado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo actualizar el pedido'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar pedido: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}