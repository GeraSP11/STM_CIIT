<?php
require_once __DIR__.'/../config/conexion.php';

try {

    /* ============================
       LOCALIDADES
    ============================ */
    echo "<h2>Localidades</h2>";
    $stmt = $pdo->query("SELECT * FROM localidades ORDER BY id_localidad");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $loc) {
        echo "ID: {$loc['id_localidad']} | Centro: {$loc['nombre_centro_trabajo']} | Tipo: {$loc['tipo_instalacion']}<br>";
    }


    /* ============================
       PERSONAL
    ============================ */
    echo "<h2>Personal</h2>";
    $stmt = $pdo->query("SELECT * FROM personal ORDER BY id_personal");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
        echo "ID: {$p['id_personal']} | Nombre: {$p['nombre_personal']} {$p['apellido_paterno']} {$p['apellido_materno']} | Cargo: {$p['cargo']}<br>";
        echo "CURP: {$p['curp']}<br><br>";
    }


    /* ============================
       USUARIOS
    ============================ */
    echo "<h2>Usuarios</h2>";
    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id_usuario");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $u) {
        echo "ID: {$u['id_usuario']} | Usuario: {$u['nombre_usuario']} | Correo: {$u['correo_electronico']} | RH: {$u['identificador_de_rh']}<br>";
        echo "Password: {$u['contrasena']}<br><br>";
    }


    /* ============================
       PRODUCTOS
    ============================ */
    echo "<h2>Productos</h2>";
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id_producto");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $prod) {
        echo "<strong>ID:</strong> {$prod['id_producto']} | ";
        echo "<strong>Nombre:</strong> {$prod['nombre_producto']} | ";
        echo "<strong>Peso:</strong> {$prod['peso']} kg | ";
        echo "<strong>Ubicación:</strong> {$prod['ubicacion_producto']} | ";
        echo "<strong>Embalaje:</strong> {$prod['tipo_de_embalaje']}<br><br>";
    }


    /* ============================
       PRODUCTOS ↔ LOCALIDADES
    ============================ */
    echo "<h2>Productos por Localidad</h2>";

    $sql = "
        SELECT pl.id_pl, 
               p.nombre_producto,
               l.nombre_centro_trabajo AS localidad
        FROM productos_localidades pl
        INNER JOIN productos p ON p.id_producto = pl.id_producto
        INNER JOIN localidades l ON l.id_localidad = pl.id_localidad
        ORDER BY pl.id_pl;
    ";

    $stmt = $pdo->query($sql);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo "No hay productos asignados a localidades.<br>";
    } else {
        foreach ($rows as $row) {
            echo "ID PL: {$row['id_pl']} | Producto: {$row['nombre_producto']} | Localidad: {$row['localidad']}<br>";
        }
    }


} catch (PDOException $e) {
    echo "Error al leer registros: " . $e->getMessage();
}
?>
