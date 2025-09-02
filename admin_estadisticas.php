<?php
// Estadísticas generales del admin
try {
    // Estadísticas generales
    $sql_general = "SELECT 
        (SELECT COUNT(*) FROM contactos) as total_contactos,
        (SELECT COUNT(*) FROM destinos_favoritos) as total_favoritos,
        (SELECT COUNT(*) FROM reservas_paquetes) as total_reservas,
        (SELECT COUNT(*) FROM estadisticas_web) as total_estadisticas,
        (SELECT COUNT(DISTINCT usuario_ip) FROM destinos_favoritos) as usuarios_unicos";
    
    $stmt = $pdo->prepare($sql_general);
    $stmt->execute();
    $stats_general = $stmt->fetch();
    
    // Contactos por mes (últimos 6 meses)
    $sql_contactos_mes = "SELECT 
        DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
        COUNT(*) as total
        FROM contactos 
        WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
        ORDER BY mes DESC";
    
    $stmt = $pdo->prepare($sql_contactos_mes);
    $stmt->execute();
    $contactos_por_mes = $stmt->fetchAll();
    
    // Destinos más populares
    $sql_destinos_pop = "SELECT destino, COUNT(*) as total,
                                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM destinos_favoritos)), 1) as porcentaje
                         FROM destinos_favoritos 
                         GROUP BY destino 
                         ORDER BY total DESC 
                         LIMIT 8";
    
    $stmt = $pdo->prepare($sql_destinos_pop);
    $stmt->execute();
    $destinos_populares = $stmt->fetchAll();
    
    // Reservas por estado
    $sql_reservas_estado = "SELECT estado, COUNT(*) as total
                           FROM reservas_paquetes 
                           GROUP BY estado";
    
    $stmt = $pdo->prepare($sql_reservas_estado);
    $stmt->execute();
    $reservas_por_estado = $stmt->fetchAll();
    
    // Actividad reciente (últimas 24 horas)
    $sql_actividad = "SELECT pagina, accion, COUNT(*) as total
                      FROM estadisticas_web 
                      WHERE fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                      GROUP BY pagina, accion
                      ORDER BY total DESC
                      LIMIT 10";
    
    $stmt = $pdo->prepare($sql_actividad);
    $stmt->execute();
    $actividad_reciente = $stmt->fetchAll();
    
    // Top usuarios por favoritos
    $sql_top_usuarios = "SELECT usuario_ip, COUNT(*) as total_favoritos,
                                GROUP_CONCAT(DISTINCT destino ORDER BY destino SEPARATOR ', ') as destinos
                         FROM destinos_favoritos 
                         GROUP BY usuario_ip 
                         ORDER BY total_favoritos DESC 
                         LIMIT 5";
    
    $stmt = $pdo->prepare($sql_top_usuarios);
    $stmt->execute();
    $top_usuarios = $stmt->fetchAll();
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar estadísticas: ' . $e->getMessage() . '</div>';
    return;
}
?>

<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-chart-pie"></i> Estadísticas Generales</h2>
        <p class="text-muted">Análisis completo del rendimiento del sitio web</p>
    </div>
</div>

