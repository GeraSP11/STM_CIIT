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

    $pdo->commit();
    echo "✅ Datos insertados correctamente.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage();
}
