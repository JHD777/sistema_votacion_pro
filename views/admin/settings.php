<?php
require_once __DIR__ . '/../../controllers/SettingsController.php';

// Verificar si el usuario es administrador supremo
if (!isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] !== 'super') {
    header('Location: ' . BASE_URL . '?view=admin/dashboard');
    exit;
}

$settingsController = new SettingsController();

// Procesar formulario
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Actualizar configuración general
        if ($_POST['action'] === 'general') {
            $result = $settingsController->updateGeneralSettings([
                'site_title' => $_POST['site_title'],
                'site_description' => $_POST['site_description'],
                'contact_email' => $_POST['contact_email'],
                'welcome_text' => $_POST['welcome_text'],
                'footer_text' => $_POST['footer_text']
            ]);
            $mensaje = $result['message'];
            $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        }
        
        // Actualizar configuración de apariencia
        if ($_POST['action'] === 'appearance') {
            $result = $settingsController->updateGeneralSettings([
                'primary_color' => $_POST['primary_color'],
                'secondary_color' => $_POST['secondary_color'],
                'accent_color' => $_POST['accent_color'],
                'text_color' => $_POST['text_color'],
                'background_color' => $_POST['background_color']
            ]);
            $mensaje = $result['message'];
            $tipo_mensaje = $result['success'] ? 'success' : 'danger';
        }
    }
}

// Obtener configuraciones
$generalSettings = $settingsController->getSettingsByCategory('general');
$appearanceSettings = $settingsController->getSettingsByCategory('appearance');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sistema - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-gear-fill"></i> Configuración del Sistema</h1>
                <p class="lead">Personaliza la apariencia y configuración general del sistema de votación.</p>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-3">
                <div class="list-group mb-4">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="bi bi-gear"></i> General
                    </a>
                    <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-palette"></i> Apariencia
                    </a>
                    <a href="#admins" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-people"></i> Administradores
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Configuración General -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-gear"></i> Configuración General</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="general">
                                    
                                    <div class="mb-3">
                                        <label for="site_title" class="form-label">Título del Sitio</label>
                                        <input type="text" class="form-control" id="site_title" name="site_title" 
                                               value="<?php echo htmlspecialchars($generalSettings['site_title'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="site_description" class="form-label">Descripción del Sitio</label>
                                        <textarea class="form-control" id="site_description" name="site_description" rows="2"><?php echo htmlspecialchars($generalSettings['site_description'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Email de Contacto</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                               value="<?php echo htmlspecialchars($generalSettings['contact_email'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="welcome_text" class="form-label">Texto de Bienvenida</label>
                                        <textarea class="form-control" id="welcome_text" name="welcome_text" rows="3"><?php echo htmlspecialchars($generalSettings['welcome_text'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="footer_text" class="form-label">Texto del Pie de Página</label>
                                        <input type="text" class="form-control" id="footer_text" name="footer_text" 
                                               value="<?php echo htmlspecialchars($generalSettings['footer_text'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Guardar Configuración
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de Apariencia -->
                    <div class="tab-pane fade" id="appearance">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-palette"></i> Configuración de Apariencia</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="appearance">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="primary_color" class="form-label">Color Primario</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['primary_color'] ?? '#0d6efd'); ?>">
                                                <input type="text" class="form-control" id="primary_color_text" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['primary_color'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="secondary_color" class="form-label">Color Secundario</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['secondary_color'] ?? '#6c757d'); ?>">
                                                <input type="text" class="form-control" id="secondary_color_text" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['secondary_color'] ?? '#6c757d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="accent_color" class="form-label">Color de Acento</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="accent_color" name="accent_color" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['accent_color'] ?? '#ffc107'); ?>">
                                                <input type="text" class="form-control" id="accent_color_text" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['accent_color'] ?? '#ffc107'); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="text_color" class="form-label">Color de Texto</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="text_color" name="text_color" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['text_color'] ?? '#212529'); ?>">
                                                <input type="text" class="form-control" id="text_color_text" 
                                                       value="<?php echo htmlspecialchars($appearanceSettings['text_color'] ?? '#212529'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="background_color" class="form-label">Color de Fondo</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="background_color" name="background_color" 
                                                   value="<?php echo htmlspecialchars($appearanceSettings['background_color'] ?? '#ffffff'); ?>">
                                            <input type="text" class="form-control" id="background_color_text" 
                                                   value="<?php echo htmlspecialchars($appearanceSettings['background_color'] ?? '#ffffff'); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3">
                                        <div class="card-header">Vista Previa</div>
                                        <div class="card-body" id="preview" style="background-color: <?php echo htmlspecialchars($appearanceSettings['background_color'] ?? '#ffffff'); ?>; color: <?php echo htmlspecialchars($appearanceSettings['text_color'] ?? '#212529'); ?>;">
                                            <h5 class="card-title">Título de Ejemplo</h5>
                                            <p class="card-text">Este es un texto de ejemplo para ver cómo se verán los colores en el sistema.</p>
                                            <button class="btn" id="preview_primary" style="background-color: <?php echo htmlspecialchars($appearanceSettings['primary_color'] ?? '#0d6efd'); ?>; color: white;">Botón Primario</button>
                                            <button class="btn" id="preview_secondary" style="background-color: <?php echo htmlspecialchars($appearanceSettings['secondary_color'] ?? '#6c757d'); ?>; color: white;">Botón Secundario</button>
                                            <button class="btn" id="preview_accent" style="background-color: <?php echo htmlspecialchars($appearanceSettings['accent_color'] ?? '#ffc107'); ?>; color: black;">Botón Acento</button>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Guardar Configuración
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gestión de Administradores -->
                    <div class="tab-pane fade" id="admins">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-people"></i> Gestión de Administradores</h5>
                            </div>
                            <div class="card-body">
                                <p>Para gestionar los administradores del sistema, haga clic en el siguiente botón:</p>
                                <a href="<?php echo BASE_URL; ?>?view=admin/admins" class="btn btn-primary">
                                    <i class="bi bi-people"></i> Ir a Gestión de Administradores
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sincronizar inputs de color
        const colorInputs = [
            'primary_color', 'secondary_color', 'accent_color', 'text_color', 'background_color'
        ];
        
        colorInputs.forEach(function(id) {
            const colorInput = document.getElementById(id);
            const textInput = document.getElementById(id + '_text');
            
            colorInput.addEventListener('input', function() {
                textInput.value = this.value;
                updatePreview();
            });
            
            textInput.addEventListener('input', function() {
                colorInput.value = this.value;
                updatePreview();
            });
        });
        
        function updatePreview() {
            const preview = document.getElementById('preview');
            const primaryBtn = document.getElementById('preview_primary');
            const secondaryBtn = document.getElementById('preview_secondary');
            const accentBtn = document.getElementById('preview_accent');
            
            preview.style.backgroundColor = document.getElementById('background_color').value;
            preview.style.color = document.getElementById('text_color').value;
            
            primaryBtn.style.backgroundColor = document.getElementById('primary_color').value;
            secondaryBtn.style.backgroundColor = document.getElementById('secondary_color').value;
            accentBtn.style.backgroundColor = document.getElementById('accent_color').