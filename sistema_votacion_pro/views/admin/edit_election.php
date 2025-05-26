<?php
require_once __DIR__ . '/../../controllers/ElectionController.php';
require_once __DIR__ . '/../../controllers/CandidatoController.php'; // Necesitaremos este controlador

// Verificar si el usuario es administrador y obtener el ID de la elección
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin' || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard'); // Redirigir si no es admin o falta ID
    exit;
}

$electionId = $_GET['id'];
$electionController = new ElectionController();
$candidatoController = new CandidatoController();

// Obtener datos de la elección a editar
$eleccion_editar = $electionController->getElectionById($electionId);

// Si la elección no existe, redirigir
if (!$eleccion_editar) {
    header('Location: ' . BASE_URL . '/admin/elections?msg=notfound');
    exit;
}

// Procesar acciones (guardar cambios de la elección, añadir/editar/eliminar candidatos)
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para procesar el guardado de la elección principal
    if (isset($_POST['action']) && $_POST['action'] === 'update_election') {
         $data = [
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        $result = $electionController->updateElection($electionId, $data);
        $mensaje = $result['message'];
        $tipo_mensaje = $result['success'] ? 'success' : 'danger';

        // No redirigimos inmediatamente para poder mostrar mensajes en la misma página
        // Después de procesar, volvemos a obtener los datos actualizados de la elección
        $eleccion_editar = $electionController->getElectionById($electionId);

    }
    
    // Lógica para añadir/editar/eliminar candidatos

    // Procesar añadir candidato
    if (isset($_POST['action']) && $_POST['action'] === 'add_candidate') {
         // Recopilar datos del formulario del modal
         $candidateData = [
             'eleccion_id' => $electionId, // Ya tenemos el ID de la elección de la URL
             'nombre' => $_POST['nombre'] ?? '',
             'descripcion' => $_POST['descripcion'] ?? '',
             // La foto se maneja en el controlador debido al archivo
         ];

         // Pasar también los datos del archivo subido
         $fileData = $_FILES['foto'] ?? null;

         // Llamar al controlador de candidatos para añadir
         $result = $candidatoController->addCandidate($candidateData, $fileData);
         
         $mensaje = $result['message'];
         $tipo_mensaje = $result['success'] ? 'success' : 'danger';

         // Después de procesar, recargar los candidatos para mostrar la tabla actualizada
         $candidatos = $candidatoController->getCandidatosByElectionId($electionId);
    }
     // Ejemplo: Editar candidato (esto es solo un placeholder)
    if (isset($_POST['action']) && $_POST['action'] === 'edit_candidate') {
        // ... lógica para editar candidato ...
        $mensaje = "Lógica para editar candidato (placeholder).";
        $tipo_mensaje = "info";
    }
      // Ejemplo: Eliminar candidato (esto es solo un placeholder)
    if (isset($_POST['action']) && $_POST['action'] === 'delete_candidate') {
        // ... lógica para eliminar candidato ...
        $mensaje = "Lógica para eliminar candidato (placeholder).";
        $tipo_mensaje = "info";
    }
}

