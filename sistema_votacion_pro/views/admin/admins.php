<?php

use Controllers\AdminController;

require_once __DIR__ . '/../../controllers/AdminController.php';

// Verificar si el usuario es administrador supremo
if (!isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] !== 'super') {
    header('Location: ' . BASE_URL . '?view=admin/dashboard');
    exit;
}

$adminController = new AdminController();

// Procesar formulario de creación/edición
$mensaje = '';
$tipo_mensaje = '';
$admin_editar = null;

// Eliminar administrador
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $result = $adminController->deleteAdmin($_GET['delete']);
    $mensaje = $result['message'];
    $tipo_mensaje = $result['success'] ? 'success' : 'danger';
}

// Editar administrador
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $result = $adminController->getAllAdmins();
    if ($result['success']) {
        foreach ($result['admins'] as $admin) {
            if ($admin['id'] == $_GET['edit']) {
                $admin_editar = $admin;
                break;
            }
        }
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Crear administrador
        if ($_POST['action'] === 'create') {
            $result = $adminController->createAdmin([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'username' => $_POST['username'], // Usar el username proporcionado
                'rol' => 2 // Cambiado de 'normal' a 2
            ]);
            $mensaje = $result['message'];
            $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        }
        
        // Actualizar administrador
        if ($_POST['action'] === 'update' && isset($_POST['admin_id'])) {
            $result = $adminController->updateAdmin($_POST['admin_id'], [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            $mensaje = $result['message'];
            $tipo_mensaje = $result['success'] ? 'success' : 'danger';
            
            // Si se envió una nueva contraseña
            if (!empty($_POST['password'])) {
                $result = $adminController->changeAdminPassword($_POST['admin_id'], $_POST['password']);
                $mensaje .= '. ' . $result['message'];
            }
        }
    }
}

// Obtener todos los administradores
$result = $adminController->getAllAdmins();
$admins = $result['success'] ? $result['admins'] : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Administradores - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/dynamic-styles.php">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-people-fill"></i> Gestión de Administradores</h1>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearAdmin">
                    <i class="bi bi-person-plus"></i> Nuevo Administrador
                </button>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Lista de Administradores</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($admins)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay administradores registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td><?php echo $admin['id']; ?></td>
                                        <td><?php echo htmlspecialchars($admin['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $admin['rol'] === 'super' ? 'danger' : 'primary'; ?>">
                                                <?php echo $admin['rol'] === 'super' ? 'Supremo' : 'Normal'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $admin['activo'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $admin['activo'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($admin['fecha_creacion'])); ?></td>
                                        <td>
                                            <?php if ($admin['rol'] !== 'super'): ?>
                                                <a href="<?php echo BASE_URL; ?>?view=admin/admins&edit=<?php echo $admin['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>?view=admin/admins&delete=<?php echo $admin['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este administrador?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No editable</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Crear Administrador -->
    <div class="modal fade" id="modalCrearAdmin" tabindex="-1" aria-labelledby="modalCrearAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCrearAdminLabel"><i class="bi bi-person-plus"></i> Crear Nuevo Administrador</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Administrador -->
    <?php if ($admin_editar): ?>
    <div class="modal fade" id="modalEditarAdmin" tabindex="-1" aria-labelledby="modalEditarAdminLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalEditarAdminLabel"><i class="bi bi-pencil"></i> Editar Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="admin_id" value="<?php echo $admin_editar['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="nombre_edit" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre_edit" name="nombre" value="<?php echo htmlspecialchars($admin_editar['nombre']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="apellido_edit" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido_edit" name="apellido" value="<?php echo htmlspecialchars($admin_editar['apellido']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_edit" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email_edit" name="email" value="<?php echo htmlspecialchars($admin_editar['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username_edit" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username_edit" name="username" value="<?php echo htmlspecialchars($admin_editar['username']); ?>" readonly>
                            <small class="text-muted">El nombre de usuario no se puede cambiar</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_edit" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password_edit" name="password">
                            <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo" <?php echo $admin_editar['activo'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="activo">Administrador activo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="<?php echo BASE_URL; ?>?view=admin/admins" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Script para mostrar automáticamente el modal de edición -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($admin_editar): ?>
        var modalEditarAdmin = new bootstrap.Modal(document.getElementById('modalEditarAdmin'));
        modalEditarAdmin.show();
        <?php endif; ?>
    });
    </script>
    <?php endif; ?>
    
    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>