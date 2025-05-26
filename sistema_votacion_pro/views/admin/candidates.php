<?php
require_once __DIR__ . '/../../controllers/CandidateController.php';
require_once __DIR__ . '/../../controllers/ElectionController.php';
require_once __DIR__ . '/../../helpers/AuthHelper.php';

// Verificar si el usuario es administrador
AuthHelper::verificarAdmin();

$candidateController = new CandidateController();
$electionController = new ElectionController();

// Obtener elección seleccionada
$eleccion_id = isset($_GET['election']) ? $_GET['election'] : null;
$eleccion = null;
$error_eleccion = '';

if ($eleccion_id) {
    $eleccion = $electionController->getElectionById($eleccion_id);
    if (!$eleccion) {
        $error_eleccion = 'La elección seleccionada no existe.';
        // No redirigir, solo mostrar el error
    }
}

// Procesar acciones
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear candidato
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'biografia' => $_POST['biografia'],
            'eleccion_id' => $_POST['eleccion_id']
        ];
        
        // Procesar foto si se ha subido
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/';
            $temp_name = $_FILES['foto']['tmp_name'];
            $file_name = time() . '_' . $_FILES['foto']['name'];
            
            if (move_uploaded_file($temp_name, $upload_dir . $file_name)) {
                $data['foto'] = $file_name;
            }
        }
        
        $result = $candidateController->createCandidate($data);
        $mensaje = $result['message'];
        $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Redireccionar para evitar reenvío del formulario
            header('Location: ' . BASE_URL . '/admin/candidates?election=' . $_POST['eleccion_id'] . '&msg=created');
            exit;
        }
    }
    
    // Actualizar candidato
    if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id'])) {
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'biografia' => $_POST['biografia'],
            'eleccion_id' => $_POST['eleccion_id']
        ];
        
        // Procesar foto si se ha subido
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/';
            $temp_name = $_FILES['foto']['tmp_name'];
            $file_name = time() . '_' . $_FILES['foto']['name'];
            
            if (move_uploaded_file($temp_name, $upload_dir . $file_name)) {
                $data['foto'] = $file_name;
            }
        }
        
        $result = $candidateController->updateCandidate($_POST['id'], $data);
        $mensaje = $result['message'];
        $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Redireccionar para evitar reenvío del formulario
            header('Location: ' . BASE_URL . '/admin/candidates?election=' . $_POST['eleccion_id'] . '&msg=updated');
            exit;
        }
    }
    
    // Eliminar candidato
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $result = $candidateController->deleteCandidate($_POST['id']);
        $mensaje = $result['message'];
        $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Redireccionar para evitar reenvío del formulario
            header('Location: ' . BASE_URL . '/admin/candidates?election=' . $_POST['eleccion_id'] . '&msg=deleted');
            exit;
        }
    }
}

// Procesar mensajes de redirección
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'created':
            $mensaje = 'Candidato creado correctamente.';
            $tipo_mensaje = 'success';
            break;
        case 'updated':
            $mensaje = 'Candidato actualizado correctamente.';
            $tipo_mensaje = 'success';
            break;
        case 'deleted':
            $mensaje = 'Candidato eliminado correctamente.';
            $tipo_mensaje = 'success';
            break;
    }
}

// Obtener todas las elecciones para el selector
$elecciones = $electionController->getAllElections();

