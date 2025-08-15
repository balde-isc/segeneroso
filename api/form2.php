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
    $interes = cleanInput($_POST['interes'] ?? '');
    $acepta_terminos = isset($_POST['acepta_terminos']) ? 1 : 0;

    // Validaciones
    if (empty($nombre) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Nombre y email son obligatorios']);
        exit;
    }

    if (!validateEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'Email no válido']);
        exit;
    }

    if (!$acepta_terminos) {
        echo json_encode(['success' => false, 'message' => 'Debes aceptar los términos y condiciones']);
        exit;
    }

    try {
        $conn = getConnection();

        // Verificar si ya existe el email
        $check_stmt = $conn->prepare("SELECT id FROM suscriptores WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Este email ya está suscrito']);
            exit;
        }

        // Insertar nueva suscripción
        $stmt = $conn->prepare("INSERT INTO suscriptores (nombre, email, interes, fecha_suscripcion) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $nombre, $email, $interes);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => '¡Suscripción exitosa! Recibirás nuestro newsletter.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al procesar la suscripción']);
        }

        $stmt->close();
        $check_stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}