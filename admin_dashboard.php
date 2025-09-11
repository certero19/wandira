<?php
// Dashboard principal del admin
try {
    // Obtener estadísticas generales
    $sql_stats = "SELECT 
        (SELECT COUNT(*) FROM contactos) as total_contactos,
        (SELECT COUNT(*) FROM destinos_favoritos) as total_favoritos,
        (SELECT COUNT(*) FROM reservas_paquetes) as total_reservas,
        (SELECT COUNT(DISTINCT usuario_ip) FROM destinos_favoritos) as usuarios_unicos";
    
    $stmt = $pdo->prepare($sql_stats);
    $stmt->execute();
    $stats = $stmt->fetch();
    
    // Obtener contactos recientes
    $sql_contactos = "SELECT nombre, email, asunto, DATE_FORMAT(fecha_creacion, '%d/%m/%Y %H:%i') as fecha 
                      FROM contactos ORDER BY fecha_creacion DESC LIMIT 5";
    $stmt = $pdo->prepare($sql_contactos);
    $stmt->execute();
    $contactos_recientes = $stmt->fetchAll();
    
    // Obtener destinos más populares
    $sql_populares = "SELECT destino, COUNT(*) as total FROM destinos_favoritos 
                      GROUP BY destino ORDER BY total DESC LIMIT 5";
    $stmt = $pdo->prepare($sql_populares);
    $stmt->execute();
    $destinos_populares = $stmt->fetchAll();
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar datos: ' . $e->getMessage() . '</div>';
    return;
}
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-chart-bar"></i> Dashboard</h2>
        <p class="text-muted">Resumen general del sistema BeTravel</p>
    </div>
</div>

<!-- Tarjetas de estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_contactos'] ?></h4>
                        <p class="mb-0">Contactos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_favoritos'] ?></h4>
                        <p class="mb-0">Favoritos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-heart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_reservas'] ?></h4>
                        <p class="mb-0">Reservas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-suitcase fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['usuarios_unicos'] ?></h4>
                        <p class="mb-0">Usuarios</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Contactos Recientes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-envelope"></i> Contactos Recientes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($contactos_recientes)): ?>
                    <p class="text-muted">No hay contactos registrados</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Asunto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contactos_recientes as $contacto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($contacto['nombre']) ?></td>
                                    <td><?= htmlspecialchars($contacto['email']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($contacto['asunto']) ?>
                                        </span>
                                    </td>
                                    <td><?= $contacto['fecha'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="admin.php?accion=contactos" class="btn btn-primary btn-sm">
                            Ver Todos los Contactos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Destinos Populares -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-heart"></i> Destinos Más Populares</h5>
            </div>
            <div class="card-body">
                <?php if (empty($destinos_populares)): ?>
                    <p class="text-muted">No hay destinos favoritos registrados</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Destino</th>
                                    <th>Favoritos</th>
                                    <th>Popularidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $max_favoritos = max(array_column($destinos_populares, 'total'));
                                foreach ($destinos_populares as $destino): 
                                    $porcentaje = ($destino['total'] / $max_favoritos) * 100;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($destino['destino']) ?></td>
                                    <td><?= $destino['total'] ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $porcentaje ?>%">
                                                <?= round($porcentaje) ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="admin.php?accion=favoritos" class="btn btn-success btn-sm">
                            Ver Todos los Favoritos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Estado del Sistema -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-server"></i> Estado del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6>Base de Datos</h6>
                        <span class="badge bg-success">
                            <i class="fas fa-check"></i> Conectada
                        </span>
                    </div>
                    <div class="col-md-4">
                        <h6>Servidor Web</h6>
                        <span class="badge bg-success">
                            <i class="fas fa-check"></i> Funcionando
                        </span>
                    </div>
                    <div class="col-md-4">
                        <h6>Última Actualización</h6>
                        <span class="text-muted"><?= date('d/m/Y H:i:s') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
