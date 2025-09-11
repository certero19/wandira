<?php
// Configuración de la base de datos para BeTravel
define('DB_HOST', 'localhost');
define('DB_NAME', 'betravel_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('SITE_NAME', 'BeTravel');
define('SITE_URL', 'http://localhost/betravel');

// Configuración de email (para notificaciones)
define('ADMIN_EMAIL', 'admin@betravel.com');
define('FROM_EMAIL', 'noreply@betravel.com');

// Función para conectar a la base de datos
function conectarDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        die("Error de conexión a la base de datos. Por favor, inténtelo más tarde.");
    }
}

// Función para sanitizar datos de entrada
function sanitizar($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para generar respuesta JSON
function respuestaJSON($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');
?>
