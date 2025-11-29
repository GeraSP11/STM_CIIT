<?php

$host = 'postgres';
$dbname = 'TMS-CIIT';
$user = 'admin';
$pass = 'password';

$max_retries = 10;
$attempt = 0;

while ($attempt < $max_retries) {
    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        break; // ÉXITO
    } catch (PDOException $e) {
        $attempt++;
        if ($attempt == $max_retries) {
            die("❌ Error de conexión a PostgreSQL: " . $e->getMessage());
        }
        sleep(2); // espera y reintenta
    }
}

?>
