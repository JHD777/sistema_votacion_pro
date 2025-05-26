<?php

use Controllers\AdminController;

require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../helpers/AuthHelper.php';

// Verificar si el usuario es administrador
AuthHelper::verificarAdmin();

$adminController = new AdminController();

// Procesar acciones
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $action = $_POST['action'];
        $user_id = (int)$_POST['user_id'];
        
        switch ($action) {
            case 'verify':
                $result = $adminController->verifyUser($user_id);
                $mensaje = $result['message'];
                $tipo_mensaje = $result['success'] ? 'success' : 'danger';
                break;
            case 'activate':
                $result = $adminController->activateUser($user_id);
                $mensaje = $result['message'];
                $tipo_mensaje = $result['success'] ? 'success' : 'danger';
                break;
            case 'deactivate':
                $result = $adminController->deactivateUser($user_id);
                $mensaje = $result['message'];
                $tipo_mensaje = $result['success'] ? 'success' : 'danger';
                break;
            default:
                // Acción no válida
                $mensaje = 'Acción no válida';
                $tipo_mensaje = 'danger';
                break;
        }
    }
}

// Obtener todos los usuarios
$users_data = $adminController->getAllUsers();
$users = isset($users_data['users']) ? $users_data['users'] : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(BASE_URL); ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-people"></i> Gestión de Usuarios</h1>
                <p class="lead">Administra los usuarios registrados en el sistema</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo htmlspecialchars(BASE_URL); ?>/admin/dashboard" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensaje); ?>" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Lista de Usuarios</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Documento</th>
                                <th>Estado</th>
                                <th>Registro</th>
                                <th>Último Login</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo (int)$user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['documento_identidad']); ?></td>
                                        <td>
                                            <?php if (isset($user['verificado']) && $user['verificado'] == 1): ?>
                                                <span class="badge bg-success">Verificado</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pendiente</span>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($user['activo']) && $user['activo'] == 0): ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo isset($user['fecha_registro']) ? date('d/m/Y', strtotime($user['fecha_registro'])) : 'N/A'; ?></td>
                                        <td>
                                            <?php if (isset($user['ultimo_login']) && $user['ultimo_login']): ?>
                                                <?php echo date('d/m/Y H:i', strtotime($user['ultimo_login'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Nunca</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($user['verificado']) && $user['verificado'] == 0): ?>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="action" value="verify">
                                                    <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Verificar usuario">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($user['activo']) && $user['activo'] == 1): ?>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="action" value="deactivate">
                                                    <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Desactivar usuario">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            <?php elseif (isset($user['activo']) && $user['activo'] == 0): ?>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="action" value="activate">
                                                    <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary" title="Activar usuario">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <button type="button" class="btn btn-sm btn-info view-user-btn" data-bs-toggle="modal" data-bs-target="#userDetailModal" 
                                                    data-id="<?php echo (int)$user['id']; ?>"
                                                    data-nombre="<?php echo htmlspecialchars($user['nombre']); ?>"
                                                    data-apellido="<?php echo htmlspecialchars($user['apellido']); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                    data-documento="<?php echo htmlspecialchars($user['documento_identidad']); ?>"
                                                    data-registro="<?php echo isset($user['fecha_registro']) ? date('d/m/Y', strtotime($user['fecha_registro'])) : 'N/A'; ?>"
                                                    title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay usuarios registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de detalles de usuario -->
    <div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userDetailModalLabel">Detalles del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">Nombre completo:</label>
                        <p id="modal-nombre"></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Email:</label>
                        <p id="modal-email"></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Documento de identidad:</label>
                        <p id="modal-documento"></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Fecha de registro:</label>
                        <p id="modal-registro"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[5, 'desc']] // Ordenar por fecha de registro (descendente)
            });
            
            // Manejar modal de detalles
            $('.view-user-btn').click(function() {
                const nombre = $(this).data('nombre') + ' ' + $(this).data('apellido');
                const email = $(this).data('email');
                const documento = $(this).data('documento');
                const registro = $(this).data('registro');
                
                $('#modal-nombre').text(nombre);
                $('#modal-email').text(email);
                $('#modal-documento').text(documento);
                $('#modal-registro').text(registro);
            });
        });
    </script>
</body>
</html>