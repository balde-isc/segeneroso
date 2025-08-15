<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
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

        // Intentar conexión
        $conn = getConnection();

        // Verificar si las tablas existen
        $table_check = $conn->query("SHOW TABLES LIKE 'suscriptores'");
        if ($table_check->num_rows == 0) {
            // Crear tabla si no existe
            $create_table = "CREATE TABLE suscriptores (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                interes VARCHAR(50),
                fecha_suscripcion DATETIME NOT NULL,
                UNIQUE KEY unique_email (email)
            )";

            if (!$conn->query($create_table)) {
                throw new Exception("Error creando tabla: " . $conn->error);
            }
        }

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
            throw new Exception("Error ejecutando consulta: " . $stmt->error);
        }

        $stmt->close();
        $check_stmt->close();
        $conn->close();
    } catch (Exception $e) {
        // Mostrar el error real (solo para debugging)
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}