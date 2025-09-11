<?php
// Gestión de reservas en el admin
try {
    // Obtener todas las reservas
    $sql = "SELECT id, nombre_cliente, email_cliente, telefono_cliente, paquete, precio, 
                   fecha_viaje, numero_personas, comentarios,
                   DATE_FORMAT(fecha_reserva, '%d/%m/%Y %H:%i') as fecha_reserva_formateada,
                   DATE_FORMAT(fecha_viaje, '%d/%m/%Y') as fecha_viaje_formateada,
                   estado
            FROM reservas_paquetes 
            ORDER BY fecha_reserva DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $reservas = $stmt->fetchAll();
    
    // Estadísticas de reservas
    $sql_stats = "SELECT 
        COUNT(*) as total_reservas,
        COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes,
        COUNT(CASE WHEN estado = 'confirmada' THEN 1 END) as confirmadas,
        COUNT(CASE WHEN estado = 'cancelada' THEN 1 END) as canceladas,
        SUM(numero_personas) as total_personas,
        COUNT(DISTINCT email_cliente) as clientes_unicos
        FROM reservas_paquetes";
    
    $stmt = $pdo->prepare($sql_stats);
    $stmt->execute();
    $stats = $stmt->fetch();
    
    // Paquetes más populares
    $sql_populares = "SELECT paquete, COUNT(*) as total_reservas, 
                             SUM(numero_personas) as total_personas,
                             ROUND(AVG(numero_personas), 1) as promedio_personas
                      FROM reservas_paquetes 
                      GROUP BY paquete 
                      ORDER BY total_reservas DESC";
    
    $stmt = $pdo->prepare($sql_populares);
    $stmt->execute();
    $paquetes_populares = $stmt->fetchAll();
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar reservas: ' . $e->getMessage() . '</div>';
    return;
}
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-suitcase"></i> Gestión de Reservas</h2>
        <p class="text-muted">Administra todas las reservas de paquetes turísticos</p>
    </div>
</div>

<!-- Estadísticas de Reservas -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary"><?= $stats['total_reservas'] ?></h4>
                <small>Total Reservas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning"><?= $stats['pendientes'] ?></h4>
                <small>Pendientes</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?= $stats['confirmadas'] ?></h4>
                <small>Confirmadas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-danger"><?= $stats['canceladas'] ?></h4>
                <small>Canceladas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info"><?= $stats['total_personas'] ?></h4>
                <small>Total Personas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-secondary"><?= $stats['clientes_unicos'] ?></h4>
                <small>Clientes Únicos</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Paquetes Más Populares -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Paquetes Más Populares</h5>
            </div>
            <div class="card-body">
                <?php if (empty($paquetes_populares)): ?>
                    <p class="text-muted">No hay datos de reservas</p>
                <?php else: ?>
                    <?php foreach ($paquetes_populares as $paquete): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small"><?= htmlspecialchars($paquete['paquete']) ?></span>
                            <span class="badge bg-primary"><?= $paquete['total_reservas'] ?></span>
                        </div>
                        <div class="small text-muted">
                            <?= $paquete['total_personas'] ?> personas (Promedio: <?= $paquete['promedio_personas'] ?>)
                        </div>
                        <hr class="my-2">
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lista de Reservas -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Reservas</h5>
                <div>
                    <button class="btn btn-success btn-sm" onclick="nuevaReserva()">
                        <i class="fas fa-plus"></i> Nueva
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="exportarReservas()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($reservas)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-suitcase fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay reservas registradas</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Paquete</th>
                                    <th>Personas</th>
                                    <th>Fecha Viaje</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td><?= $reserva['id'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($reserva['nombre_cliente']) ?></div>
                                        <div class="small text-muted">
                                            <a href="mailto:<?= htmlspecialchars($reserva['email_cliente']) ?>">
                                                <?= htmlspecialchars($reserva['email_cliente']) ?>
                                            </a>
                                        </div>
                                        <?php if ($reserva['telefono_cliente']): ?>
                                        <div class="small">
                                            <a href="tel:<?= htmlspecialchars($reserva['telefono_cliente']) ?>">
                                                <?= htmlspecialchars($reserva['telefono_cliente']) ?>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold small"><?= htmlspecialchars($reserva['paquete']) ?></div>
                                        <div class="small text-success"><?= htmlspecialchars($reserva['precio']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $reserva['numero_personas'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($reserva['fecha_viaje_formateada']): ?>
                                            <?= $reserva['fecha_viaje_formateada'] ?>
                                        <?php else: ?>
                                            <span class="text-muted">No especificada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = [
                                            'pendiente' => 'bg-warning',
                                            'confirmada' => 'bg-success',
                                            'cancelada' => 'bg-danger'
                                        ];
                                        ?>
                                        <span class="badge <?= $badge_class[$reserva['estado']] ?? 'bg-secondary' ?>">
                                            <?= ucfirst($reserva['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" 
                                                    onclick="verReserva(<?= $reserva['id'] ?>)"
                                                    title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($reserva['estado'] === 'pendiente'): ?>
                                            <button class="btn btn-outline-success" 
                                                    onclick="confirmarReserva(<?= $reserva['id'] ?>)"
                                                    title="Confirmar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="cancelarReserva(<?= $reserva['id'] ?>)"
                                                    title="Cancelar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de reserva -->
<div class="modal fade" id="reservaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reservaDetalles"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verReserva(id) {
    // Buscar la reserva en los datos PHP
    <?php
    echo "const reservas = " . json_encode($reservas) . ";";
    ?>
    
    const reserva = reservas.find(r => r.id == id);
    if (reserva) {
        const detalles = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Información del Cliente</h6>
                    <p><strong>Nombre:</strong> ${reserva.nombre_cliente}</p>
                    <p><strong>Email:</strong> ${reserva.email_cliente}</p>
                    <p><strong>Teléfono:</strong> ${reserva.telefono_cliente || 'No especificado'}</p>
                </div>
                <div class="col-md-6">
                    <h6>Información del Viaje</h6>
                    <p><strong>Paquete:</strong> ${reserva.paquete}</p>
                    <p><strong>Precio:</strong> ${reserva.precio}</p>
                    <p><strong>Número de personas:</strong> ${reserva.numero_personas}</p>
                    <p><strong>Fecha de viaje:</strong> ${reserva.fecha_viaje_formateada || 'No especificada'}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h6>Comentarios</h6>
                    <p>${reserva.comentarios || 'Sin comentarios'}</p>
                    <hr>
                    <p><strong>Fecha de reserva:</strong> ${reserva.fecha_reserva_formateada}</p>
                    <p><strong>Estado:</strong> <span class="badge bg-primary">${reserva.estado}</span></p>
                </div>
            </div>
        `;
        document.getElementById('reservaDetalles').innerHTML = detalles;
        new bootstrap.Modal(document.getElementById('reservaModal')).show();
    }
}

function confirmarReserva(id) {
    if (confirm('¿Confirmar esta reserva?')) {
        alert('Funcionalidad de confirmación - ID: ' + id);
        location.reload();
    }
}

function cancelarReserva(id) {
    if (confirm('¿Cancelar esta reserva?')) {
        alert('Funcionalidad de cancelación - ID: ' + id);
        location.reload();
    }
}

function nuevaReserva() {
    alert('Funcionalidad para crear nueva reserva manualmente');
}

function exportarReservas() {
    alert('Funcionalidad de exportación de reservas - Se puede implementar para generar CSV o PDF');
}
</script>
