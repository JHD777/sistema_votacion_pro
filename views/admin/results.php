<?php
require_once __DIR__ . '/../../controllers/ElectionController.php';
require_once __DIR__ . '/../../controllers/VoteController.php';
require_once __DIR__ . '/../../helpers/AuthHelper.php';

// Verificar si el usuario es administrador
AuthHelper::verificarAdmin();

$electionController = new ElectionController();
$voteController = new VoteController();

// Obtener elección seleccionada (ahora desde POST si el formulario fue enviado)
$eleccion_id = isset($_POST['election']) ? $_POST['election'] : (isset($_GET['election']) ? $_GET['election'] : null);
$eleccion = null;

// --- Debugging temporal ---
error_log("Debug: views/admin/results.php - \$eleccion_id: " . print_r($eleccion_id, true));
// --------------------------

if ($eleccion_id) {
    $eleccion = $electionController->getElectionById($eleccion_id);
    
    // --- Debugging temporal ---
    error_log("Debug: views/admin/results.php - \$eleccion: " . print_r($eleccion, true));
    // --------------------------

    if (!$eleccion) {
        header('Location: ' . BASE_URL . '/admin/elections');
        exit;
    }
    
    // Obtener resultados de la elección
    $resultados = $voteController->getElectionResults($eleccion_id);
    
    // --- Debugging temporal ---
    error_log("Debug: views/admin/results.php - \$resultados: " . print_r($resultados, true));
    // --------------------------
    
    // Calcular porcentajes y preparar datos para gráficos
    $total_votos = 0;
    $labels = [];
    $data = [];
    $colors = [];
    
    foreach ($resultados as $resultado) {
        $total_votos += $resultado['votos'];
    }
    
    // Definir una paleta de colores predefinida para mejor contraste
    $predefinedColors = [
        'rgba(54, 162, 235, 0.8)',
        'rgba(255, 99, 132, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(153, 102, 255, 0.8)',
        'rgba(255, 205, 86, 0.8)',
        'rgba(201, 203, 207, 0.8)',
        'rgba(255, 99, 71, 0.8)',
        'rgba(50, 205, 50, 0.8)',
        'rgba(138, 43, 226, 0.8)'
    ];
    
    foreach ($resultados as &$resultado) {
        $resultado['porcentaje'] = $total_votos > 0 ? round(($resultado['votos'] / $total_votos) * 100, 2) : 0;
        
        // Datos para gráficos
        $labels[] = $resultado['nombre'] . ' ' . $resultado['apellido'];
        $data[] = $resultado['votos'];
        
        // Usar color de la paleta predefinida o generar uno si se acaban
        $colorIndex = count($colors) % count($predefinedColors);
        $colors[] = $predefinedColors[$colorIndex];
    }
    
    // Obtener estadísticas de participación
    $stats = $voteController->getElectionParticipationStats($eleccion_id);
}

// Obtener todas las elecciones para el selector
$elecciones = $electionController->getAllElections();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Votación - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-bar-chart"></i> Resultados de Votación</h1>
                <?php if ($eleccion): ?>
                    <p class="lead">Resultados para la elección: <strong><?php echo htmlspecialchars($eleccion['titulo']); ?></strong></p>
                <?php else: ?>
                    <p class="lead">Selecciona una elección para ver sus resultados</p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </div>
        
        <!-- Selector de elección -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-filter"></i> Seleccionar Elección</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label for="election" class="form-label">Elección:</label>
                            <select name="election" id="election" class="form-select" required>
                                <option value="">Selecciona una elección</option>
                                <?php foreach ($elecciones as $e): ?>
                                    <option value="<?php echo $e['id']; ?>" <?php echo ($eleccion_id == $e['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($e['titulo']); ?> 
                                        (<?php echo date('d/m/Y', strtotime($e['fecha_inicio'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($e['fecha_fin'])); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Ver Resultados
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if ($eleccion && !empty($resultados)): ?>
            <!-- Resumen de participación -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-people"></i> Resumen de Participación</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h5>Total de Votantes</h5>
                                    <h2 class="display-4"><?php echo $stats['total_usuarios_verificados']; ?></h2>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5>Votos Emitidos</h5>
                                    <h2 class="display-4"><?php echo $stats['votos_emitidos']; ?></h2>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5>Participación</h5>
                                    <h2 class="display-4"><?php echo $stats['porcentaje_participacion']; ?>%</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de resultados -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Gráfico de Resultados</h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 400px;">
                                <canvas id="resultsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de resultados -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-table"></i> Tabla de Resultados</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Candidato</th>
                                            <th class="text-center">Votos</th>
                                            <th class="text-center">Porcentaje</th>
                                            <th class="text-center">Gráfico</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $resultado): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($resultado['foto'])): ?>
                                                            <img src="<?php echo BASE_URL . '/uploads/candidates/' . $resultado['foto']; ?>" 
                                                                alt="<?php echo htmlspecialchars($resultado['nombre'] . ' ' . $resultado['apellido']); ?>" 
                                                                class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center" 
                                                                style="width: 40px; height: 40px; color: white;">
                                                                <i class="bi bi-person"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($resultado['nombre'] . ' ' . $resultado['apellido']); ?></strong><br>
                                                            <?php // <small class="text-muted"><?php echo htmlspecialchars($resultado['partido']); ?></small> // Columna partido no seleccionada actualmente ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center"><?php echo $resultado['votos']; ?></td>
                                                <td class="text-center"><?php echo $resultado['porcentaje']; ?>%</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                            style="width: <?php echo $resultado['porcentaje']; ?>%; background-color: <?php echo str_replace('0.8', '1', $colors[array_search($resultado['nombre'] . ' ' . $resultado['apellido'], $labels)]); ?>;" 
                                                            aria-valuenow="<?php echo $resultado['porcentaje']; ?>" aria-valuemin="0" aria-valuemax="100">
                                                            <?php echo $resultado['porcentaje']; ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-dark">
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center"><?php echo $total_votos; ?></th>
                                            <th class="text-center">100%</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Opciones de exportación -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-download"></i> Exportar Resultados</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <a href="<?php echo BASE_URL; ?>/export/results?election=<?php echo $eleccion_id; ?>&format=pdf" class="btn btn-danger w-100">
                                        <i class="bi bi-file-pdf"></i> Exportar como PDF
                                    </a>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <a href="<?php echo BASE_URL; ?>/export/results?election=<?php echo $eleccion_id; ?>&format=excel" class="btn btn-success w-100">
                                        <i class="bi bi-file-excel"></i> Exportar como Excel
                                    </a>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <a href="<?php echo BASE_URL; ?>/export/results?election=<?php echo $eleccion_id; ?>&format=csv" class="btn btn-primary w-100">
                                        <i class="bi bi-file-text"></i> Exportar como CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Script para el gráfico -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('resultsChart').getContext('2d');
                    const resultsChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($labels); ?>,
                            datasets: [{
                                label: 'Votos',
                                data: <?php echo json_encode($data); ?>,
                                backgroundColor: <?php echo json_encode($colors); ?>,
                                borderColor: <?php echo json_encode(array_map(function($color) {
                                    return str_replace('0.8', '1', $color);
                                }, $colors)); ?>,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += context.parsed.y;
                                            label += ' (' + (context.parsed.y / <?php echo $total_votos ?: 1; ?> * 100).toFixed(2) + '%)';
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        <?php elseif ($eleccion && empty($resultados)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No hay resultados disponibles para esta elección.
            </div>
        <?php endif; ?>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>