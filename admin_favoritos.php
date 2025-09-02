<?php
// Gestión de favoritos en el admin
try {
    // Obtener todos los favoritos con información del usuario
    $sql = "SELECT df.id, df.usuario_ip, df.destino, 
                   DATE_FORMAT(df.fecha_agregado, '%d/%m/%Y %H:%i') as fecha_formateada,
                   COUNT(*) OVER (PARTITION BY df.usuario_ip) as total_usuario,
                   COUNT(*) OVER (PARTITION BY df.destino) as popularidad_destino
            FROM destinos_favoritos df
            ORDER BY df.fecha_agregado DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $favoritos = $stmt->fetchAll();
    
    // Estadísticas de favoritos
    $sql_stats = "SELECT 
        COUNT(*) as total_favoritos,
        COUNT(DISTINCT usuario_ip) as usuarios_unicos,
        COUNT(DISTINCT destino) as destinos_unicos,
        (SELECT destino FROM destinos_favoritos GROUP BY destino ORDER BY COUNT(*) DESC LIMIT 1) as destino_mas_popular
        FROM destinos_favoritos";
    
    $stmt = $pdo->prepare($sql_stats);
    $stmt->execute();
    $stats = $stmt->fetch();
    
    // Top destinos
    $sql_top = "SELECT destino, COUNT(*) as total, 
                       ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM destinos_favoritos)), 1) as porcentaje
                FROM destinos_favoritos 
                GROUP BY destino 
                ORDER BY total DESC 
                LIMIT 10";
    
    $stmt = $pdo->prepare($sql_top);
    $stmt->execute();
    $top_destinos = $stmt->fetchAll();
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar favoritos: ' . $e->getMessage() . '</div>';
    return;
}
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-heart"></i> Gestión de Favoritos</h2>
        <p class="text-muted">Administra los destinos favoritos de los usuarios</p>
    </div>
</div>

<!-- Estadísticas de Favoritos -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-danger"><?= $stats['total_favoritos'] ?></h4>
                <small>Total Favoritos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info"><?= $stats['usuarios_unicos'] ?></h4>
                <small>Usuarios Únicos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?= $stats['destinos_unicos'] ?></h4>
                <small>Destinos Únicos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-warning"><?= htmlspecialchars($stats['destino_mas_popular'] ?? 'N/A') ?></h6>
                <small>Más Popular</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Destinos -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Top Destinos Favoritos</h5>
            </div>
            <div class="card-body">
                <?php if (empty($top_destinos)): ?>
                    <p class="text-muted">No hay datos de favoritos</p>
                <?php else: ?>
                    <?php foreach ($top_destinos as $destino): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold"><?= htmlspecialchars($destino['destino']) ?></span>
                            <span class="badge bg-primary"><?= $destino['total'] ?> (<?= $destino['porcentaje'] ?>%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: <?= $destino['porcentaje'] ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lista de Favoritos -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Favoritos Recientes</h5>
                <button class="btn btn-primary btn-sm" onclick="exportarFavoritos()">
                    <i class="fas fa-download"></i> Exportar
                </button>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($favoritos)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay favoritos registrados</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario IP</th>
                                    <th>Destino</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($favoritos, 0, 20) as $favorito): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary" title="Total favoritos de este usuario: <?= $favorito['total_usuario'] ?>">
                                            <?= htmlspecialchars($favorito['usuario_ip']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold" title="Popularidad: <?= $favorito['popularidad_destino'] ?> usuarios">
                                            <?= htmlspecialchars($favorito['destino']) ?>
                                        </span>
                                    </td>
                                    <td><?= $favorito['fecha_formateada'] ?></td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="eliminarFavorito(<?= $favorito['id'] ?>)"
                                                title="Eliminar favorito">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($favoritos) > 20): ?>
                        <div class="text-center mt-3">
                            <small class="text-muted">Mostrando los 20 más recientes de <?= count($favoritos) ?> total</small>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Análisis por Usuario -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Análisis por Usuario</h5>
            </div>
            <div class="card-body">
                <?php
                // Agrupar favoritos por usuario
                $usuarios_favoritos = [];
                foreach ($favoritos as $favorito) {
                    $ip = $favorito['usuario_ip'];
                    if (!isset($usuarios_favoritos[$ip])) {
                        $usuarios_favoritos[$ip] = [];
                    }
                    $usuarios_favoritos[$ip][] = $favorito['destino'];
                }
                
                // Ordenar por cantidad de favoritos
                uasort($usuarios_favoritos, function($a, $b) {
                    return count($b) - count($a);
                });
                ?>
                
                <div class="row">
                    <?php foreach (array_slice($usuarios_favoritos, 0, 6, true) as $ip => $destinos): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($ip) ?>
                                </h6>
                                <p class="card-text">
                                    <strong><?= count($destinos) ?></strong> destinos favoritos
                                </p>
                                <div class="small">
                                    <?php foreach (array_slice($destinos, 0, 3) as $destino): ?>
                                        <span class="badge bg-light text-dark me-1"><?= htmlspecialchars($destino) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($destinos) > 3): ?>
                                        <span class="text-muted">+<?= count($destinos) - 3 ?> más</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function eliminarFavorito(id) {
    if (confirm('¿Estás seguro de eliminar este favorito?')) {
        // Aquí se implementaría la llamada AJAX para eliminar
        alert('Funcionalidad de eliminación - ID: ' + id);
        location.reload();
    }
}

function exportarFavoritos() {
    alert('Funcionalidad de exportación de favoritos - Se puede implementar para generar CSV');
}
</script>