// Obtener los candidatos asociados a esta elección
$candidatos = $candidatoController->getCandidatosByElectionId($electionId);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Elección - Sistema de Votación</title>
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
                <h1><i class="bi bi-pencil"></i> Editar Elección: <?php echo htmlspecialchars($eleccion_editar['titulo']); ?></h1>
                <p class="lead">Configura los detalles y candidatos para esta elección.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo BASE_URL; ?>/admin/elections" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Elecciones
                </a>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulario para editar los detalles de la elección -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Detalles de la Elección</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_election">
                    <input type="hidden" name="id" value="<?php echo $eleccion_editar['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="titulo" class="form-label">Título:</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required
                                   value="<?php echo htmlspecialchars($eleccion_editar['titulo']); ?>">
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                       <?php echo ($eleccion_editar['activo'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activo">
                                    Elección activa
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($eleccion_editar['descripcion']); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio:</label>
                            <input type="text" class="form-control datepicker" id="fecha_inicio" name="fecha_inicio" required
                                   value="<?php echo date('Y-m-d H:i', strtotime($eleccion_editar['fecha_inicio'])); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de fin:</label>
                            <input type="text" class="form-control datepicker" id="fecha_fin" name="fecha_fin" required
                                   value="<?php echo date('Y-m-d H:i', strtotime($eleccion_editar['fecha_fin'])); ?>">
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Actualizar Elección
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Sección para gestionar candidatos -->
        <div class="card mb-4">
             <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Candidatos de la Elección</h5>
            </div>
            <div class="card-body">
                <!-- Botón para añadir nuevo candidato -->
                <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
                    <i class="bi bi-plus-circle"></i> Añadir Nuevo Candidato
                </button>

                <!-- Tabla de candidatos -->
                <div class="table-responsive">
                    <table id="candidatesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Foto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                // Aquí iteraremos sobre los candidatos de esta elección
                                if (!empty($candidatos)): 
                                    foreach ($candidatos as $candidato): 
                            ?>
                                <tr>
                                    <td><?php echo $candidato['id']; ?></td>
                                    <td><?php echo htmlspecialchars($candidato['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($candidato['descripcion']); ?></td>
                                    <td>
                                        <?php if (!empty($candidato['foto_url'])): ?>
                                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($candidato['foto_url']); ?>" alt="Foto de <?php echo htmlspecialchars($candidato['nombre']); ?>" style="width: 50px; height: auto;">
                                        <?php else: ?>
                                            Sin foto
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Botones de acción (Editar y Eliminar) -->
                                        <button type="button" class="btn btn-sm btn-primary edit-candidate-btn" data-bs-toggle="modal" data-bs-target="#editCandidateModal" 
                                                data-id="<?php echo $candidato['id']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($candidato['nombre']); ?>"
                                                data-descripcion="<?php echo htmlspecialchars($candidato['descripcion']); ?>"
                                                data-foto-url="<?php echo htmlspecialchars($candidato['foto_url']); ?>"
                                                title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-candidate-btn" data-bs-toggle="modal" data-bs-target="#deleteCandidateModal" 
                                                data-id="<?php echo $candidato['id']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($candidato['nombre']); ?>"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                                    endforeach; 
                                else: 
                            ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay candidatos registrados para esta elección.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal para añadir nuevo candidato -->
        <div class="modal fade" id="addCandidateModal" tabindex="-1" aria-labelledby="addCandidateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCandidateModalLabel">Añadir Nuevo Candidato a esta Elección</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addCandidateForm" method="POST" action="" enctype="multipart/form-data">
                         <input type="hidden" name="action" value="add_candidate">
                         <input type="hidden" name="eleccion_id" value="<?php echo $electionId; ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="candidate_nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="candidate_nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="candidate_descripcion" class="form-label">Descripción:</label>
                                <textarea class="form-control" id="candidate_descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                             <div class="mb-3">
                                <label for="candidate_foto" class="form-label">Foto:</label>
                                <input type="file" class="form-control" id="candidate_foto" name="foto">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Candidato</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para editar candidato (lo añadiremos más tarde) -->
        <div class="modal fade" id="editCandidateModal" tabindex="-1" aria-labelledby="editCandidateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCandidateModalLabel">Editar Candidato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <?php // Formulario de edición de candidato irá aquí ?>
                     <div class="modal-body">
                         <p>Formulario de edición de candidato (placeholder).</p>
                     </div>
                      <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                </div>
            </div>
        </div>

         <!-- Modal para eliminar candidato -->
        <div class="modal fade" id="deleteCandidateModal" tabindex="-1" aria-labelledby="deleteCandidateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCandidateModalLabel">Confirmar Eliminación de Candidato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                     <form id="deleteCandidateForm" method="POST" action="">
                         <input type="hidden" name="action" value="delete_candidate">
                         <input type="hidden" name="id" id="delete-candidate-id">
                          <input type="hidden" name="eleccion_id" value="<?php echo $electionId; ?>">
                        <div class="modal-body">
                            <p>¿Estás seguro de que deseas eliminar al candidato <span id="delete-candidate-name"></span>?</p>
                            <p class="text-danger">Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar Candidato</button>
                        </div>
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
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script> <!-- Opcional: localización -->

    <script>
        $(document).ready(function() {
             // Inicializar Flatpickr para los selectores de fecha/hora
            flatpickr(".datepicker", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:s", // Asegúrate de que el formato coincida con el de tu DB
                locale: "es" // Usar localización en español
            });

            // Manejar la apertura del modal de eliminación de candidato
            $('.delete-candidate-btn').click(function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                
                $('#delete-candidate-id').val(id);
                $('#delete-candidate-name').text(nombre);
            });
        });
    </script>
</body>
</html> 