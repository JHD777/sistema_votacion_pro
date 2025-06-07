<?php
require_once __DIR__ . '/../../controllers/ElectionController.php';
require_once __DIR__ . '/../../controllers/VoteController.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$electionController = new ElectionController();
$voteController = new VoteController();

$elecciones_actuales = $electionController->getCurrentElections();
$elecciones_proximas = $electionController->getUpcomingElections();
$voto = new Voto();
$mis_votos = $voto->getByUserId($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1><i class="bi bi-person-circle"></i> Mi Panel</h1>
                <p class="lead">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-calendar-event"></i> Elecciones Actuales</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['admin_rol']) && $_SESSION['admin_rol'] === 'super'): ?>
                            <div class="alert alert-info">
                                <strong>Debug Elecciones Activas:</strong><br>
                                Hora actual del sistema: <?php echo date('d/m/Y H:i:s'); ?><br>
                                Elecciones traídas por <code>getCurrentElections()</code>:<br>
                                <pre><?php print_r($elecciones_actuales); ?></pre>
                            </div>
                        <?php endif; ?>
                        <?php if (count($elecciones_actuales) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($elecciones_actuales as $eleccion): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($eleccion['titulo']); ?></h5>
                                            <small>Finaliza: <?php echo date('d/m/Y H:i', strtotime($eleccion['fecha_fin'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars($eleccion['descripcion']); ?></p>
                                        <?php 
                                            $yaVoto = false;
                                            if ($mis_votos) {
                                                foreach ($mis_votos as $voto) {
                                                    if ($voto['eleccion_id'] == $eleccion['id']) {
                                                        $yaVoto = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <?php if ($yaVoto): ?>
                                            <span class="badge bg-success">Ya has votado</span>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_URL; ?>/user/vote?election=<?php echo $eleccion['id']; ?>" class="btn btn-primary btn-sm mt-2">Votar</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center">No hay elecciones activas en este momento.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-calendar-plus"></i> Próximas Elecciones</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($elecciones_proximas) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($elecciones_proximas as $eleccion): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($eleccion['titulo']); ?></h5>
                                            <small>Inicia: <?php echo date('d/m/Y H:i', strtotime($eleccion['fecha_inicio'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars($eleccion['descripcion']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center">No hay próximas elecciones programadas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-person"></i> Mi Perfil</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="https://via.placeholder.com/150" class="rounded-circle" alt="Foto de perfil">
                        </div>
                        <h5 class="card-title text-center"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h5>
                        <p class="card-text text-center"><?php echo htmlspecialchars(isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''); ?></p>
                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>/user/profile" class="btn btn-primary">Editar Perfil</a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-check-circle"></i> Mis Votos</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($mis_votos && count($mis_votos) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($mis_votos as $voto): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($voto['candidato_nombre']); ?>
                                        <span class="badge bg-primary rounded-pill"><?php echo date('d/m/Y', strtotime($voto['fecha_voto'])); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center">Aún no has emitido ningún voto.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <p class="text-end text-muted">Hora del sistema: <?php echo date('d/m/Y H:i:s'); ?></p>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>