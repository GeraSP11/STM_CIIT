<?php
// =====================================================
//  CONTROLADOR DE CARROCERÍAS (6.1)
//  Aplica reglas de negocio y comunica con el Modelo
// =====================================================

require_once __DIR__ . "/../models/carroceria-model.php";

class CarroceriaController
{
    /**
     * 6.1.1 Registrar Carrocería
     */
    public function registrarCarroceria($data)
    {
        $matricula = strtoupper(trim($data['matricula']));
        $modalidad = $data['modalidad_carroceria'];
        $tipo = $data['tipo_carroceria'];
        $peso = floatval($data['peso_vehicular']);
        $localidad = intval($data['localidad_pertenece']);
        $responsable = intval($data['responsable_carroceria']);
        
        // Campos condicionales (si no vienen, se ponen en 0)
        $ejes = isset($data['numero_ejes_vehiculares']) ? intval($data['numero_ejes_vehiculares']) : 0;
        $contenedores = isset($data['numero_contenedores']) ? intval($data['numero_contenedores']) : 0;

        $model = new CarroceriaModel();

        // 1. Validar si la matrícula ya existe
        if ($model->matriculaExiste($matricula)) {
            return "Error: La matrícula ya se encuentra registrada.";
        }

        // 2. Aplicar validación de formato de matrícula según modalidad (Regla 6.1.1)
        if (!$this->validarFormatoMatricula($matricula, $modalidad)) {
            return "Error: El formato de la matrícula no es válido para la modalidad $modalidad.";
        }

        // 3. Validar obligatoriedad de Ejes y Contenedores
        if (in_array($modalidad, ['Carretero', 'Ferroviario']) && $ejes <= 0) {
            return "Error: El número de ejes es obligatorio para esta modalidad.";
        }
        if (in_array($tipo, ['Unidad de carga', 'Mixta']) && $contenedores <= 0) {
            return "Error: El número de contenedores es obligatorio para este tipo de carrocería.";
        }

        // 4. Registrar (El estatus se pone como 'Disponible' por defecto en el modelo/BD)
        $resultado = $model->registrarCarroceria(
            $matricula, $localidad, $responsable, 
            $contenedores, $peso, $ejes, 
            $tipo, $modalidad
        );

        return $resultado ? "OK" : "Error al registrar la carrocería en la base de datos.";
    }

    /**
     * 6.1.2 Consultar Carrocerías
     */
    public function consultarCarrocerias($data)
    {
        $filtros = [
            'modalidad' => $data['modalidad_carroceria'] ?? null,
            'tipo'      => $data['tipo_carroceria'] ?? null,
            'estatus'   => $data['estatus_carroceria'] ?? null,
            'matricula' => $data['matricula'] ?? null
        ];

        $model = new CarroceriaModel();
        $resultado = $model->consultarCarrocerias($filtros);

        return $resultado ?: [];
    }

    /**
     * Auxiliar para autocompletado (Búsqueda rápida)
     */
    public function buscarCarrocerias($texto)
    {
        $texto = trim($texto);
        if (strlen($texto) < 2) return [];

        $model = new CarroceriaModel();
        return $model->buscarCarrocerias($texto) ?: [];
    }

    /**
     * 6.1.3 Actualizar Carrocería
     */
    public function actualizarCarroceria($data)
    {
        $id = $data['id_carroceria'] ?? null;
        if (!$id) return "ID de carrocería inválido";

        $matricula = strtoupper(trim($data['matricula']));
        $model = new CarroceriaModel();

        // Verificar si la matrícula ya la tiene otra carrocería
        $existente = $model->obtenerCarroceriaPorMatricula($matricula);
        if ($existente && $existente['id_carroceria'] != $id) {
            return "Error: La matrícula ya está asignada a otra carrocería.";
        }

        // Ejecutar actualización
        $resultado = $model->actualizarCarroceria($id, $data);

        return $resultado ? "OK" : "Error al actualizar la carrocería.";
    }

    /**
     * 6.1.4 Eliminar Carrocería
     */
    public function eliminarCarroceria($data)
    {
        $id = $data['id_carroceria'] ?? null;
        if (!$id) return "ID inválido";

        $model = new CarroceriaModel();
        
        // REGLA DE NEGOCIO: Verificar estatus antes de eliminar
        $carroceria = $model->obtenerCarroceriaPorId($id);
        if ($carroceria && $carroceria['estatus_carroceria'] === 'Ensamblada') {
            return "No se puede eliminar: La carrocería está ensamblada a un vehículo activo.";
        }

        $resultado = $model->eliminarCarroceria($id);
        return $resultado ? "OK" : "Error al intentar eliminar la carrocería.";
    }

    public function mostrarCarroceriaEliminar($data)
    {
        $model = new CarroceriaModel();
        return $model->mostrarCarroceriaEliminar($data);
    }

    /**
     * MÉTODO PRIVADO: Lógica de validación de formatos (Diagrama 6.1.1)
     */
    private function validarFormatoMatricula($matricula, $modalidad)
    {
        switch ($modalidad) {
            case 'Carretero':
                // NIV: 17 caracteres alfanuméricos
                return preg_match('/^[A-Z0-9]{17}$/', $matricula);
            case 'Ferroviario':
                // 12 dígitos numéricos
                return preg_match('/^\d{12}$/', $matricula);
            case 'Marítimo':
                // 7 dígitos numéricos
                return preg_match('/^\d{7}$/', $matricula);
            case 'Aéreo':
                // Alfanumérico de 2 a 7 caracteres
                return preg_match('/^[A-Z0-9]{2,7}$/', $matricula);
            default:
                return false;
        }
    }
}