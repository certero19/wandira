<?php
require_once 'config.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJSON(false, 'Método no permitido');
}

// Obtener la acción solicitada
$accion = sanitizar($_POST['accion'] ?? '');
$destino = sanitizar($_POST['destino'] ?? '');

// Obtener IP del usuario
$usuario_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

try {
    $pdo = conectarDB();
    
    switch ($accion) {
        case 'agregar':
            if (empty($destino)) {
                respuestaJSON(false, 'El destino es requerido');
            }
            
            // Verificar si ya existe
            $sql_check = "SELECT id FROM destinos_favoritos WHERE usuario_ip = :usuario_ip AND destino = :destino";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([':usuario_ip' => $usuario_ip, ':destino' => $destino]);
            
            if ($stmt_check->fetch()) {
                respuestaJSON(false, 'Este destino ya está en tus favoritos');
            }
            
            // Agregar a favoritos
            $sql = "INSERT INTO destinos_favoritos (usuario_ip, destino) VALUES (:usuario_ip, :destino)";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([':usuario_ip' => $usuario_ip, ':destino' => $destino]);
            
            if ($resultado) {
                registrarEstadistica('destinos', 'agregar_favorito', $destino);
                respuestaJSON(true, "¡$destino agregado a tus favoritos!");
            } else {
                respuestaJSON(false, 'Error al agregar a favoritos');
            }
            break;
            
        case 'eliminar':
            if (empty($destino)) {
                respuestaJSON(false, 'El destino es requerido');
            }
            
            $sql = "DELETE FROM destinos_favoritos WHERE usuario_ip = :usuario_ip AND destino = :destino";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([':usuario_ip' => $usuario_ip, ':destino' => $destino]);
            
            if ($resultado) {
                registrarEstadistica('destinos', 'eliminar_favorito', $destino);
                respuestaJSON(true, "$destino eliminado de tus favoritos");
            } else {
                respuestaJSON(false, 'Error al eliminar de favoritos');
            }
            break;
            
        case 'listar':
            $sql = "SELECT destino, DATE_FORMAT(fecha_agregado, '%d/%m/%Y') as fecha_formateada 
                    FROM destinos_favoritos 
                    WHERE usuario_ip = :usuario_ip 
                    ORDER BY fecha_agregado DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario_ip' => $usuario_ip]);
            $favoritos = $stmt->fetchAll();
            
            respuestaJSON(true, 'Favoritos obtenidos correctamente', $favoritos);
            break;
            
        case 'verificar':
            if (empty($destino)) {
                respuestaJSON(false, 'El destino es requerido');
            }
            
            $sql = "SELECT id FROM destinos_favoritos WHERE usuario_ip = :usuario_ip AND destino = :destino";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario_ip' => $usuario_ip, ':destino' => $destino]);
            $existe = $stmt->fetch() ? true : false;
            
            respuestaJSON(true, 'Verificación completada', ['es_favorito' => $existe]);
            break;
            
        default:
            respuestaJSON(false, 'Acción no válida');
    }
    
} catch (PDOException $e) {
    error_log("Error en favoritos.php: " . $e->getMessage());
    respuestaJSON(false, 'Error interno del servidor');
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
