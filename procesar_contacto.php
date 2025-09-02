<?php
require_once 'config.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJSON(false, 'Método no permitido');
}

// Obtener y sanitizar los datos del formulario
$nombre = sanitizar($_POST['nombre'] ?? '');
$email = sanitizar($_POST['email'] ?? '');
$telefono = sanitizar($_POST['telefono'] ?? '');
$asunto = sanitizar($_POST['asunto'] ?? '');
$mensaje = sanitizar($_POST['mensaje'] ?? '');
$newsletter = isset($_POST['newsletter']) ? 1 : 0;

// Validaciones
$errores = [];

if (empty($nombre)) {
    $errores[] = 'El nombre es requerido';
}

if (empty($email)) {
    $errores[] = 'El email es requerido';
} elseif (!validarEmail($email)) {
    $errores[] = 'El email no es válido';
}

if (empty($asunto)) {
    $errores[] = 'El asunto es requerido';
}

if (empty($mensaje)) {
    $errores[] = 'El mensaje es requerido';
}

// Si hay errores, devolver respuesta de error
if (!empty($errores)) {
    respuestaJSON(false, 'Errores de validación: ' . implode(', ', $errores));
}

try {
    // Conectar a la base de datos
    $pdo = conectarDB();
    
    // Preparar la consulta SQL
    $sql = "INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, newsletter) 
            VALUES (:nombre, :email, :telefono, :asunto, :mensaje, :newsletter)";
    
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta
    $resultado = $stmt->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':telefono' => $telefono,
        ':asunto' => $asunto,
        ':mensaje' => $mensaje,
        ':newsletter' => $newsletter
    ]);
    
    if ($resultado) {
        $contacto_id = $pdo->lastInsertId();
        
        // Registrar estadística
        registrarEstadistica('contacto', 'formulario_enviado', "ID: $contacto_id, Asunto: $asunto");
        
        // Preparar respuesta de éxito
        $respuesta_data = [
            'id' => $contacto_id,
            'nombre' => $nombre,
            'email' => $email,
            'asunto' => $asunto
        ];
        
        respuestaJSON(true, '¡Gracias por contactarnos! Hemos recibido tu mensaje y te responderemos pronto.', $respuesta_data);
    } else {
        respuestaJSON(false, 'Error al guardar el mensaje. Por favor, inténtalo de nuevo.');
    }
    
} catch (PDOException $e) {
    error_log("Error en procesar_contacto.php: " . $e->getMessage());
    respuestaJSON(false, 'Error interno del servidor. Por favor, inténtalo más tarde.');
}

// Función para registrar estadísticas
function registrarEstadistica($pagina, $accion, $detalles = null) {
    try {
        $pdo = conectarDB();
        $sql = "INSERT INTO estadisticas_web (pagina, accion, detalles, usuario_ip, user_agent) 
                VALUES (:pagina, :accion, :detalles, :usuario_ip, :user_agent)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pagina' => $pagina,
            ':accion' => $accion,
            ':detalles' => $detalles,
            ':usuario_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        error_log("Error al registrar estadística: " . $e->getMessage());
    }
}
?>
