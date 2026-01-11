<?php
// =====================================================
//  CONTROLADOR: CARROCERÍAS (Módulo 6.1)
//  Implementa el flujo de registro en dos pasos (RF-GV-CC-01)
// =====================================================

require_once __DIR__ . "/../models/carroceria-model.php";

class CarroceriaController {
    private $model;

    public function __construct() {
        $this->model = new CarroceriaModel();
    }

    /**
     * Registra una carrocería y sus detalles (si aplican)
     */
    public function registrarCarroceria($datos) {
        // 1. Limpieza de datos básicos
        $carroceria = [
            'modalidad'    => $datos['modalidad_carroceria'],
            'matricula'    => trim($datos['matricula']),
            'localidad'    => $datos['localidad_pertenece'],
            'responsable'  => $datos['responsable_carroceria'],
            'tipo'         => $datos['tipo_carroceria'],
            'peso'         => floatval($datos['peso_vehicular']),
            'ejes'         => isset($datos['numero_ejes_vehiculares']) ? intval($datos['numero_ejes_vehiculares']) : 0,
            'contenedores' => isset($datos['numero_contenedores']) ? intval($datos['numero_contenedores']) : 0,
            'estatus'      => 'Disponible' // Valor por defecto bloqueado
        ];

        // 2. Iniciar Proceso de Almacenamiento (Paso 1: Tabla carrocerias)
        $id_carroceria = $this->model->insertarCarroceria($carroceria);

        if (!$id_carroceria) {
            return "Error al registrar los datos principales de la carrocería.";
        }

        // 3. Paso 2: Insertar detalles si el tipo es Carga o Mixta
        if (in_array($carroceria['tipo'], ['Unidad de carga', 'Mixta']) && isset($datos['detalles'])) {
            foreach ($datos['detalles'] as $detalle) {
                // Validar que las dimensiones sean correctas antes de insertar
                if ($detalle['altura'] <= 0 || $detalle['anchura'] <= 0 || $detalle['longitud'] <= 0) {
                    return "Por favor ingresar las dimensiones correctas para el contenedor #" . $detalle['numero_contenedor'];
                }

                $exitoDetalle = $this->model->insertarDetalleCarroceria(
                    $id_carroceria,
                    $detalle['numero_contenedor'],
                    $detalle['longitud'],
                    $detalle['anchura'],
                    $detalle['altura']
                );

                if (!$exitoDetalle) {
                    return "Error al registrar el detalle del contenedor #" . $detalle['numero_contenedor'];
                }
            }
        }

        return "OK";
    }

    /**
     * Consulta con filtros aplicados
     */
    public function consultarCarrocerias($filtros) {
        return $this->model->listarCarrocerias($filtros);
    }

    /**
     * Para autocompletado en actualizar
     */
    public function buscarCarrocerias($texto) {
        return $this->model->buscarPorMatricula($texto);
    }

    /**
     * Actualiza datos (valida que no esté Ensamblada si es necesario)
     */
    public function actualizarCarroceria($datos) {
        // Aquí podrías agregar validaciones de negocio antes del UPDATE
        $exito = $this->model->updateCarroceria($datos);
        return $exito ? "OK" : "Error al actualizar el registro.";
    }

    /**
     * Verifica estatus antes de borrar (RF-GV-CC-04)
     */
    public function mostrarCarroceriaEliminar($datos) {
        return $this->model->obtenerPorMatricula($datos['matricula']);
    }

    public function eliminarCarroceria($datos) {
        // El modelo debe verificar si el estatus es 'Ensamblada'
        $resultado = $this->model->deleteCarroceria($datos['id_carroceria']);
        return $resultado === true ? "OK" : $resultado;
    }
}