<?php
require_once __DIR__ . "/libs/Control.php";
require_once __DIR__ . "/libs/MySQLdb.php";

if (session_status() !== PHP_SESSION_ACTIVE && !session_start()) {
    http_response_code(500);
    echo "No se pudo iniciar la sesión. Verifica la configuración de PHP.";
    exit;
}

try {
    $db = new MySQLdb();
    new Control($db);
} catch (PDOException $e) {
    error_log("Error en una operación de base de datos: " . $e->getMessage());
    http_response_code(500);
    echo "No se pudo completar la operación de base de datos. Verifica que MySQL esté encendido y que la base biblioteca esté creada.";
    exit;
}
?>