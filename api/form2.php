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
        $telefono = cleanInput($_POST['telefono'] ?? '');
        $categoria = cleanInput($_POST['categoria'] ?? '');
        $comentarios = cleanInput($_POST['comentarios'] ?? '');
        $acepta_terminos = cleanInput($_POST['acepta_terminos'] ?? '');

        // Validaciones
        if (empty($nombre) || empty($email) || empty($telefono)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar completos']);
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
        $table_check = $conn->query("SHOW TABLES LIKE 'apoyos'");
        if ($table_check->num_rows == 0) {
            // Crear tabla si no existe
            $create_table = "CREATE TABLE apoyos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                telefono VARCHAR(20),
                categoria VARCHAR(100) NOT NULL,
                comentarios TEXT NOT NULL,
                fecha_envio DATETIME NOT NULL
            )";

            if (!$conn->query($create_table)) {
                throw new Exception("Error creando tabla: " . $conn->error);
            }
        }

        // Preparar consulta
        $stmt = $conn->prepare("INSERT INTO apoyos (nombre, email, telefono, categoria, comentarios, fecha_envio) VALUES (?, ?, ?, ?, ?, NOW())");

        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $conn->error);
        }

        $stmt->bind_param("ssss", $nombre, $email, $telefono, $categoria, $comentarios);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => '¡Mensaje enviado correctamente! Te contactaremos pronto.']);
        } else {
            throw new Exception("Error ejecutando consulta: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        // Mostrar el error real (solo para debugging)
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}