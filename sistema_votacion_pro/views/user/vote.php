<?php
require_once __DIR__ . '/../../controllers/ElectionController.php';
require_once __DIR__ . '/../../controllers/CandidateController.php';
require_once __DIR__ . '/../../controllers/VoteController.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// Verificar si se proporcionó un ID de elección
if (!isset($_GET['election']) || empty($_GET['election'])) {
    header('Location: ' . BASE_URL . '/user/dashboard');
    exit;
}

$electionId = $_GET['election'];
$electionController = new ElectionController();
$candidateController = new CandidateController();
$voteController = new VoteController();

// Obtener información de la elección
$eleccion = $electionController->getElectionById($electionId);

// Verificar si la elección existe
if (!$eleccion) {
    header('Location: ' . BASE_URL . '/user/dashboard');
    exit;
}

// Verificar si la elección está activa
$now = new DateTime();
$inicio = new DateTime($eleccion['fecha_inicio']);
$fin = new DateTime($eleccion['fecha_fin']);

if ($now < $inicio || $now > $fin || $eleccion['activo'] != 1) {
    header('Location: ' . BASE_URL . '/user/dashboard');
    exit;
}

// Verificar si el usuario ya ha votado en esta elección
$voto = new Voto();
$yaVoto = false;
$mis_votos = $voto->getByUserId($_SESSION['user_id']);

if ($mis_votos) {
    foreach ($mis_votos as $v) {
        if ($v['eleccion_id'] == $electionId) {
            $yaVoto = true;
            break;
        }
    }
}

// Obtener candidatos de la elección
$candidatos = $candidateController->getCandidatesByElection($electionId);

// Procesar el voto
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidato_id']) && !$yaVoto) {
    $candidato_id = $_POST['candidato_id'];
    $resultado = $voteController->registerVote($candidato_id);
    
    if ($resultado['success']) {
        $mensaje = $resultado['message'];
        $tipo_mensaje = 'success';
        $yaVoto = true;
    } else {
        $mensaje = $resultado['message'];
        $tipo_mensaje = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votar - <?php echo htmlspecialchars($eleccion['titulo']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1><i class="bi bi-check2-square"></i> Votar: <?php echo htmlspecialchars($eleccion['titulo']); ?></h1>
                <p class="lead"><?php echo htmlspecialchars($eleccion['descripcion']); ?></p>
                <p><strong>Finaliza:</strong> <?php echo date('d/m/Y H:i', strtotime($eleccion['fecha_fin'])); ?></p>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($yaVoto): ?>
            <div class="alert alert-info" role="alert">
                Ya has emitido tu voto en esta elección. Gracias por participar.
            </div>
            <div class="text-center mt-4">
                <a href="<?php echo BASE_URL; ?>/user/dashboard" class="btn btn-primary">Volver al Panel</a>
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Selecciona un candidato para votar</h5>
                    <p class="card-text text-danger">Importante: Una vez emitido tu voto, no podrás cambiarlo.</p>
                    
                    <?php if (count($candidatos) > 0): ?>
                        <form id="voteForm" method="POST" action="">
                            <div class="row">
                                <?php foreach ($candidatos as $candidato): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card candidate-card h-100">
                                            <div class="card-body text-center">
                                                <?php if (!empty($candidato['foto'])): ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/<?php echo $candidato['foto']; ?>" class="candidate-image" alt="<?php echo htmlspecialchars($candidato['nombre']); ?>">
                                                <?php else: ?>
                                                    <img src="<?php echo BASE_URL; ?>/assets/img/candidate-placeholder.png" class="candidate-image" alt="<?php echo htmlspecialchars($candidato['nombre']); ?>">
                                                <?php endif; ?>
                                                <h5 class="card-title"><?php echo htmlspecialchars($candidato['nombre'] . ' ' . $candidato['apellido']); ?></h5>
                                                <p class="card-text"><?php echo htmlspecialchars($candidato['biografia']); ?></p>
                                                <div class="form-check">
                                                    <input class="form-check-input candidate-radio" type="radio" name="candidato_id" id="candidato_<?php echo $candidato['id']; ?>" value="<?php echo $candidato['id']; ?>" required>
                                                    <label class="form-check-label" for="candidato_<?php echo $candidato['id']; ?>">
                                                        Seleccionar
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-primary" id="confirmVoteBtn">Emitir Voto</button>
                                <a href="<?php echo BASE_URL; ?>/user/dashboard" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="text-center">No hay candidatos registrados para esta elección.</p>
                        <div class="text-center mt-4">
                            <a href="<?php echo BASE_URL; ?>/user/dashboard" class="btn btn-primary">Volver al Panel</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Voto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas emitir tu voto? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="submitVoteBtn">Confirmar Voto</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmVoteBtn = document.getElementById('confirmVoteBtn');
            const submitVoteBtn = document.getElementById('submitVoteBtn');
            const voteForm = document.getElementById('voteForm');
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            
            if (confirmVoteBtn) {
                confirmVoteBtn.addEventListener('click', function() {
                    const selectedCandidate = document.querySelector('input[name="candidato_id"]:checked');
                    
                    if (selectedCandidate) {
                        confirmModal.show();
                    } else {
                        alert('Por favor, selecciona un candidato para votar.');
                    }
                });
            }
            
            if (submitVoteBtn) {
                submitVoteBtn.addEventListener('click', function() {
                    voteForm.submit();
                });
            }
        });
    </script>
</body>
</html>