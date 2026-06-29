<?php
/**
 * TransporteController.php
 *
 * Controlador HTTP/AJAX del módulo de Programación de Envíos.
 * Recibe la acción solicitada, valida la entrada, delega en
 * TransporteModel y siempre responde en formato JSON:
 *
 *   { "success": true,  "data": ... }
 *   { "success": false, "message": "..." }
 */

declare(strict_types=1);

require_once __DIR__ . '/../models/envios-model.php';

class EnviosController
{
    private TransporteModel $model;

    public function __construct()
    {
        $this->model = new TransporteModel();
    }

    public function manejarSolicitud(string $accion): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            switch ($accion) {
                case 'listar_pedidos':
                    $this->listarPedidos();
                    break;

                case 'generar_problema':
                    $this->generarProblema();
                    break;

                case 'confirmar_programacion':
                    $this->confirmarProgramacion();
                    break;

                default:
                    throw new InvalidArgumentException('Acción no reconocida.');
            }
        } catch (InvalidArgumentException $excepcion) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $excepcion->getMessage()]);
        } catch (Throwable $excepcion) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $excepcion->getMessage()]);
        }
    }

    /**
     * GET listar_pedidos?tipo_filtro=hoy|semana|personalizado&fecha_inicio=AAAA-MM-DD&fecha_fin=AAAA-MM-DD
     *
     * "tipo_filtro" determina cómo se calcula el rango de fechas en el servidor:
     *   - hoy:           fecha_solicitud = fecha actual.
     *   - semana:        fecha_solicitud dentro de la semana actual (lunes a domingo).
     *   - personalizado: usa fecha_inicio y fecha_fin enviados por el cliente.
     *
     * En todos los casos solo se devuelven pedidos con estatus_pedido = 'En preparación'.
     */
    private function listarPedidos(): void
    {
        $tipoFiltro = $_GET['tipo_filtro'] ?? $_POST['tipo_filtro'] ?? 'hoy';
        $tipoFiltro = (string) $tipoFiltro;

        [$fechaInicio, $fechaFin] = $this->resolverRangoFechas($tipoFiltro);

        $pedidos = $this->model->obtenerPedidosPorRangoFechas($fechaInicio, $fechaFin);

        echo json_encode([
            'success' => true,
            'data' => $pedidos,
            'rango' => ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin],
        ]);
    }

    /**
     * Calcula el rango de fechas (fecha_inicio, fecha_fin) según el tipo de filtro solicitado.
     *
     * @return array{0: string, 1: string}
     */
    private function resolverRangoFechas(string $tipoFiltro): array
    {
        switch ($tipoFiltro) {
            case 'hoy':
                $hoy = (new DateTimeImmutable('today'))->format('Y-m-d');
                return [$hoy, $hoy];

            case 'semana':
                $hoy = new DateTimeImmutable('today');
                // ISO-8601: la semana inicia en lunes (1) y termina en domingo (7).
                $inicioSemana = $hoy->modify('monday this week');
                $finSemana = $hoy->modify('sunday this week');
                return [$inicioSemana->format('Y-m-d'), $finSemana->format('Y-m-d')];

            case 'personalizado':
                $fechaInicio = $_GET['fecha_inicio'] ?? $_POST['fecha_inicio'] ?? null;
                $fechaFin = $_GET['fecha_fin'] ?? $_POST['fecha_fin'] ?? null;

                if (!$fechaInicio || !$fechaFin) {
                    throw new InvalidArgumentException(
                        'Para un rango personalizado debes indicar fecha_inicio y fecha_fin.'
                    );
                }

                $this->validarFecha((string) $fechaInicio);
                $this->validarFecha((string) $fechaFin);

                if ($fechaInicio > $fechaFin) {
                    throw new InvalidArgumentException('La fecha inicial no puede ser posterior a la fecha final.');
                }

                return [(string) $fechaInicio, (string) $fechaFin];

            default:
                throw new InvalidArgumentException(
                    "Tipo de filtro no reconocido: {$tipoFiltro}. Usa 'hoy', 'semana' o 'personalizado'."
                );
        }
    }

    /**
     * POST generar_problema  pedidos[]=1&pedidos[]=2&pedidos[]=3...
     */
    private function generarProblema(): void
    {
        $idsPedidos = $_POST['pedidos'] ?? [];

        if (!is_array($idsPedidos) || count($idsPedidos) < 3) {
            throw new InvalidArgumentException(
                'Selecciona al menos 3 pedidos para generar la programación de envíos.'
            );
        }

        $problema = $this->model->construirProblemaTransporte($idsPedidos);

        echo json_encode(['success' => true, 'data' => $problema]);
    }

    /**
     * POST confirmar_programacion  pedidos[]=1&pedidos[]=2&pedidos[]=3...
     *
     * Se llama cuando el usuario confirma la solución de transporte mostrada
     * en pantalla. Cambia el estatus de los pedidos involucrados de
     * 'En preparación' a 'En recolección'.
     */
    private function confirmarProgramacion(): void
    {
        $idsPedidos = $_POST['pedidos'] ?? [];

        if (!is_array($idsPedidos) || count($idsPedidos) < 3) {
            throw new InvalidArgumentException(
                'Selecciona al menos 3 pedidos para confirmar la programación de envíos.'
            );
        }

        $totalActualizados = $this->model->actualizarEstatusARecoleccion($idsPedidos);

        echo json_encode([
            'success' => true,
            'data' => ['pedidos_actualizados' => $totalActualizados],
        ]);
    }

    private function validarFecha(string $fecha): void
    {
        $partes = explode('-', $fecha);
        if (count($partes) !== 3 || !checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])) {
            throw new InvalidArgumentException("Fecha inválida: {$fecha}. Usa el formato AAAA-MM-DD.");
        }
    }
}
