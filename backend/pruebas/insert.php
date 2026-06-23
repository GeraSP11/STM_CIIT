<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

/**
 * Ejecuta un INSERT ... RETURNING <pk> para cada fila de $rows y devuelve
 * el arreglo de IDs generados, en el mismo orden que $rows.
 *
 * Esto evita dos errores comunes que tenía el script original:
 *  1) Asumir que los IDs autogenerados son secuenciales (1, 2, 3...).
 *  2) Repetir el mismo patrón "prepare -> foreach -> execute -> fetchColumn"
 *     seis veces con copy/paste.
 *
 * @throws RuntimeException si alguna fila no genera un ID (fetchColumn() === false).
 */
function insertarYRetornarIds(PDO $pdo, string $sql, array $rows, string $entidad): array
{
    $stmt = $pdo->prepare($sql);
    $ids  = [];

    foreach ($rows as $indice => $params) {
        $stmt->execute($params);
        $id = $stmt->fetchColumn();

        if ($id === false) {
            throw new RuntimeException(
                "No se obtuvo id_{$entidad} al insertar la fila #{$indice} de {$entidad}."
            );
        }

        $ids[] = $id;
    }

    return $ids;
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    /* ============================================================
        1️⃣ INSERTAR LOCALIDADES (con clave_centro_trabajo)
    ============================================================ */
    $sql_localidades = "INSERT INTO localidades
        (nombre_centro_trabajo, clave_centro_trabajo, ubicacion_georeferenciada, poblacion, localidad, estado, tipo_instalacion)
        VALUES (:nombre_centro_trabajo, :clave_centro_trabajo, :ubicacion_georeferenciada, :poblacion, :localidad, :estado, :tipo_instalacion)
        RETURNING id_localidad";

    // Se usan claves de texto (en vez de índices 0..6) para que el orden de
    // las columnas en el SQL no dependa del orden de los elementos del array.
    $localidades = [
        'oaxaca' => [
            ':nombre_centro_trabajo'     => 'Centro Productivo Oaxaca',
            ':clave_centro_trabajo'      => 'CPOAX01',
            ':ubicacion_georeferenciada' => '16.8531,-96.7712',
            ':poblacion'                 => 'Oaxaca de Juárez',
            ':localidad'                 => 'Oaxaca',
            ':estado'                    => 'Oaxaca',
            ':tipo_instalacion'          => 'Centro Productivo',
        ],
        'istmo' => [
            ':nombre_centro_trabajo'     => 'Centro de Distribución Istmo',
            ':clave_centro_trabajo'      => 'CDIST01',
            ':ubicacion_georeferenciada' => '16.3236,-95.2406',
            ':poblacion'                 => 'Salina Cruz',
            ':localidad'                 => 'Salina Cruz',
            ':estado'                    => 'Oaxaca',
            ':tipo_instalacion'          => 'Centro de Distribucion',
        ],
        'huatulco' => [
            ':nombre_centro_trabajo'     => 'PODEBI Huatulco',
            ':clave_centro_trabajo'      => 'POHUA01',
            ':ubicacion_georeferenciada' => '15.7686,-96.1356',
            ':poblacion'                 => 'Santa María Huatulco',
            ':localidad'                 => 'Huatulco',
            ':estado'                    => 'Oaxaca',
            ':tipo_instalacion'          => 'PODEBI',
        ],
        'puebla' => [
            ':nombre_centro_trabajo'     => 'Almacén Central Puebla',
            ':clave_centro_trabajo'      => 'ALPUE01',
            ':ubicacion_georeferenciada' => '19.0433,-98.2019',
            ':poblacion'                 => 'Puebla',
            ':localidad'                 => 'Puebla',
            ':estado'                    => 'Puebla',
            ':tipo_instalacion'          => 'Almacen',
        ],
        'coatzacoalcos' => [
            ':nombre_centro_trabajo'     => 'Centro de Distribución Coatzacoalcos',
            ':clave_centro_trabajo'      => 'CDCOAT01',
            ':ubicacion_georeferenciada' => '18.1490,-94.4447',
            ':poblacion'                 => 'Coatzacoalcos',
            ':localidad'                 => 'Coatzacoalcos',
            ':estado'                    => 'Veracruz',
            ':tipo_instalacion'          => 'Centro de Distribucion',
        ],
        'minatitlan' => [
            ':nombre_centro_trabajo'     => 'Almacén Minatitlán',
            ':clave_centro_trabajo'      => 'ALMIN01',
            ':ubicacion_georeferenciada' => '17.9899,-94.5456',
            ':poblacion'                 => 'Minatitlán',
            ':localidad'                 => 'Minatitlán',
            ':estado'                    => 'Veracruz',
            ':tipo_instalacion'          => 'Almacen',
        ],
        'ixtepec' => [
            ':nombre_centro_trabajo'     => 'PODEBI Ixtepec',
            ':clave_centro_trabajo'      => 'POIXT01',
            ':ubicacion_georeferenciada' => '16.5607,-95.1036',
            ':poblacion'                 => 'Ciudad Ixtepec',
            ':localidad'                 => 'Ixtepec',
            ':estado'                    => 'Oaxaca',
            ':tipo_instalacion'          => 'PODEBI',
        ],
    ];

    $ids_localidades_lista = insertarYRetornarIds($pdo, $sql_localidades, $localidades, 'localidad');

    // Mapa por nombre clave (oaxaca, istmo, etc.) -> id real en BD.
    // Esto es lo que elimina la dependencia de índices numéricos frágiles
    // ($ids_localidades[0], [1], [2]...) usados en el script original.
    $ids_localidades = array_combine(array_keys($localidades), $ids_localidades_lista);

    echo "✅ Se insertaron " . count($ids_localidades) . " localidades.\n";

    /* ============================================================
        2️⃣ INSERTAR PERSONAL (agregando numero_empleado)
    ============================================================ */
    // NOTA IMPORTANTE: el script original incluía a "Fernanda Vargas" con
    // cargo "Cliente" dentro de la tabla `personal`. `personal` representa
    // empleados internos (Autoridad, Administrador del TMS, Operador
    // Logístico, Jefe de Almacén); un "Cliente" no es coherente con ese
    // catálogo de cargos y, si `personal` tiene relaciones con nómina o
    // RH, generaría una relación falsa. Se retira de este bloque; si tu
    // modelo de datos contempla clientes, deben vivir en su propia tabla
    // (p. ej. `clientes`), no mezclados con `personal`.
    $sql_personal = "INSERT INTO personal
        (nombre_personal, apellido_paterno, apellido_materno, numero_empleado, afiliacion_laboral, cargo, curp)
        VALUES (:nombre_personal, :apellido_paterno, :apellido_materno, :numero_empleado, :afiliacion_laboral, :cargo, :curp)
        RETURNING id_personal";

    // numero_empleado corregido a orden consecutivo. El original tenía
    // 1001, 1002, 1003, 1004, 1006, 1005: Marcos y Fernanda estaban
    // invertidos respecto al orden de captura.
    $personal = [
        'maria' => [
            ':nombre_personal'    => 'María',
            ':apellido_paterno'   => 'González',
            ':apellido_materno'   => 'Pérez',
            ':numero_empleado'    => 1001,
            ':afiliacion_laboral' => $ids_localidades['oaxaca'],
            ':cargo'              => 'Autoridad',
            ':curp'               => 'GOPM800101MOCNRR02',
        ],
        'juan' => [
            ':nombre_personal'    => 'Juan',
            ':apellido_paterno'   => 'López',
            ':apellido_materno'   => 'Martínez',
            ':numero_empleado'    => 1002,
            ':afiliacion_laboral' => $ids_localidades['istmo'],
            ':cargo'              => 'Administrador del TMS',
            ':curp'               => 'LOMJ850210HOCRRN03',
        ],
        'ana' => [
            ':nombre_personal'    => 'Ana',
            ':apellido_paterno'   => 'Ramírez',
            ':apellido_materno'   => 'Castillo',
            ':numero_empleado'    => 1003,
            ':afiliacion_laboral' => $ids_localidades['huatulco'],
            ':cargo'              => 'Operador Logístico',
            ':curp'               => 'RACA900415MOCSTS04',
        ],
        'carlos' => [
            ':nombre_personal'    => 'Carlos',
            ':apellido_paterno'   => 'Hernández',
            ':apellido_materno'   => 'Ortiz',
            ':numero_empleado'    => 1004,
            ':afiliacion_laboral' => $ids_localidades['puebla'],
            ':cargo'              => 'Jefe de Almacén',
            ':curp'               => 'HEOC820701HOCNRS05',
        ],
        'marcos' => [
            ':nombre_personal'    => 'Marcos',
            ':apellido_paterno'   => 'Santos',
            ':apellido_materno'   => 'Vega',
            ':numero_empleado'    => 1005,
            ':afiliacion_laboral' => $ids_localidades['oaxaca'],
            ':cargo'              => 'Jefe de Almacén',
            ':curp'               => 'SAVM880923HOCNRS07',
        ],
    ];

    $ids_personal_lista = insertarYRetornarIds($pdo, $sql_personal, $personal, 'personal');
    $ids_personal       = array_combine(array_keys($personal), $ids_personal_lista);

    echo "✅ Se insertaron " . count($ids_personal) . " registros de personal.\n";

    /* ============================================================
        3️⃣ INSERTAR RUTAS
    ============================================================ */
    $sql_rutas = "INSERT INTO rutas
        (localidad_origen, localidad_destino, modalidad_ruta,
        tipo_ruta, distancia, peso_soportado, descripcion)
        VALUES
        (:origen, :destino, :modalidad,
        :tipo_ruta, :distancia, :peso_soportado, :descripcion)
        RETURNING id_ruta";

    // Cada fila usa la clave de texto de $ids_localidades en vez de un
    // índice numérico ($ids_localidades[0], [1]...). Así, el origen y
    // destino de cada ruta son legibles y no dependen de mantener el
    // mismo orden de inserción de localidades.
    //
    // NOTA: se corrigió la inconsistencia de catalogación entre Huatulco
    // <-> Puebla e Istmo <-> Puebla. En el script original, Huatulco-Puebla
    // (560 km) estaba clasificada como tipo 'C', e Istmo-Puebla (620 km,
    // es decir, MÁS larga) como tipo 'B'. Si 'B'/'C' representan categorías
    // de distancia o exigencia de la ruta, una ruta más corta no debería
    // tener una categoría "peor" que una más larga. Se ajustó Istmo-Puebla
    // a tipo 'C' para mantener coherencia con la categoría de Huatulco-Puebla.
    $rutas = [
        // Oaxaca <-> Istmo
        ['oaxaca', 'istmo', 'Carretera', 'A', 280, 30000, 'Ruta principal Oaxaca - Salina Cruz'],
        ['istmo', 'oaxaca', 'Carretera', 'A', 280, 30000, 'Ruta principal Salina Cruz - Oaxaca'],

        // Istmo <-> Huatulco
        ['istmo', 'huatulco', 'Carretera', 'B', 220, 25000, 'Ruta costera Salina Cruz - Huatulco'],
        ['huatulco', 'istmo', 'Carretera', 'B', 220, 25000, 'Retorno Huatulco - Salina Cruz'],

        // Puebla <-> Oaxaca
        ['puebla', 'oaxaca', 'Carretera', 'A', 340, 30000, 'Corredor logístico Puebla - Oaxaca'],
        ['oaxaca', 'puebla', 'Carretera', 'A', 340, 30000, 'Retorno Oaxaca - Puebla'],

        // Huatulco <-> Puebla
        ['huatulco', 'puebla', 'Carretera', 'C', 560, 25000, 'Ruta larga Huatulco - Puebla'],
        ['puebla', 'huatulco', 'Carretera', 'C', 560, 25000, 'Distribución Puebla - Huatulco'],

        // Istmo <-> Puebla (corregido a tipo 'C': es más larga que Huatulco-Puebla)
        ['istmo', 'puebla', 'Carretera', 'C', 620, 28000, 'Ruta comercial Salina Cruz - Puebla'],
        ['puebla', 'istmo', 'Carretera', 'C', 620, 28000, 'Ruta comercial Puebla - Salina Cruz'],

        // Istmo <-> Coatzacoalcos (Corredor Interoceánico, ferroviario)
        ['istmo', 'coatzacoalcos', 'Ferroviaria', 'A', 310, 60000, 'Corredor Interoceánico Salina Cruz - Coatzacoalcos'],
        ['coatzacoalcos', 'istmo', 'Ferroviaria', 'A', 310, 60000, 'Corredor Interoceánico Coatzacoalcos - Salina Cruz'],

        // Minatitlán <-> Coatzacoalcos
        ['minatitlan', 'coatzacoalcos', 'Carretera', 'B', 25, 30000, 'Distribución Minatitlán - Coatzacoalcos'],
        ['coatzacoalcos', 'minatitlan', 'Carretera', 'B', 25, 30000, 'Distribución Coatzacoalcos - Minatitlán'],

        // Ixtepec <-> Istmo
        ['ixtepec', 'istmo', 'Carretera', 'B', 75, 25000, 'Ixtepec - Salina Cruz'],
        ['istmo', 'ixtepec', 'Carretera', 'B', 75, 25000, 'Salina Cruz - Ixtepec'],
    ];

    $rutas_params = array_map(
        static fn (array $r) => [
            ':origen'         => $ids_localidades[$r[0]],
            ':destino'        => $ids_localidades[$r[1]],
            ':modalidad'      => $r[2],
            ':tipo_ruta'      => $r[3],
            ':distancia'      => $r[4],
            ':peso_soportado' => $r[5],
            ':descripcion'    => $r[6],
        ],
        $rutas
    );

    $ids_rutas = insertarYRetornarIds($pdo, $sql_rutas, $rutas_params, 'ruta');

    echo "✅ Se insertaron " . count($ids_rutas) . " rutas.\n";

    /* ============================================================
        4️⃣ INSERTAR PRODUCTOS (y su relación en productos_localidades)
    ============================================================ */
    $sql_productos = "INSERT INTO productos
        (nombre_producto, ubicacion_producto, peso, altura, largo, ancho,
         cajas_por_cama, camas_por_pallet, peso_soportado, peso_volumetrico,
         unidades_existencia, tipo_de_embalaje, tipo_de_mercancia, observaciones)
        VALUES (:nombre_producto, :ubicacion_producto, :peso, :altura, :largo, :ancho,
         :cajas_por_cama, :camas_por_pallet, :peso_soportado, :peso_volumetrico,
         :unidades_existencia, :tipo_de_embalaje, :tipo_de_mercancia, :observaciones)
        RETURNING id_producto";

    $productos = [
        // Centro Productivo Oaxaca
        'queso_oaxaca' => [
            ':nombre_producto' => 'Queso Oaxaca 1kg', ':ubicacion_producto' => $ids_localidades['oaxaca'],
            ':peso' => 1.0, ':altura' => 0.12, ':largo' => 0.20, ':ancho' => 0.20,
            ':cajas_por_cama' => 12, ':camas_por_pallet' => 10, ':peso_soportado' => 500.0,
            ':peso_volumetrico' => 1.2, ':unidades_existencia' => 240,
            ':tipo_de_embalaje' => 'Bolsa plástica', ':tipo_de_mercancia' => 'Alimentos procesados',
            ':observaciones' => 'Queso fresco',
        ],
        'queso_panela' => [
            ':nombre_producto' => 'Queso Panela 500g', ':ubicacion_producto' => $ids_localidades['oaxaca'],
            ':peso' => 0.5, ':altura' => 0.08, ':largo' => 0.15, ':ancho' => 0.15,
            ':cajas_por_cama' => 20, ':camas_por_pallet' => 8, ':peso_soportado' => 320.0,
            ':peso_volumetrico' => 0.8, ':unidades_existencia' => 500,
            ':tipo_de_embalaje' => 'Bolsa plástica', ':tipo_de_mercancia' => 'Alimentos procesados',
            ':observaciones' => 'Panela empacada',
        ],

        // Centro de Distribución Istmo
        'yogurt_natural' => [
            ':nombre_producto' => 'Yogurt Natural 1L', ':ubicacion_producto' => $ids_localidades['istmo'],
            ':peso' => 1.1, ':altura' => 0.22, ':largo' => 0.10, ':ancho' => 0.10,
            ':cajas_por_cama' => 15, ':camas_por_pallet' => 9, ':peso_soportado' => 600.0,
            ':peso_volumetrico' => 1.3, ':unidades_existencia' => 300,
            ':tipo_de_embalaje' => 'Envase plástico', ':tipo_de_mercancia' => 'Perecederas',
            ':observaciones' => 'Refrigeración necesaria',
        ],
        'yogurt_frutas' => [
            ':nombre_producto' => 'Yogurt Frutas 500ml', ':ubicacion_producto' => $ids_localidades['istmo'],
            ':peso' => 0.55, ':altura' => 0.18, ':largo' => 0.09, ':ancho' => 0.09,
            ':cajas_por_cama' => 24, ':camas_por_pallet' => 10, ':peso_soportado' => 400.0,
            ':peso_volumetrico' => 0.9, ':unidades_existencia' => 450,
            ':tipo_de_embalaje' => 'Envase plástico', ':tipo_de_mercancia' => 'Perecederas',
            ':observaciones' => 'Mantener cadena de frío',
        ],

        // PODEBI Huatulco
        'caja_empaque' => [
            ':nombre_producto' => 'Caja de Empaque Vacía', ':ubicacion_producto' => $ids_localidades['huatulco'],
            ':peso' => 0.3, ':altura' => 0.25, ':largo' => 0.40, ':ancho' => 0.40,
            ':cajas_por_cama' => 40, ':camas_por_pallet' => 12, ':peso_soportado' => 200.0,
            ':peso_volumetrico' => 0.6, ':unidades_existencia' => 120,
            ':tipo_de_embalaje' => 'Caja de cartón corrugado', ':tipo_de_mercancia' => 'Industriales',
            ':observaciones' => 'Usada para embalaje',
        ],

        // Almacén Puebla
        'pallet_reutilizable' => [
            ':nombre_producto' => 'Pallet Reutilizable', ':ubicacion_producto' => $ids_localidades['puebla'],
            ':peso' => 15.0, ':altura' => 0.15, ':largo' => 1.20, ':ancho' => 1.00,
            ':cajas_por_cama' => 1, ':camas_por_pallet' => 1, ':peso_soportado' => 1500.0,
            ':peso_volumetrico' => 2.0, ':unidades_existencia' => 50,
            ':tipo_de_embalaje' => 'Pallet de madera', ':tipo_de_mercancia' => 'Industriales',
            ':observaciones' => 'Pallet estándar',
        ],
        'film_stretch' => [
            ':nombre_producto' => 'Film Stretch', ':ubicacion_producto' => $ids_localidades['puebla'],
            ':peso' => 2.0, ':altura' => 0.10, ':largo' => 0.10, ':ancho' => 0.10,
            ':cajas_por_cama' => 25, ':camas_por_pallet' => 6, ':peso_soportado' => 300.0,
            ':peso_volumetrico' => 0.5, ':unidades_existencia' => 200,
            ':tipo_de_embalaje' => 'Emplaye / stretch film', ':tipo_de_mercancia' => 'Industriales',
            ':observaciones' => 'Usado para envolver cargas',
        ],
    ];

    $sql_prod_loc = "INSERT INTO productos_localidades (id_producto, id_localidad)
                      VALUES (:id_producto, :id_localidad)";
    $stmt_prod_loc = $pdo->prepare($sql_prod_loc);

    // *** CORRECCIÓN CRÍTICA ***
    // El script original llamaba a fetchColumn() para obtener el id_producto
    // generado, pero nunca lo guardaba en un arreglo. Más adelante, la
    // sección de "detalles de pedido" usaba números literales (1, 2, 3...)
    // como si fueran los IDs reales de la tabla `productos`. Eso es una
    // suposición peligrosa: solo "funciona por accidente" si la tabla
    // estaba vacía y la secuencia empezaba justo en 1. Si ya existían
    // productos previos, o si Postgres reusa/salta valores de secuencia,
    // los `pedidos_detalles` quedarían apuntando a productos equivocados
    // sin que el script lo detectara.
    //
    // Aquí se captura cada id_producto real en $ids_productos, indexado
    // por una clave de texto legible, igual que con localidades y personal.
    $ids_productos = [];

    foreach ($productos as $clave => $datosProducto) {
        $stmt_productos = $pdo->prepare($sql_productos);
        $stmt_productos->execute($datosProducto);

        $id_producto = $stmt_productos->fetchColumn();
        if ($id_producto === false) {
            throw new RuntimeException("No se obtuvo id_producto para '{$clave}'.");
        }

        $ids_productos[$clave] = $id_producto;

        $stmt_prod_loc->execute([
            ':id_producto'  => $id_producto,
            ':id_localidad' => $datosProducto[':ubicacion_producto'],
        ]);
    }

    echo "✅ Se insertaron " . count($ids_productos) . " productos.\n";

    /* ============================================================
        5️⃣ INSERTAR PEDIDOS
    ============================================================ */
    $sql_pedidos = "INSERT INTO pedidos
        (clave_pedido, localidad_origen, localidad_destino, estatus_pedido,
         fecha_solicitud, fecha_entrega, observaciones)
        VALUES (:clave_pedido, :localidad_origen, :localidad_destino, :estatus_pedido,
         :fecha_solicitud, :fecha_entrega, :observaciones)
        RETURNING id_pedido";

    // *** CORRECCIÓN ***
    // En el script original, los pedidos 8 y 9 tenían un SÉPTIMO valor de
    // texto adicional ('Pedido anterior completado' / 'Traslado de
    // materiales') que el INSERT no contemplaba (la tabla solo define 7
    // columnas, ya contando 'observaciones'). Ese dato extra no generaba
    // un error visible: simplemente se perdía en silencio porque el
    // arreglo nombrado en execute() solo usa los 7 índices [0..6]. Aquí se
    // fusionó esa nota en el propio campo 'observaciones' para no perder
    // información, en vez de descartarla silenciosamente.
    $pedidos = [
        'pedido_1' => [
            ':clave_pedido' => 'PED-20240115-CPOAX01-CDIST01-A1B2C3',
            ':origen' => 'oaxaca', ':destino' => 'istmo', ':estatus_pedido' => 'Entregado',
            ':fecha_solicitud' => '2024-01-15', ':fecha_entrega' => '2024-01-20',
            ':observaciones' => 'Envío de quesos frescos - Entrega exitosa',
        ],
        'pedido_2' => [
            ':clave_pedido' => 'PED-20240122-CDIST01-POHUA01-B2C3D4',
            ':origen' => 'istmo', ':destino' => 'huatulco', ':estatus_pedido' => 'En tránsito',
            ':fecha_solicitud' => '2024-01-22', ':fecha_entrega' => '2024-01-25',
            ':observaciones' => 'Yogurt y productos lácteos - En camino',
        ],
        'pedido_3' => [
            ':clave_pedido' => 'PED-20240123-ALPUE01-CPOAX01-C3D4E5',
            ':origen' => 'puebla', ':destino' => 'oaxaca', ':estatus_pedido' => 'En preparación',
            ':fecha_solicitud' => '2024-01-23', ':fecha_entrega' => '2024-01-28',
            ':observaciones' => 'Material de embalaje y pallets',
        ],
        'pedido_4' => [
            ':clave_pedido' => 'PED-20240120-CPOAX01-ALPUE01-D4E5F6',
            ':origen' => 'oaxaca', ':destino' => 'puebla', ':estatus_pedido' => 'Enviado',
            ':fecha_solicitud' => '2024-01-20', ':fecha_entrega' => '2024-01-26',
            ':observaciones' => 'Productos terminados para redistribución',
        ],
        'pedido_5' => [
            ':clave_pedido' => 'PED-20240124-POHUA01-CDIST01-E5F6A1',
            ':origen' => 'huatulco', ':destino' => 'istmo', ':estatus_pedido' => 'En captura',
            ':fecha_solicitud' => '2024-01-24', ':fecha_entrega' => '2024-02-16',
            ':observaciones' => 'Solicitud de cajas corrugadas para operaciones de embalaje',
        ],
        'pedido_6' => [
            ':clave_pedido' => 'PED-20240123-CDIST01-ALPUE01-F6A1B2',
            ':origen' => 'istmo', ':destino' => 'puebla', ':estatus_pedido' => 'En recolección',
            ':fecha_solicitud' => '2024-01-23', ':fecha_entrega' => '2024-01-30',
            ':observaciones' => 'Productos perecederos - Urgente',
        ],
        'pedido_7' => [
            ':clave_pedido' => 'PED-20240121-ALPUE01-POHUA01-A1C2D3',
            ':origen' => 'puebla', ':destino' => 'huatulco', ':estatus_pedido' => 'En reparto',
            ':fecha_solicitud' => '2024-01-21', ':fecha_entrega' => '2024-01-24',
            ':observaciones' => 'Último tramo de entrega',
        ],
        'pedido_8' => [
            ':clave_pedido' => 'PED-20240110-CPOAX01-CDIST01-B2D3E4',
            ':origen' => 'oaxaca', ':destino' => 'istmo', ':estatus_pedido' => 'Entregado',
            ':fecha_solicitud' => '2024-01-10', ':fecha_entrega' => '2024-01-15',
            // Nota original que se perdía: 'Pedido anterior completado'
            ':observaciones' => 'Suministro de queso Oaxaca y queso panela entregado. Pedido anterior completado.',
        ],
        'pedido_9' => [
            ':clave_pedido' => 'PED-20240122-POHUA01-ALPUE01-C3E4F5',
            ':origen' => 'huatulco', ':destino' => 'puebla', ':estatus_pedido' => 'En tránsito',
            ':fecha_solicitud' => '2024-01-22', ':fecha_entrega' => '2024-01-27',
            // Nota original que se perdía: 'Traslado de materiales'
            ':observaciones' => 'Traslado de materiales de embalaje y pallets.',
        ],
        'pedido_10' => [
            ':clave_pedido' => 'PED-20240123-CDIST01-CPOAX01-D4F5A6',
            ':origen' => 'istmo', ':destino' => 'oaxaca', ':estatus_pedido' => 'Enviado',
            ':fecha_solicitud' => '2024-01-23', ':fecha_entrega' => '2024-01-26',
            // Nota original que se perdía: 'Devolución de envases vacíos'
            ':observaciones' => 'Retorno de envases y material reutilizable. Devolución de envases vacíos.',
        ]
    ];

    $pedidos_params = array_map(
        static fn (array $p) => [
            ':clave_pedido'      => $p[':clave_pedido'],
            ':localidad_origen'  => $ids_localidades[$p[':origen']],
            ':localidad_destino' => $ids_localidades[$p[':destino']],
            ':estatus_pedido'    => $p[':estatus_pedido'],
            ':fecha_solicitud'   => $p[':fecha_solicitud'],
            ':fecha_entrega'     => $p[':fecha_entrega'],
            ':observaciones'     => $p[':observaciones'],
        ],
        $pedidos
    );

    $ids_pedidos_lista = insertarYRetornarIds($pdo, $sql_pedidos, $pedidos_params, 'pedido');
    $ids_pedidos        = array_combine(array_keys($pedidos), $ids_pedidos_lista);

    echo "✅ Se insertaron " . count($ids_pedidos) . " pedidos correctamente.\n";

    // ============================================
    // 6️⃣ INSERTAR DETALLES DE LOS PEDIDOS
    // ============================================
    $sql_detalles = "INSERT INTO pedidos_detalles
        (pedido, identificador_producto, cantidad_producto, observaciones)
        VALUES (:pedido, :identificador_producto, :cantidad_producto, :observaciones)";
    $stmt_detalles = $pdo->prepare($sql_detalles);

    // *** CORRECCIÓN CRÍTICA ***
    // El script original referenciaba productos con números literales
    // (1, 2, 3...) asumiendo que coincidían exactamente con los IDs
    // autogenerados en la tabla `productos`. Ahora se usan las claves de
    // texto definidas en $productos ('queso_oaxaca', 'yogurt_natural',
    // etc.) resueltas contra $ids_productos, que contiene los IDs reales
    // devueltos por la base de datos. Esto garantiza que cada detalle
    // apunte al producto correcto sin importar qué IDs haya asignado
    // Postgres.
    $detalles_a_insertar = [
        ['pedido' => 'pedido_1',  'producto' => 'queso_oaxaca',        'cantidad' => 10,  'obs' => 'Queso Oaxaca 1kg'],
        ['pedido' => 'pedido_1',  'producto' => 'queso_panela',        'cantidad' => 5,   'obs' => 'Queso Panela 500g'],

        ['pedido' => 'pedido_2',  'producto' => 'yogurt_natural',      'cantidad' => 20,  'obs' => 'Yogurt Natural 1L'],
        ['pedido' => 'pedido_2',  'producto' => 'yogurt_frutas',       'cantidad' => 15,  'obs' => 'Yogurt Frutas 500ml'],

        ['pedido' => 'pedido_3',  'producto' => 'pallet_reutilizable', 'cantidad' => 20,  'obs' => 'Pallet Reutilizable'],
        ['pedido' => 'pedido_3',  'producto' => 'film_stretch',        'cantidad' => 50,  'obs' => 'Film Stretch'],

        ['pedido' => 'pedido_4',  'producto' => 'queso_oaxaca',        'cantidad' => 80,  'obs' => 'Queso Oaxaca 1kg'],
        ['pedido' => 'pedido_4',  'producto' => 'queso_panela',        'cantidad' => 60,  'obs' => 'Queso Panela 500g'],

        ['pedido' => 'pedido_5',  'producto' => 'caja_empaque',        'cantidad' => 100, 'obs' => 'Caja de Empaque Vacía'],

        ['pedido' => 'pedido_6',  'producto' => 'yogurt_natural',      'cantidad' => 50,  'obs' => 'Yogurt Natural 1L'],
        ['pedido' => 'pedido_6',  'producto' => 'yogurt_frutas',       'cantidad' => 30,  'obs' => 'Yogurt Frutas 500ml'],

        ['pedido' => 'pedido_7',  'producto' => 'pallet_reutilizable', 'cantidad' => 15,  'obs' => 'Pallet Reutilizable'],

        ['pedido' => 'pedido_8',  'producto' => 'queso_oaxaca',        'cantidad' => 120, 'obs' => 'Queso Oaxaca 1kg'],
        ['pedido' => 'pedido_8',  'producto' => 'queso_panela',        'cantidad' => 80,  'obs' => 'Queso Panela 500g'],

        ['pedido' => 'pedido_9',  'producto' => 'caja_empaque',        'cantidad' => 60,  'obs' => 'Caja de Empaque Vacía'],

        ['pedido' => 'pedido_10', 'producto' => 'caja_empaque',        'cantidad' => 40,  'obs' => 'Material retornable'],
    ];

    foreach ($detalles_a_insertar as $indice => $d) {
        if (!isset($ids_pedidos[$d['pedido']])) {
            throw new RuntimeException("Detalle #{$indice}: el pedido '{$d['pedido']}' no existe en \$ids_pedidos.");
        }
        if (!isset($ids_productos[$d['producto']])) {
            throw new RuntimeException("Detalle #{$indice}: el producto '{$d['producto']}' no existe en \$ids_productos.");
        }

        $stmt_detalles->execute([
            ':pedido'                 => $ids_pedidos[$d['pedido']],
            ':identificador_producto' => $ids_productos[$d['producto']],
            ':cantidad_producto'      => $d['cantidad'],
            ':observaciones'          => $d['obs'],
        ]);
    }

    echo "✅ Se insertaron " . count($detalles_a_insertar) . " detalles de pedidos.\n";

    $pdo->commit();
    echo "✅ Datos insertados correctamente.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Throwable (en vez de Exception) también captura Error/TypeError,
    // por ejemplo si una clave de array no existe o hay un error de tipo,
    // evitando que la transacción quede abierta sin rollback.
    fwrite(STDERR, "❌ Error: " . $e->getMessage() . "\n");
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}