// Obtener candidatos de la elección seleccionada
$candidatos = [];
if ($eleccion_id) {
    $candidatos = $candidateController->getCandidatesByElection($eleccion_id);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Candidatos - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-person-badge"></i> Gestión de Candidatos</h1>
                <?php if ($eleccion): ?>
                    <p class="lead">Candidatos para la elección: <strong><?php echo htmlspecialchars($eleccion['titulo']); ?></strong></p>
                <?php else: ?>
                    <p class="lead">Selecciona una elección para gestionar sus candidatos</p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_eleccion)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_eleccion; ?>
            </div>
        <?php endif; ?>
        
        <!-- Selector de elección -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-filter"></i> Seleccionar Elección</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="">
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
                                <i class="bi bi-search"></i> Mostrar Candidatos
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if ($eleccion): ?>
            <!-- Formulario para agregar candidato -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Agregar Nuevo Candidato</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="eleccion_id" value="<?php echo $eleccion_id; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido:</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="biografia" class="form-label">Biografía:</label>
                            <textarea class="form-control" id="biografia" name="biografia" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto:</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB.</div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Candidato
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Lista de candidatos -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Lista de Candidatos</h5>
                </div>
                <div class="card-body">
                    <?php if (count($candidatos) > 0): ?>
                        <div class="row">
                            <?php foreach ($candidatos as $candidato): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($candidato['nombre'] . ' ' . $candidato['apellido']); ?></h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <?php if (!empty($candidato['foto'])): ?>
                                                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo $candidato['foto']; ?>" class="img-fluid mb-3" style="max-height: 150px;" alt="<?php echo htmlspecialchars($candidato['nombre']); ?>">
                                            <?php else: ?>
                                                <img src="<?php echo BASE_URL; ?>/assets/img/candidate-placeholder.png" class="img-fluid mb-3" style="max-height: 150px;" alt="<?php echo htmlspecialchars($candidato['nombre']); ?>">
                                            <?php endif; ?>
                                            
                                            <p class="card-text"><?php echo htmlspecialchars($candidato['biografia']); ?></p>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-sm btn-primary edit-candidate-btn" data-bs-toggle="modal" data-bs-target="#editCandidateModal" 
                                                        data-id="<?php echo $candidato['id']; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($candidato['nombre']); ?>"
                                                        data-apellido="<?php echo htmlspecialchars($candidato['apellido']); ?>"
                                                        data-biografia="<?php echo htmlspecialchars($candidato['biografia']); ?>"
                                                        data-foto="<?php echo htmlspecialchars($candidato['foto']); ?>">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-candidate-btn" data-bs-toggle="modal" data-bs-target="#deleteCandidateModal" 
                                                        data-id="<?php echo $candidato['id']; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($candidato['nombre'] . ' ' . $candidato['apellido']); ?>">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No hay candidatos registrados para esta elección.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal para editar candidato -->
    <div class="modal fade" id="editCandidateModal" tabindex="-1" aria-labelledby="editCandidateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCandidateModalLabel">Editar Candidato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit-id">
                        <input type="hidden" name="eleccion_id" value="<?php echo $eleccion_id; ?>">
                        
                        <div class="mb-3">
                            <label for="edit-nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-apellido" class="form-label">Apellido:</label>
                            <input type="text" class="form-control" id="edit-apellido" name="apellido" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-biografia" class="form-label">Biografía:</label>
                            <textarea class="form-control" id="edit-biografia" name="biografia" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-foto" class="form-label">Foto:</label>
                            <input type="file" class="form-control" id="edit-foto" name="foto" accept="image/*">
                            <div class="form-text">Deja este campo vacío si no deseas cambiar la foto actual.</div>
                            <div id="current-photo" class="mt-2 text-center"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para eliminar candidato -->
    <div class="modal fade" id="deleteCandidateModal" tabindex="-1" aria-labelledby="deleteCandidateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCandidateModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar al candidato <span id="delete-candidate-name"></span>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-id">
                        <input type="hidden" name="eleccion_id" value="<?php echo $eleccion_id; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar modal de edición
            const editButtons = document.querySelectorAll('.edit-candidate-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const apellido = this.getAttribute('data-apellido');
                    const biografia = this.getAttribute('data-biografia');
                    const foto = this.getAttribute('data-foto');
                    
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-nombre').value = nombre;
                    document.getElementById('edit-apellido').value = apellido;
                    document.getElementById('edit-biografia').value = biografia;
                    
                    const currentPhotoDiv = document.getElementById('current-photo');
                    if (foto) {
                        currentPhotoDiv.innerHTML = `<img src="${BASE_URL}/uploads/${foto}" class="img-thumbnail" style="max-height: 100px;" alt="Foto actual">`;
                    } else {
                        currentPhotoDiv.innerHTML = 'No hay foto actual';
                    }
                });
            });
            
            // Manejar modal de eliminación
            const deleteButtons = document.querySelectorAll('.delete-candidate-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    
                    document.getElementById('delete-id').value = id;
                    document.getElementById('delete-candidate-name').textContent = nombre;
                });
            });
        });
    </script>
</body>
</html>