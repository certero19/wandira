<?php
require_once 'config.php';

// Verificar si se solicita una acción específica
$accion = $_GET['accion'] ?? 'dashboard';

try {
    $pdo = conectarDB();
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeTravel - Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <i class="fas fa-cog"></i> BeTravel Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-home"></i> Volver al Sitio
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bars"></i> Menú</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="admin.php?accion=dashboard" class="list-group-item list-group-item-action <?= $accion === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                        <a href="admin.php?accion=contactos" class="list-group-item list-group-item-action <?= $accion === 'contactos' ? 'active' : '' ?>">
                            <i class="fas fa-envelope"></i> Contactos
                        </a>
                        <a href="admin.php?accion=favoritos" class="list-group-item list-group-item-action <?= $accion === 'favoritos' ? 'active' : '' ?>">
                            <i class="fas fa-heart"></i> Favoritos
                        </a>
                        <a href="admin.php?accion=reservas" class="list-group-item list-group-item-action <?= $accion === 'reservas' ? 'active' : '' ?>">
                            <i class="fas fa-suitcase"></i> Reservas
                        </a>
                        <a href="admin.php?accion=estadisticas" class="list-group-item list-group-item-action <?= $accion === 'estadisticas' ? 'active' : '' ?>">
                            <i class="fas fa-chart-pie"></i> Estadísticas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9">
                <?php
                switch ($accion) {
                    case 'dashboard':
                        include 'admin_dashboard.php';
                        break;
                    case 'contactos':
                        include 'admin_contactos.php';
                        break;
                    case 'favoritos':
                        include 'admin_favoritos.php';
                        break;
                    case 'reservas':
                        include 'admin_reservas.php';
                        break;
                    case 'estadisticas':
                        include 'admin_estadisticas.php';
                        break;
                    default:
                        echo '<div class="alert alert-warning">Sección no encontrada</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
