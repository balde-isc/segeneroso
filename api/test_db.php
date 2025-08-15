<?php
require_once 'config.php';

try {
    $conn = getConnection();
    echo "Conexión exitosa a la base de datos!";
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}