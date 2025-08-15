<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener y limpiar datos
    $nombre = cleanInput($_POST['nombre'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $telefono = cleanInput($_POST['telefono'] ?? '');
    $mensaje = cleanInput($_POST['mensaje'] ?? '');

    // Validaciones
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar completos']);
        exit;
    }

    if (!validateEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'Email no válido']);
        exit;
    }

    try {
        $conn = getConnection();

        // Preparar consulta
        $stmt = $conn->prepare("INSERT INTO contactos (nombre, email, telefono, mensaje, fecha_envio) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => '¡Mensaje enviado correctamente! Te contactaremos pronto.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el mensaje']);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}