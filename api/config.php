<?php
// Configuración de base de datos
$servername = "localhost";
$username = "segeneroso";
$password = "UR*hDYK1y$";
$dbname = "segeneroso";

// Crear conexión
function getConnection()
{
    global $servername, $username, $password, $dbname;

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
    return $conn;
}

// Función para validar email
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para limpiar datos
function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}