<!-- Resumen General -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tachometer-alt"></i> Resumen General</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-primary"><?= $stats_general['total_contactos'] ?></h3>
                            <p class="mb-0">Contactos</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-danger"><?= $stats_general['total_favoritos'] ?></h3>
                            <p class="mb-0">Favoritos</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-success"><?= $stats_general['total_reservas'] ?></h3>
                            <p class="mb-0">Reservas</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-info"><?= $stats_general['usuarios_unicos'] ?></h3>
                            <p class="mb-0">Usuarios Únicos</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-warning"><?= $stats_general['total_estadisticas'] ?></h3>
                            <p class="mb-0">Eventos</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h3 class="text-secondary">
                            <?= $stats_general['total_favoritos'] > 0 ? round($stats_general['total_favoritos'] / $stats_general['usuarios_unicos'], 1) : 0 ?>
                        </h3>
                        <p class="mb-0">Favoritos/Usuario</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico de Contactos por Mes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Contactos por Mes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($contactos_por_mes)): ?>
                    <p class="text-muted">No hay datos suficientes</p>
                <?php else: ?>
                    <canvas id="contactosChart" width="400" height="200"></canvas>
                    <script>
                        // Datos para el gráfico
                        const contactosData = <?= json_encode(array_reverse($contactos_por_mes)) ?>;
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Destinos Más Populares -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-heart"></i> Destinos Más Populares</h5>
            </div>
            <div class="card-body">
                <?php if (empty($destinos_populares)): ?>
                    <p class="text-muted">No hay datos de favoritos</p>
                <?php else: ?>
                    <?php foreach ($destinos_populares as $destino): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold"><?= htmlspecialchars($destino['destino']) ?></span>
                            <span class="badge bg-primary"><?= $destino['total'] ?> (<?= $destino['porcentaje'] ?>%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: <?= min($destino['porcentaje'] * 2, 100) ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Estado de Reservas -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie"></i> Estado de Reservas</h5>
            </div>
            <div class="card-body">
                <?php if (empty($reservas_por_estado)): ?>
                    <p class="text-muted">No hay reservas</p>
                <?php else: ?>
                    <?php 
                    $total_reservas = array_sum(array_column($reservas_por_estado, 'total'));
                    $colores = [
                        'pendiente' => 'warning',
                        'confirmada' => 'success',
                        'cancelada' => 'danger'
                    ];
                    ?>
                    <?php foreach ($reservas_por_estado as $estado): ?>
                    <?php $porcentaje = round(($estado['total'] / $total_reservas) * 100, 1); ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold"><?= ucfirst($estado['estado']) ?></span>
                            <span class="badge bg-<?= $colores[$estado['estado']] ?? 'secondary' ?>">
                                <?= $estado['total'] ?> (<?= $porcentaje ?>%)
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-<?= $colores[$estado['estado']] ?? 'secondary' ?>" 
                                 role="progressbar" style="width: <?= $porcentaje ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Actividad Reciente (24h)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($actividad_reciente)): ?>
                    <p class="text-muted">No hay actividad registrada</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Página</th>
                                    <th>Acción</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($actividad_reciente as $actividad): ?>
                                <tr>
                                    <td><?= htmlspecialchars($actividad['pagina']) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($actividad['accion']) ?>
                                        </span>
                                    </td>
                                    <td><?= $actividad['total'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Usuarios -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Top Usuarios</h5>
            </div>
            <div class="card-body">
                <?php if (empty($top_usuarios)): ?>
                    <p class="text-muted">No hay usuarios con favoritos</p>
                <?php else: ?>
                    <?php foreach ($top_usuarios as $usuario): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small"><?= htmlspecialchars($usuario['usuario_ip']) ?></span>
                            <span class="badge bg-primary"><?= $usuario['total_favoritos'] ?></span>
                        </div>
                        <div class="small text-muted">
                            <?= htmlspecialchars(substr($usuario['destinos'], 0, 50)) ?>
                            <?= strlen($usuario['destinos']) > 50 ? '...' : '' ?>
                        </div>
                        <hr class="my-2">
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Información del Sistema -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Información del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h6>Base de Datos</h6>
                        <p>MySQL - <?= DB_NAME ?></p>
                        <span class="badge bg-success">Conectada</span>
                    </div>
                    <div class="col-md-3">
                        <h6>Servidor</h6>
                        <p><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido' ?></p>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    <div class="col-md-3">
                        <h6>PHP</h6>
                        <p>Versión <?= PHP_VERSION ?></p>
                        <span class="badge bg-info">Funcionando</span>
                    </div>
                    <div class="col-md-3">
                        <h6>Última Actualización</h6>
                        <p><?= date('d/m/Y H:i:s') ?></p>
                        <span class="badge bg-secondary">Ahora</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de contactos por mes
<?php if (!empty($contactos_por_mes)): ?>
const ctx = document.getElementById('contactosChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: contactosData.map(item => {
                const [year, month] = item.mes.split('-');
                return new Date(year, month - 1).toLocaleDateString('es-ES', { 
                    year: 'numeric', 
                    month: 'short' 
                });
            }),
            datasets: [{
                label: 'Contactos',
                data: contactosData.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
<?php endif; ?>
</script>
