<?php

use Controllers\AdminController;

require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/ElectionController.php';
require_once __DIR__ . '/../../helpers/AuthHelper.php';

// Verificar si el usuario es administrador
AuthHelper::verificarAdmin();
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$adminController = new AdminController();
$electionController = new ElectionController();

// Obtener estadísticas del sistema
$stats = $adminController->getSystemStats();
$elecciones = $electionController->getAllElections();

// Inicializar valores predeterminados para evitar errores
$total_usuarios = 0;
$usuarios_verificados = 0;
$total_votos = 0;
$porcentaje_participacion = 0;

// Verificar si las estadísticas existen y asignar valores
if (isset($stats['stats'])) {
    $total_usuarios = isset($stats['stats']['total_usuarios']) ? $stats['stats']['total_usuarios'] : 0;
    $usuarios_verificados = isset($stats['stats']['usuarios_verificados']) ? $stats['stats']['usuarios_verificados'] : 0;
    $total_votos = isset($stats['stats']['total_votos']) ? $stats['stats']['total_votos'] : 0;
    $porcentaje_participacion = isset($stats['stats']['porcentaje_participacion']) ? $stats['stats']['porcentaje_participacion'] : 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1><i class="bi bi-speedometer2"></i> Panel de Administración</h1>
                
            </div>
        </div>
        
        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people"></i> Usuarios Registrados</h5>
                        <h2 class="display-4"><?php echo $total_usuarios; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-person-check"></i> Usuarios Verificados</h5>
                        <h2 class="display-4"><?php echo $usuarios_verificados; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-check2-square"></i> Votos Emitidos</h5>
                        <h2 class="display-4"><?php echo $total_votos; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-graph-up"></i> Participación</h5>
                        <h2 class="display-4"><?php echo $porcentaje_participacion; ?>%</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Acciones rápidas -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-lightning"></i> Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>?view=admin/elections" class="btn btn-primary w-100 p-3">
                                    <i class="bi bi-calendar-event"></i> Gestionar Elecciones
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>?view=admin/candidates" class="btn btn-success w-100 p-3">
                                    <i class="bi bi-person-badge"></i> Gestionar Candidatos
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>?view=admin/users" class="btn btn-info w-100 p-3">
                                    <i class="bi bi-people"></i> Gestionar Usuarios
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>?view=admin/results" class="btn btn-warning w-100 p-3">
                                    <i class="bi bi-bar-chart"></i> Ver Resultados
                                </a>
                            </div>
                            
                            <?php if (isset($_SESSION['admin_rol']) && $_SESSION['admin_rol'] === 'super'): ?>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>?view=admin/admins" class="btn btn-danger w-100 p-3">
                                    <i class="bi bi-people-fill"></i> Gestionar Admins
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>?view=admin/personalizacion" class="btn btn-info w-100 p-3">
                                    <i class="bi bi-palette"></i> Personalizar Sistema
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Elecciones recientes -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Elecciones Recientes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($elecciones) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($elecciones as $eleccion): ?>
                                            <?php 
                                                $now = new DateTime();
                                                $inicio = new DateTime($eleccion['fecha_inicio']);
                                                $fin = new DateTime($eleccion['fecha_fin']);
                                                
                                                if ($now < $inicio) {
                                                    $estado = 'Pendiente';
                                                    $badge_class = 'bg-warning';
                                                } elseif ($now >= $inicio && $now <= $fin) {
                                                    $estado = 'En curso';
                                                    $badge_class = 'bg-success';
                                                } else {
                                                    $estado = 'Finalizada';
                                                    $badge_class = 'bg-secondary';
                                                }
                                                
                                                if ($eleccion['activo'] == 0) {
                                                    $estado = 'Inactiva';
                                                    $badge_class = 'bg-danger';
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($eleccion['titulo']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($eleccion['fecha_inicio'])); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($eleccion['fecha_fin'])); ?></td>
                                                <td><span class="badge <?php echo $badge_class; ?>"><?php echo $estado; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <a href="<?php echo BASE_URL; ?>/admin/elections" class="btn btn-primary">Ver todas las elecciones</a>
                            </div>
                        <?php else: ?>
                            <p class="text-center">No hay elecciones registradas.</p>
                            <div class="text-center">
                                <a href="<?php echo BASE_URL; ?>?view=admin/elections" class="btn btn-primary">Crear nueva elección</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>