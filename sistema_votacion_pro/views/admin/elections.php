<?php
require_once __DIR__ . '/../../controllers/ElectionController.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$electionController = new ElectionController();

// Procesar acciones
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear elección
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $data = [
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        $result = $electionController->createElection($data);
        $mensaje = $result['message'];
        $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Redireccionar para evitar reenvío del formulario
            header('Location: ' . BASE_URL . '/admin/elections?msg=created');
            exit;
        }
    }
    
    // Actualizar elección
    if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id'])) {
        $data = [
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        $result = $electionController->updateElection($_POST['id'], $data);
        $mensaje = $result['message'];
        $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Redireccionar para evitar reenvío del formulario
            header('Location: ' . BASE_URL . '/admin/elections?msg=updated');
            exit;
        }
    }
    
    // Eliminar elección
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $result = $electionController->deleteElection($_POST['id']);
        $mensaje = $result['message'];
        $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Redireccionar para evitar reenvío del formulario
            header('Location: ' . BASE_URL . '/admin/elections?msg=deleted');
            exit;
        }
    }
}

// Procesar mensajes de redirección
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'created':
            $mensaje = 'Elección creada correctamente.';
            $tipo_mensaje = 'success';
            break;
        case 'updated':
            $mensaje = 'Elección actualizada correctamente.';
            $tipo_mensaje = 'success';
            break;
        case 'deleted':
            $mensaje = 'Elección eliminada correctamente.';
            $tipo_mensaje = 'success';
            break;
    }
}

// Obtener todas las elecciones
$elecciones = $electionController->getAllElections();

// Obtener elección para editar (si se ha seleccionado)
$eleccion_editar = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $eleccion_editar = $electionController->getElectionById($_GET['edit']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Elecciones - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Flatpickr para selector de fecha/hora -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-calendar-event"></i> Gestión de Elecciones</h1>
                <p class="lead">Administra las elecciones del sistema</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Panel
                </a>
                <!-- Botón para añadir nueva elección -->
                <a href="<?php echo BASE_URL; ?>?view=admin/add_election" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Añadir Nueva Elección
                </a>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulario para crear/editar elección -->
        <?php // Este formulario se ha movido a views/admin/edit_election.php ?>
        
        <!-- Lista de elecciones -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Lista de Elecciones</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="electionsTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th>Candidatos</th>
                                <th>Acciones</th>
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
                                    <td><?php echo $eleccion['id']; ?></td>
                                    <td><?php echo htmlspecialchars($eleccion['titulo']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($eleccion['fecha_inicio'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($eleccion['fecha_fin'])); ?></td>
                                    <td><span class="badge <?php echo $badge_class; ?>"><?php echo $estado; ?></span></td>
                                    <td>
                                        <?php 
                                            // Obtener el número real de candidatos para esta elección
                                            $candidatos = $electionController->getCandidatesCount($eleccion['id']);
                                            if ($candidatos > 0) {
                                                echo '<span class="badge bg-info">' . $candidatos . ' candidato' . ($candidatos != 1 ? 's' : '') . '</span>';
                                            } else {
                                                echo '<span class="badge bg-warning">Sin candidatos</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>?view=admin/edit_election&id=<?php echo $eleccion['id']; ?>" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>?view=admin/candidates&election=<?php echo $eleccion['id']; ?>" class="btn btn-sm btn-success" title="Gestionar candidatos">
                                            <i class="bi bi-person-badge"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>?view=admin/results&election=<?php echo $eleccion['id']; ?>" class="btn btn-sm btn-info" title="Ver resultados">
                                            <i class="bi bi-bar-chart"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-election-btn" data-bs-toggle="modal" data-bs-target="#deleteElectionModal" 
                                                data-id="<?php echo $eleccion['id']; ?>"
                                                data-titulo="<?php echo htmlspecialchars($eleccion['titulo']); ?>"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para eliminar elección -->
    <div class="modal fade" id="deleteElectionModal" tabindex="-1" aria-labelledby="deleteElectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteElectionModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar la elección <span id="delete-election-title"></span>?</p>
                    <p class="text-danger">Esta acción eliminará todos los candidatos y votos asociados a esta elección. No se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-id">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#electionsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[2, 'desc']] // Ordenar por fecha de inicio (descendente)
            });
            
            // Inicializar Flatpickr (selector de fecha/hora)
            flatpickr(".datepicker", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                locale: "es",
                time_24hr: true
            });
            
            // Manejar modal de eliminación
            $('.delete-election-btn').click(function() {
                const id = $(this).data('id');
                const titulo = $(this).data('titulo');
                
                $('#delete-id').val(id);
                $('#delete-election-title').text(titulo);
            });
        });
    </script>
</body>
</html>