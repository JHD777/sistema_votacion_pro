<?php
// Verificar que sea un administrador supremo
if (!isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] !== 'super') {
    header('Location: ' . BASE_URL . '?view=admin/login');
    exit;
}

require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/SettingsController.php';
$adminController = new AdminController();
$settingsController = new SettingsController();

// Procesar formularios
$mensaje = '';
$tipo_mensaje = '';

// 1. Procesar creación de administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'crear_admin':
            if (isset($_POST['nombre'], $_POST['apellido'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['rol'])) {
                $result = $adminController->createAdmin([
                    'nombre' => $_POST['nombre'],
                    'apellido' => $_POST['apellido'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'username' => $_POST['username'],
                    'rol_id' => $_POST['rol']
                ]);
                
                $mensaje = $result['message'];
                $tipo_mensaje = $result['success'] ? 'success' : 'danger';
            }
            break;
            
        case 'actualizar_config':
            if (isset($_POST['nombre_sistema'], $_POST['color_primario'], $_POST['color_secundario'], $_POST['color_texto'], $_POST['footer_texto'])) {
                // Actualizar configuración usando SettingsController
                $settings = [
                    'site_title' => $_POST['nombre_sistema'],
                    'primary_color' => $_POST['color_primario'],
                    'secondary_color' => $_POST['color_secundario'],
                    'text_color' => $_POST['color_texto'],
                    'footer_text' => $_POST['footer_texto'],
                    'accent_color' => isset($_POST['color_acento']) ? $_POST['color_acento'] : '#ffc107'
                ];
                
                $result = $settingsController->updateGeneralSettings($settings);
                
                $mensaje = $result['message'];
                $tipo_mensaje = $result['success'] ? 'success' : 'danger';
            }
            break;
    }
}

// Obtener la configuración actual del sistema
$config = $settingsController->getAllSettings();

// Obtener roles de administrador
$roles = $adminController->getAdminRoles();

// Obtener lista de administradores
$result = $adminController->getAllAdmins();
$admins = $result['success'] ? $result['admins'] : [];

