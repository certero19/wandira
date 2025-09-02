<?php
require_once 'config.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJSON(false, 'Método no permitido');
}

// Obtener la acción solicitada
$accion = sanitizar($_POST['accion'] ?? '');

try {
    $pdo = conectarDB();
    
    switch ($accion) {
        case 'crear_reserva':
            // Obtener y sanitizar los datos del formulario
            $nombre_cliente = sanitizar($_POST['nombre_cliente'] ?? '');
            $email_cliente = sanitizar($_POST['email_cliente'] ?? '');
            $telefono_cliente = sanitizar($_POST['telefono_cliente'] ?? '');
            $paquete = sanitizar($_POST['paquete'] ?? '');
            $precio = sanitizar($_POST['precio'] ?? '');
            $fecha_viaje = sanitizar($_POST['fecha_viaje'] ?? '');
            $numero_personas = intval($_POST['numero_personas'] ?? 1);
            $comentarios = sanitizar($_POST['comentarios'] ?? '');
            
            // Validaciones
            $errores = [];
            
            if (empty($nombre_cliente)) {
                $errores[] = 'El nombre es requerido';
            }
            
            if (empty($email_cliente)) {
                $errores[] = 'El email es requerido';
            } elseif (!validarEmail($email_cliente)) {
                $errores[] = 'El email no es válido';
            }
            
            if (empty($paquete)) {
                $errores[] = 'El paquete es requerido';
            }
            
            if (empty($precio)) {
                $errores[] = 'El precio es requerido';
            }
            
            if ($numero_personas < 1 || $numero_personas > 20) {
                $errores[] = 'El número de personas debe estar entre 1 y 20';
            }
            
            // Si hay errores, devolver respuesta de error
            if (!empty($errores)) {
                respuestaJSON(false, 'Errores de validación: ' . implode(', ', $errores));
            }
            
            // Preparar la consulta SQL
            $sql = "INSERT INTO reservas_paquetes (nombre_cliente, email_cliente, telefono_cliente, paquete, precio, fecha_viaje, numero_personas, comentarios) 
                    VALUES (:nombre_cliente, :email_cliente, :telefono_cliente, :paquete, :precio, :fecha_viaje, :numero_personas, :comentarios)";
            
            $stmt = $pdo->prepare($sql);
            
            // Ejecutar la consulta
            $resultado = $stmt->execute([
                ':nombre_cliente' => $nombre_cliente,
                ':email_cliente' => $email_cliente,
                ':telefono_cliente' => $telefono_cliente,
                ':paquete' => $paquete,
                ':precio' => $precio,
                ':fecha_viaje' => $fecha_viaje ?: null,
                ':numero_personas' => $numero_personas,
                ':comentarios' => $comentarios
            ]);
            
            if ($resultado) {
                $reserva_id = $pdo->lastInsertId();
                
                // Registrar estadística
                registrarEstadistica('reservas', 'nueva_reserva', "ID: $reserva_id, Paquete: $paquete");
                
                // Preparar respuesta de éxito
                $respuesta_data = [
                    'id' => $reserva_id,
                    'nombre_cliente' => $nombre_cliente,
                    'paquete' => $paquete,
                    'numero_personas' => $numero_personas
                ];
                
                respuestaJSON(true, '¡Reserva creada exitosamente! Te contactaremos pronto para confirmar los detalles.', $respuesta_data);
            } else {
                respuestaJSON(false, 'Error al crear la reserva. Por favor, inténtalo de nuevo.');
            }
            break;
            
        case 'consultar_reserva':
            $email = sanitizar($_POST['email'] ?? '');
            
            if (empty($email)) {
                respuestaJSON(false, 'El email es requerido');
            }
            
            if (!validarEmail($email)) {
                respuestaJSON(false, 'El email no es válido');
            }
            
            $sql = "SELECT id, paquete, precio, fecha_viaje, numero_personas, 
                           DATE_FORMAT(fecha_reserva, '%d/%m/%Y %H:%i') as fecha_reserva_formateada,
                           estado
                    FROM reservas_paquetes 
                    WHERE email_cliente = :email 
                    ORDER BY fecha_reserva DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $reservas = $stmt->fetchAll();
            
            if ($reservas) {
                respuestaJSON(true, 'Reservas encontradas', $reservas);
            } else {
                respuestaJSON(false, 'No se encontraron reservas para este email');
            }
            break;
            
        case 'estadisticas_paquetes':
            $sql = "SELECT paquete, COUNT(*) as total_reservas, 
                           COUNT(DISTINCT email_cliente) as clientes_unicos,
                           AVG(numero_personas) as promedio_personas
                    FROM reservas_paquetes 
                    GROUP BY paquete 
                    ORDER BY total_reservas DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $estadisticas = $stmt->fetchAll();
            
            respuestaJSON(true, 'Estadísticas obtenidas', $estadisticas);
            break;
            
        default:
            respuestaJSON(false, 'Acción no válida');
    }
    
} catch (PDOException $e) {
    error_log("Error en reservas.php: " . $e->getMessage());
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
