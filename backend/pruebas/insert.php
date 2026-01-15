<?php

require_once __DIR__ . '/../config/conexion.php';

try {
    $pdo->beginTransaction();

    /* ============================================================
        1️⃣ INSERTAR LOCALIDADES (con clave_centro_trabajo)
    ============================================================ */
    $sql_localidades = "INSERT INTO localidades 
        (nombre_centro_trabajo, clave_centro_trabajo, ubicacion_georeferenciada, poblacion, localidad, estado, tipo_instalacion)
        VALUES (:nombre_centro_trabajo, :clave_centro_trabajo, :ubicacion_georeferenciada, :poblacion, :localidad, :estado, :tipo_instalacion)
        RETURNING id_localidad";

    $stmt_localidades = $pdo->prepare($sql_localidades);

    $localidades = [
        ['Centro Productivo Oaxaca', 'CPOAX01', '16.8531,-96.7712', 'Oaxaca de Juárez', 'Oaxaca', 'Oaxaca', 'Centro Productivo'],
        ['Centro de Distribución Istmo', 'CDIST01', '16.3236,-95.2406', 'Salina Cruz', 'Salina Cruz', 'Oaxaca', 'Centro de Distribucion'],
        ['PODEBI Huatulco', 'POHUA01', '15.7686,-96.1356', 'Santa María Huatulco', 'Huatulco', 'Oaxaca', 'PODEBI'],
        ['Almacén Central Puebla', 'ALPUE01', '19.0433,-98.2019', 'Puebla', 'Puebla', 'Puebla', 'Almacen']
    ];

    $ids_localidades = [];

    foreach ($localidades as $loc) {
        $stmt_localidades->execute([
            ':nombre_centro_trabajo'    => $loc[0],
            ':clave_centro_trabajo'     => $loc[1],
            ':ubicacion_georeferenciada' => $loc[2],
            ':poblacion'                => $loc[3],
            ':localidad'                => $loc[4],
            ':estado'                   => $loc[5],
            ':tipo_instalacion'         => $loc[6]
        ]);
        $ids_localidades[] = $stmt_localidades->fetchColumn();
    }

    /* ============================================================
        2️⃣ INSERTAR PERSONAL (agregando numero_empleado)
    ============================================================ */
    $sql_personal = "INSERT INTO personal 
        (nombre_personal, apellido_paterno, apellido_materno, numero_empleado, afiliacion_laboral, cargo, curp)
        VALUES (:nombre_personal, :apellido_paterno, :apellido_materno, :numero_empleado, :afiliacion_laboral, :cargo, :curp)
        RETURNING id_personal";

    $stmt_personal = $pdo->prepare($sql_personal);

    $personal = [
        ['María', 'González', 'Pérez', 1001, $ids_localidades[0], 'Autoridad', 'GOPM800101MOCNRR02'],
        ['Juan', 'López', 'Martínez', 1002, $ids_localidades[1], 'Administrador del TMS', 'LOMJ850210HOCRRN03'],
        ['Ana', 'Ramírez', 'Castillo', 1003, $ids_localidades[2], 'Operador Logístico', 'RACA900415MOCSTS04'],
        ['Carlos', 'Hernández', 'Ortiz', 1004, $ids_localidades[3], 'Jefe de Almacén', 'HEOC820701HOCNRS05'],
        ['Fernanda', 'Vargas', 'Luna', 1005, $ids_localidades[1], 'Cliente', 'VALF950812MOCNRN06']
    ];

    $ids_personal = [];

    foreach ($personal as $p) {
        $stmt_personal->execute([
            ':nombre_personal'    => $p[0],
            ':apellido_paterno'   => $p[1],
            ':apellido_materno'   => $p[2],
            ':numero_empleado'    => $p[3],
            ':afiliacion_laboral' => $p[4],
            ':cargo'              => $p[5],
            ':curp'               => $p[6]
        ]);
        $ids_personal[] = $stmt_personal->fetchColumn();
    }

    /* ============================================================
        3️⃣ INSERTAR USUARIOS
    ============================================================ */
    /*
    $sql_usuarios = "INSERT INTO usuarios
        (nombre_usuario, contrasena, correo_electronico, identificador_de_rh)
        VALUES (:nombre_usuario, :contrasena, :correo_electronico, :identificador_de_rh)";

    $stmt_usuarios = $pdo->prepare($sql_usuarios);

    $usuarios = [
        ['maria_gonzalez', 'admin123', 'maria.gonzalez@empresa.com', $ids_personal[0]],
        ['juan_lopez', 'tms2024', 'juan.lopez@empresa.com', $ids_personal[1]],
        ['ana_ramirez', 'logistica45', 'ana.ramirez@empresa.com', $ids_personal[2]],
        ['carlos_hernandez', 'almacen99', 'carlos.hernandez@empresa.com', $ids_personal[3]]
    ];

    foreach ($usuarios as $u) {
        $stmt_usuarios->execute([
            ':nombre_usuario' => $u[0],
            ':contrasena'     => $u[1],
            ':correo_electronico' => $u[2],
            ':identificador_de_rh' => $u[3]
        ]);
    }
        */

    /* ============================================================
        4️⃣ INSERTAR PRODUCTOS (adaptados a tu nueva estructura)
    ============================================================ */
    $sql_productos = "INSERT INTO productos
    (nombre_producto, ubicacion_producto, peso, altura, largo, ancho,
     cajas_por_cama, camas_por_pallet, peso_soportado, peso_volumetrico,
     unidades_existencia, tipo_de_embalaje, tipo_de_mercancia, observaciones)
    VALUES (:nombre_producto, :ubicacion_producto, :peso, :altura, :largo, :ancho,
     :cajas_por_cama, :camas_por_pallet, :peso_soportado, :peso_volumetrico,
     :unidades_existencia, :tipo_de_embalaje, :tipo_de_mercancia, :observaciones)
     RETURNING id_producto";


    $stmt_productos = $pdo->prepare($sql_productos);

    $productos = [
        // Centro Productivo Oaxaca
        ['Queso Oaxaca 1kg',  $ids_localidades[0], 1.0, 0.12, 0.20, 0.20, 12, 10, 500.0, 1.2, 240, 'Bolsa plástica', 'Alimentos procesados', 'Queso fresco'],
        ['Queso Panela 500g', $ids_localidades[0], 0.5, 0.08, 0.15, 0.15, 20, 8, 320.0, 0.8, 500, 'Bolsa plástica', 'Alimentos procesados', 'Panela empacada'],

        // Centro de Distribución Istmo
        ['Yogurt Natural 1L', $ids_localidades[1], 1.1, 0.22, 0.10, 0.10, 15, 9, 600.0, 1.3, 300, 'Envase plástico', 'Perecederas', 'Refrigeración necesaria'],
        ['Yogurt Frutas 500ml', $ids_localidades[1], 0.55, 0.18, 0.09, 0.09, 24, 10, 400.0, 0.9, 450, 'Envase plástico', 'Perecederas', 'Mantener cadena de frío'],

        // PODEBI Huatulco
        ['Caja de Empaque Vacía', $ids_localidades[2], 0.3, 0.25, 0.40, 0.40, 40, 12, 200.0, 0.6, 120, 'Caja de cartón corrugado', 'Industriales', 'Usada para embalaje'],

        // Almacén Puebla
        ['Pallet Reutilizable', $ids_localidades[3], 15.0, 0.15, 1.20, 1.00, 1, 1, 1500.0, 2.0, 50, 'Pallet de madera', 'Industriales', 'Pallet estándar'],
        ['Film Stretch',        $ids_localidades[3], 2.0, 0.10, 0.10, 0.10, 25, 6, 300.0, 0.5, 200, 'Emplaye / stretch film', 'Industriales', 'Usado para envolver cargas']
    ];

    /* 4.1 — preparar INSERT para productos_localidades */
    $sql_prod_loc = "INSERT INTO productos_localidades (id_producto, id_localidad)
                 VALUES (:id_producto, :id_localidad)";

    $stmt_prod_loc = $pdo->prepare($sql_prod_loc);

    foreach ($productos as $prod) {
        // Insertar producto
        $stmt_productos->execute([
            ':nombre_producto'    => $prod[0],
            ':ubicacion_producto' => $prod[1],
            ':peso'               => $prod[2],
            ':altura'             => $prod[3],
            ':largo'              => $prod[4],
            ':ancho'              => $prod[5],
            ':cajas_por_cama'     => $prod[6],
            ':camas_por_pallet'   => $prod[7],
            ':peso_soportado'     => $prod[8],
            ':peso_volumetrico'   => $prod[9],
            ':unidades_existencia' => $prod[10],
            ':tipo_de_embalaje'   => $prod[11],
            ':tipo_de_mercancia'  => $prod[12],
            ':observaciones'      => $prod[13]
        ]);

        // Obtener id_producto generado
        $id_producto = $stmt_productos->fetchColumn();

        /* ============================================================
        4.2 INSERTAR EN productos_localidades
        usa el mismo id_localidad del producto
    ============================================================ */
        $stmt_prod_loc->execute([
            ':id_producto' => $id_producto,
            ':id_localidad' => $prod[1]
        ]);
    }


    /* ============================================================
        5️⃣ INSERTAR PEDIDOS
    ============================================================ */
    $sql_pedidos = "INSERT INTO pedidos
        (clave_pedido, localidad_origen, localidad_destino, estatus_pedido, 
         fecha_solicitud, fecha_entrega, observaciones)
        VALUES (:clave_pedido, :localidad_origen, :localidad_destino, :estatus_pedido,
         :fecha_solicitud, :fecha_entrega, :observaciones)
        RETURNING id_pedido";

    $stmt_pedidos = $pdo->prepare($sql_pedidos);

    $pedidos = [
        // Pedido 1: Centro Productivo Oaxaca → Centro de Distribución Istmo (Entregado)
        [
            'PED-2024-001', 
            $ids_localidades[0], // Oaxaca
            $ids_localidades[1], // Istmo
            'Entregado',
            '2024-01-15',
            '2024-01-20',
            'Envío de quesos frescos - Entrega exitosa'
        ],
        
        // Pedido 2: Centro de Distribución Istmo → PODEBI Huatulco (En tránsito)
        [
            'PED-2024-002',
            $ids_localidades[1], // Istmo
            $ids_localidades[2], // Huatulco
            'En tránsito',
            '2024-01-22',
            '2024-01-25',
            'Yogurt y productos lácteos - En camino'
        ],
        
        // Pedido 3: Almacén Central Puebla → Centro Productivo Oaxaca (En preparación)
        [
            'PED-2024-003',
            $ids_localidades[3], // Puebla
            $ids_localidades[0], // Oaxaca
            'En preparación',
            '2024-01-23',
            '2024-01-28',
            'Material de embalaje y pallets'
        ],
        
        // Pedido 4: Centro Productivo Oaxaca → Almacén Central Puebla (Enviado)
        [
            'PED-2024-004',
            $ids_localidades[0], // Oaxaca
            $ids_localidades[3], // Puebla
            'Enviado',
            '2024-01-20',
            '2024-01-26',
            'Productos terminados para redistribución'
        ],
        
        // Pedido 5: PODEBI Huatulco → Centro de Distribución Istmo (En captura)
        [
            'PED-2024-005',
            $ids_localidades[2], // Huatulco
            $ids_localidades[1], // Istmo
            'En captura',
            '2024-01-24',
            null, // Sin fecha de entrega aún
            'Pedido en proceso de captura'
        ],
        
        // Pedido 6: Centro de Distribución Istmo → Almacén Central Puebla (En recolección)
        [
            'PED-2024-006',
            $ids_localidades[1], // Istmo
            $ids_localidades[3], // Puebla
            'En recolección',
            '2024-01-23',
            '2024-01-30',
            'Productos perecederos - Urgente'
        ],
        
        // Pedido 7: Almacén Central Puebla → PODEBI Huatulco (En reparto)
        [
            'PED-2024-007',
            $ids_localidades[3], // Puebla
            $ids_localidades[2], // Huatulco
            'En reparto',
            '2024-01-21',
            '2024-01-24',
            'Último tramo de entrega'
        ],
        
        // Pedido 8: Centro Productivo Oaxaca → Centro de Distribución Istmo (Entregado)
        [
            'PED-2024-008',
            $ids_localidades[0], // Oaxaca
            $ids_localidades[1], // Istmo
            'Entregado',
            '2024-01-10',
            '2024-01-15',
            'Pedido anterior completado'
        ],
        
        // Pedido 9: PODEBI Huatulco → Almacén Central Puebla (En tránsito)
        [
            'PED-2024-009',
            $ids_localidades[2], // Huatulco
            $ids_localidades[3], // Puebla
            'En tránsito',
            '2024-01-22',
            '2024-01-27',
            'Traslado de materiales'
        ],
        
        // Pedido 10: Centro de Distribución Istmo → Centro Productivo Oaxaca (Enviado)
        [
            'PED-2024-010',
            $ids_localidades[1], // Istmo
            $ids_localidades[0], // Oaxaca
            'Enviado',
            '2024-01-23',
            '2024-01-26',
            'Devolución de envases vacíos'
        ]
    ];

    $ids_pedidos = [];

    foreach ($pedidos as $ped) {
        $stmt_pedidos->execute([
            ':clave_pedido'      => $ped[0],
            ':localidad_origen'  => $ped[1],
            ':localidad_destino' => $ped[2],
            ':estatus_pedido'    => $ped[3],
            ':fecha_solicitud'   => $ped[4],
            ':fecha_entrega'     => $ped[5],
            ':observaciones'     => $ped[6]
        ]);
        $ids_pedidos[] = $stmt_pedidos->fetchColumn();
    }

    echo "\n✅ Se insertaron " . count($ids_pedidos) . " pedidos correctamente.\n";

    // ============================================
    // 6️⃣ INSERTAR DETALLES DE LOS PEDIDOS
    // ============================================

    $sql_detalles = "INSERT INTO pedidos_detalles
        (pedido, identificador_producto, cantidad_producto, observaciones)
        VALUES (:pedido, :identificador_producto, :cantidad_producto, :observaciones)";
    $stmt_detalles = $pdo->prepare($sql_detalles);

    // Define qué productos y cantidades van a cada pedido
    $detalles_a_insertar = [
        ['pedido' => $ids_pedidos[0], 'producto' => 1, 'cantidad' => 10, 'obs' => 'Queso Oaxaca 1kg'],
        ['pedido' => $ids_pedidos[0], 'producto' => 2, 'cantidad' => 5,  'obs' => 'Queso Panela 500g'],

        ['pedido' => $ids_pedidos[1], 'producto' => 3, 'cantidad' => 20, 'obs' => 'Yogurt Natural 1L'],
        ['pedido' => $ids_pedidos[1], 'producto' => 4, 'cantidad' => 15, 'obs' => 'Yogurt Frutas 500ml'],

        ['pedido' => $ids_pedidos[5], 'producto' => 3, 'cantidad' => 50, 'obs' => 'Yogurt Natural 1L'],
        ['pedido' => $ids_pedidos[5], 'producto' => 4, 'cantidad' => 30, 'obs' => 'Yogurt Frutas 500ml'],
    ];

    // Ejecutar inserciones
    foreach ($detalles_a_insertar as $d) {
        $stmt_detalles->execute([
            ':pedido' => $d['pedido'],
            ':identificador_producto' => $d['producto'],
            ':cantidad_producto' => $d['cantidad'],
            ':observaciones' => $d['obs']
        ]);
    }

echo "✅ Se insertaron los detalles de los pedidos.\n";


    $pdo->commit();
    echo "✅ Datos insertados correctamente.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage();
}