// Determinar qué pestaña debe estar activa
$activeTab = 'crear-admin'; // Pestaña por defecto
if (isset($_GET['tab'])) {
    $activeTab = $_GET['tab'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador Supremo - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Color picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/dynamic-styles.php">
    <style>
        .color-preview {
            width: 30px;
            height: 30px;
            display: inline-block;
            border: 1px solid #ccc;
            vertical-align: middle;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-shield-lock-fill"></i> Panel Supremo</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="panel-tabs" role="tablist">
                            <a href="#crear-admin" class="list-group-item list-group-item-action <?php echo $activeTab === 'crear-admin' ? 'active' : ''; ?>" data-bs-toggle="tab" role="tab" aria-controls="crear-admin" aria-selected="<?php echo $activeTab === 'crear-admin' ? 'true' : 'false'; ?>">
                                <i class="bi bi-person-plus-fill"></i> Crear Administradores
                            </a>
                            <a href="#config-sistema" class="list-group-item list-group-item-action <?php echo $activeTab === 'config-sistema' ? 'active' : ''; ?>" data-bs-toggle="tab" role="tab" aria-controls="config-sistema" aria-selected="<?php echo $activeTab === 'config-sistema' ? 'true' : 'false'; ?>">
                                <i class="bi bi-palette-fill"></i> Configuración Visual
                            </a>
                            <a href="#lista-admins" class="list-group-item list-group-item-action <?php echo $activeTab === 'lista-admins' ? 'active' : ''; ?>" data-bs-toggle="tab" role="tab" aria-controls="lista-admins" aria-selected="<?php echo $activeTab === 'lista-admins' ? 'true' : 'false'; ?>">
                                <i class="bi bi-people-fill"></i> Administradores
                            </a>
                            <a href="<?php echo BASE_URL; ?>?view=admin/dashboard" class="list-group-item list-group-item-action">
                                <i class="bi bi-speedometer2"></i> Panel Normal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="tab-content">
                    <!-- Crear Administrador -->
                    <div class="tab-pane fade <?php echo $activeTab === 'crear-admin' ? 'show active' : ''; ?>" id="crear-admin" role="tabpanel" aria-labelledby="crear-admin-tab">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-plus-fill"></i> Crear Nuevo Administrador</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="crear_admin">
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="apellido" class="form-label">Apellido</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Nombre de Usuario</label>
                                            <input type="text" class="form-control" id="username" name="username" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Correo Electrónico</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="rol" class="form-label">Rol</label>
                                            <select class="form-select" id="rol" name="rol" required>
                                                <?php if (isset($roles) && is_array($roles)): ?>
                                                    <?php foreach ($roles as $rol): ?>
                                                        <?php if (isset($rol['id']) && $rol['id'] != 1): // Excluir rol de super admin ?>
                                                            <option value="<?php echo $rol['id']; ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="2">Administrador</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Guardar Administrador
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración del Sistema -->
                    <div class="tab-pane fade <?php echo $activeTab === 'config-sistema' ? 'show active' : ''; ?>" id="config-sistema" role="tabpanel" aria-labelledby="config-sistema-tab">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-palette-fill"></i> Configuración Visual del Sistema</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="actualizar_config">
                                    
                                    <div class="mb-3">
                                        <label for="nombre_sistema" class="form-label">Nombre del Sistema</label>
                                        <input type="text" class="form-control" id="nombre_sistema" name="nombre_sistema" value="<?php echo isset($config['site_title']) ? htmlspecialchars($config['site_title']) : 'Sistema de Votación'; ?>" required>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="color_primario" class="form-label">Color Primario</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <div class="color-preview" id="preview_primario" style="background-color: <?php echo isset($config['primary_color']) ? $config['primary_color'] : '#0d6efd'; ?>"></div>
                                                </span>
                                                <input type="text" class="form-control" id="color_primario" name="color_primario" value="<?php echo isset($config['primary_color']) ? $config['primary_color'] : '#0d6efd'; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="color_secundario" class="form-label">Color Secundario</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <div class="color-preview" id="preview_secundario" style="background-color: <?php echo isset($config['secondary_color']) ? $config['secondary_color'] : '#6c757d'; ?>"></div>
                                                </span>
                                                <input type="text" class="form-control" id="color_secundario" name="color_secundario" value="<?php echo isset($config['secondary_color']) ? $config['secondary_color'] : '#6c757d'; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="color_acento" class="form-label">Color de Acento</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <div class="color-preview" id="preview_acento" style="background-color: <?php echo isset($config['accent_color']) ? $config['accent_color'] : '#ffc107'; ?>"></div>
                                                </span>
                                                <input type="text" class="form-control" id="color_acento" name="color_acento" value="<?php echo isset($config['accent_color']) ? $config['accent_color'] : '#ffc107'; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="color_texto" class="form-label">Color de Texto</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <div class="color-preview" id="preview_texto" style="background-color: <?php echo isset($config['text_color']) ? $config['text_color'] : '#212529'; ?>"></div>
                                                </span>
                                                <input type="text" class="form-control" id="color_texto" name="color_texto" value="<?php echo isset($config['text_color']) ? $config['text_color'] : '#212529'; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="color_fondo" class="form-label">Color de Fondo</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <div class="color-preview" id="preview_fondo" style="background-color: <?php echo isset($config['background_color']) ? $config['background_color'] : '#ffffff'; ?>"></div>
                                                </span>
                                                <input type="text" class="form-control" id="color_fondo" name="color_fondo" value="<?php echo isset($config['background_color']) ? $config['background_color'] : '#ffffff'; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="footer_texto" class="form-label">Texto del Pie de Página</label>
                                        <input type="text" class="form-control" id="footer_texto" name="footer_texto" value="<?php echo isset($config['footer_text']) ? htmlspecialchars($config['footer_text']) : '© ' . date('Y') . ' Sistema de Votación. Todos los derechos reservados.'; ?>" required>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Guardar Configuración
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Administradores -->
                    <div class="tab-pane fade <?php echo $activeTab === 'lista-admins' ? 'show active' : ''; ?>" id="lista-admins" role="tabpanel" aria-labelledby="lista-admins-tab">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-people-fill"></i> Administradores del Sistema</h5>
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
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($admins)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No hay administradores registrados</td>
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
                                <div class="mt-3">
                                    <a href="<?php echo BASE_URL; ?>?view=admin/admins" class="btn btn-primary">
                                        <i class="bi bi-gear"></i> Gestión Avanzada de Administradores
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts de Bootstrap y funcionalidad -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    <script>
        // Activar la primera pestaña por defecto
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar la primera pestaña por defecto
            const firstTabLink = document.querySelector('.list-group-item-action[data-bs-toggle="tab"]');
            const firstTabId = firstTabLink.getAttribute('href');
            const firstTab = document.querySelector(firstTabId);
            
            firstTab.classList.add('show', 'active');
            firstTabLink.classList.add('active');
            
            // Inicializar los selectores de color
            const colorInputs = document.querySelectorAll('.color-picker');
            colorInputs.forEach(input => {
                const pickr = Pickr.create({
                    el: input,
                    theme: 'classic',
                    default: input.dataset.color || '#000000',
                    components: {
                        preview: true,
                        opacity: true,
                        hue: true,
                        interaction: {
                            hex: true,
                            rgba: true,
                            hsla: false,
                            hsva: false,
                            cmyk: false,
                            input: true,
                            clear: false,
                            save: true
                        }
                    }
                });
                
                // Actualizar el input oculto cuando cambie el color
                pickr.on('save', (color) => {
                    const hexColor = color.toHEXA().toString();
                    const targetInput = document.getElementById(input.dataset.target);
                    if (targetInput) {
                        targetInput.value = hexColor;
                        // Actualizar la vista previa
                        const preview = document.querySelector(`[data-preview="${input.dataset.target}"]`);
                        if (preview) {
                            preview.style.backgroundColor = hexColor;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>