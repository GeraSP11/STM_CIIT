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
public function registrarCarroceria($data)
{
    try {
        $model = new CarroceriaModel();

        // 1. Datos principales
        $carroceria = [
            'modalidad'    => $data['modalidad_carroceria'],
            'matricula'    => strtoupper(trim($data['matricula'])),
            'localidad'    => $data['localidad_pertenece'],
            'responsable'  => $data['responsable_carroceria'],
            'tipo'         => $data['tipo_carroceria'],
            'peso'         => $data['peso_vehicular'],
            'ejes'         => !empty($data['numero_ejes_vehiculares']) ? $data['numero_ejes_vehiculares'] : 0,
            // --- AQUÍ ESTABA EL ERROR: Faltaba esta línea ---
            'contenedores' => !empty($data['numero_contenedores']) ? $data['numero_contenedores'] : 0, 
            'estatus'      => 'Disponible'
        ];

        if ($model->matriculaExiste($carroceria['matricula'])) {
            return "La matrícula " . $carroceria['matricula'] . " ya existe.";
        }

        $id_carroceria = $model->insertarCarroceria($carroceria);
        if (!$id_carroceria) return "Error al registrar los datos principales.";

        // 2. CORRECCIÓN ERROR 2: Bucle de detalles
        // Verificamos si vienen arreglos de dimensiones (longitud[], anchura[], altura[])
        if (isset($data['longitud']) && is_array($data['longitud'])) {
            foreach ($data['longitud'] as $index => $valorLongitud) {
                $detalle = [
                    'id_carroceria'     => $id_carroceria,
                    'numero_contenedor' => ($index + 1),
                    'longitud'          => $data['longitud'][$index],
                    'anchura'           => $data['anchura'][$index],
                    'altura'            => $data['altura'][$index]
                ];
                
                if (!$model->insertarDetalleCarroceria($detalle)) {
                    return "Error al registrar el contenedor #" . ($index + 1);
                }
            }
        }

        return "OK";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
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