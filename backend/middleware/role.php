<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireRole($rolesPermitidos = []) {

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /index.php");
        exit;
    }

    $rolUsuario = $_SESSION['cargo'] ?? null;

    if (!in_array($rolUsuario, $rolesPermitidos)) {
        header("Location: /includes/acceso-denegado.php");
        exit;
    }
}
?>
