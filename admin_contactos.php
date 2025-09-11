<?php
// Gestión de contactos en el admin
try {
    // Obtener todos los contactos
    $sql = "SELECT id, nombre, email, telefono, asunto, mensaje, newsletter, 
                   DATE_FORMAT(fecha_creacion, '%d/%m/%Y %H:%i') as fecha_formateada,
                   estado
            FROM contactos 
            ORDER BY fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $contactos = $stmt->fetchAll();
    
    // Estadísticas de contactos
    $sql_stats = "SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN estado = 'nuevo' THEN 1 END) as nuevos,
        COUNT(CASE WHEN estado = 'en_proceso' THEN 1 END) as en_proceso,
        COUNT(CASE WHEN estado = 'resuelto' THEN 1 END) as resueltos,
        COUNT(CASE WHEN newsletter = 1 THEN 1 END) as newsletter_suscriptores
        FROM contactos";
    
    $stmt = $pdo->prepare($sql_stats);
    $stmt->execute();
    $stats = $stmt->fetch();
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar contactos: ' . $e->getMessage() . '</div>';
    return;
}
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-envelope"></i> Gestión de Contactos</h2>
        <p class="text-muted">Administra todos los mensajes de contacto recibidos</p>
    </div>
</div>

<!-- Estadísticas de Contactos -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary"><?= $stats['total'] ?></h4>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning"><?= $stats['nuevos'] ?></h4>
                <small>Nuevos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info"><?= $stats['en_proceso'] ?></h4>
                <small>En Proceso</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?= $stats['resueltos'] ?></h4>
                <small>Resueltos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-secondary"><?= $stats['newsletter_suscriptores'] ?></h4>
                <small>Newsletter</small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Contactos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Contactos</h5>
        <div>
            <button class="btn btn-primary btn-sm" onclick="exportarContactos()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($contactos)): ?>
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay contactos registrados</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Newsletter</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contactos as $contacto): ?>
                        <tr>
                            <td><?= $contacto['id'] ?></td>
                            <td><?= htmlspecialchars($contacto['nombre']) ?></td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($contacto['email']) ?>">
                                    <?= htmlspecialchars($contacto['email']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($contacto['telefono']): ?>
                                    <a href="tel:<?= htmlspecialchars($contacto['telefono']) ?>">
                                        <?= htmlspecialchars($contacto['telefono']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($contacto['asunto']) ?>
                                </span>
                            </td>
                            <td><?= $contacto['fecha_formateada'] ?></td>
                            <td>
                                <?php
                                $badge_class = [
                                    'nuevo' => 'bg-warning',
                                    'en_proceso' => 'bg-info',
                                    'resuelto' => 'bg-success'
                                ];
                                ?>
                                <span class="badge <?= $badge_class[$contacto['estado']] ?? 'bg-secondary' ?>">
                                    <?= ucfirst($contacto['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($contacto['newsletter']): ?>
                                    <i class="fas fa-check text-success" title="Suscrito"></i>
                                <?php else: ?>
                                    <i class="fas fa-times text-muted" title="No suscrito"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" 
                                            onclick="verMensaje(<?= $contacto['id'] ?>, '<?= htmlspecialchars($contacto['mensaje'], ENT_QUOTES) ?>')"
                                            title="Ver mensaje">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" 
                                            onclick="cambiarEstado(<?= $contacto['id'] ?>, 'resuelto')"
                                            title="Marcar como resuelto">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" 
                                            onclick="eliminarContacto(<?= $contacto['id'] ?>)"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
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

<!-- Modal para ver mensaje -->
<div class="modal fade" id="mensajeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mensaje de Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="mensajeContenido"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verMensaje(id, mensaje) {
    document.getElementById('mensajeContenido').innerHTML = '<p>' + mensaje + '</p>';
    new bootstrap.Modal(document.getElementById('mensajeModal')).show();
}

function cambiarEstado(id, nuevoEstado) {
    if (confirm('¿Estás seguro de cambiar el estado de este contacto?')) {
        // Aquí se implementaría la llamada AJAX para cambiar el estado
        alert('Funcionalidad de cambio de estado - ID: ' + id + ', Nuevo estado: ' + nuevoEstado);
        location.reload();
    }
}

function eliminarContacto(id) {
    if (confirm('¿Estás seguro de eliminar este contacto? Esta acción no se puede deshacer.')) {
        // Aquí se implementaría la llamada AJAX para eliminar
        alert('Funcionalidad de eliminación - ID: ' + id);
        location.reload();
    }
}

function exportarContactos() {
    alert('Funcionalidad de exportación - Se puede implementar para generar CSV o Excel');
}
</script>
