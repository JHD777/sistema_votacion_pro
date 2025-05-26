<?php
require_once __DIR__ . '/../../controllers/ElectionController.php';
require_once __DIR__ . '/../../helpers/AuthHelper.php';

// Verificar si el usuario es administrador
AuthHelper::verificarAdmin();

$electionController = new ElectionController();

// Procesar formulario de creación de elección
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'titulo' => $_POST['titulo'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? '',
        'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
        'fecha_fin' => $_POST['fecha_fin'] ?? '',
        'activo' => isset($_POST['activo']) ? 1 : 0
    ];

    $result = $electionController->createElection($data);

    $mensaje = $result['message'];
    $tipo_mensaje = $result['success'] ? 'success' : 'danger';

    // Si la creación fue exitosa, puedes redirigir o limpiar el formulario
    // For now, just display the message on the same page
    if ($result['success']) {
        // Optional: Clear form fields after successful creation
        $_POST = []; // Clear post data to reset form
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nueva Elección - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Flatpickr para selector de fecha/hora -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-plus-circle"></i> Añadir Nueva Elección</h1>
                <p class="lead">Completa los detalles para crear una nueva elección.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo BASE_URL; ?>?view=admin/elections" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Gestión de Elecciones
                </a>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulario para crear la elección -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Detalles de la Elección</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="titulo" class="form-label">Título:</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                                <label class="form-check-label" for="activo">
                                    Elección activa
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio:</label>
                            <input type="text" class="form-control datepicker" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de fin:</label>
                            <input type="text" class="form-control datepicker" id="fecha_fin" name="fecha_fin" required>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Crear Elección
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        });
    </script>
</body>
</html> 