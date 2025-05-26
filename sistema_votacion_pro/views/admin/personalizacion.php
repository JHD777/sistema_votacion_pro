<?php
require_once __DIR__ . '/../../controllers/SystemController.php';

// Verificar si el usuario es administrador supremo
if (!isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] !== 'super') {
    header('Location: ' . BASE_URL . '?view=admin/dashboard');
    exit;
}

$systemController = new SystemController();

// Procesar formulario
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Configuraciones de colores
    $colores = [
        'color_primario' => $_POST['color_primario'] ?? '#0d6efd',
        'color_secundario' => $_POST['color_secundario'] ?? '#6c757d',
        'color_exito' => $_POST['color_exito'] ?? '#198754',
        'color_peligro' => $_POST['color_peligro'] ?? '#dc3545',
        'color_advertencia' => $_POST['color_advertencia'] ?? '#ffc107',
        'color_info' => $_POST['color_info'] ?? '#0dcaf0',
        'color_fondo' => $_POST['color_fondo'] ?? '#f8f9fa'
    ];
    
    // Configuraciones de textos
    $textos = [
        'titulo_sistema' => $_POST['titulo_sistema'] ?? 'Sistema de Votación',
        'subtitulo_sistema' => $_POST['subtitulo_sistema'] ?? 'Vota de manera segura y transparente',
        'mensaje_bienvenida' => $_POST['mensaje_bienvenida'] ?? 'Bienvenido al sistema de votación',
        'footer_texto' => $_POST['footer_texto'] ?? '© ' . date('Y') . ' Sistema de Votación'
    ];
    
    // Configuración de zona horaria
    $timezone = $_POST['timezone'] ?? 'America/Mexico_City';
    $configs = array_merge($colores, $textos, ['timezone' => $timezone]);
    
    // Guardar configuraciones
    $result = $systemController->updateConfigs($configs);
    $mensaje = $result['message'];
    $tipo_mensaje = $result['success'] ? 'success' : 'danger';
}

// Obtener configuraciones actuales
$result = $systemController->getAllConfigs();
$configs = $result['success'] ? $result['configs'] : [];

// Valores por defecto
$defaults = [
    'color_primario' => '#0d6efd',
    'color_secundario' => '#6c757d',
    'color_exito' => '#198754',
    'color_peligro' => '#dc3545',
    'color_advertencia' => '#ffc107',
    'color_info' => '#0dcaf0',
    'color_fondo' => '#f8f9fa',
    'titulo_sistema' => 'Sistema de Votación',
    'subtitulo_sistema' => 'Vota de manera segura y transparente',
    'mensaje_bienvenida' => 'Bienvenido al sistema de votación',
    'footer_texto' => '© ' . date('Y') . ' Sistema de Votación'
];

// Combinar valores por defecto con configuraciones existentes
foreach ($defaults as $key => $value) {
    if (!isset($configs[$key])) {
        $configs[$key] = $value;
    }
}

// Obtener lista de zonas horarias
$zonas_horarias = DateTimeZone::listIdentifiers();
$zona_actual = isset($configs['timezone']) ? $configs['timezone'] : 'America/Mexico_City';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalización del Sistema - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-palette"></i> Personalización del Sistema</h1>
                <p class="lead">Personaliza los colores y textos del sistema de votación.</p>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <!-- Personalización de colores -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-palette2"></i> Colores del Sistema</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="color_primario" class="form-label">Color Primario</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="color_primario" name="color_primario" value="<?php echo $configs['color_primario']; ?>">
                                    <input type="text" class="form-control" value="<?php echo $configs['color_primario']; ?>" id="color_primario_text" oninput="document.getElementById('color_primario').value = this.value">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="color_secundario" class="form-label">Color Secundario</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="color_secundario" name="color_secundario" value="<?php echo $configs['color_secundario']; ?>">
                                    <input type="text" class="form-control" value="<?php echo $configs['color_secundario']; ?>" id="color_secundario_text" oninput="document.getElementById('color_secundario').value = this.value">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="color_exito" class="form-label">Color Éxito</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="color_exito" name="color_exito" value="<?php echo $configs['color_exito']; ?>">
                                    <input type="text" class="form-control" value="<?php echo $configs['color_exito']; ?>" id="color_exito_text" oninput="document.getElementById('color_exito').value = this.value">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="color_peligro" class="form-label">Color Peligro</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="color_peligro" name="color_peligro" value="<?php echo $configs['color_peligro']; ?>">
                                    <input type="text" class="form-control" value="<?php echo $configs['color_peligro']; ?>" id="color_peligro_text" oninput="document.getElementById('color_peligro').value = this.value">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="color_advertencia" class="form-label">Color Advertencia</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="color_advertencia" name="color_advertencia" value="<?php echo $configs['color_advertencia']; ?>">
                                    <input type="text" class="form-control" value="<?php echo $configs['color_advertencia']; ?>" id="color_advertencia_text" oninput="document.getElementById('color_advertencia').value = this.value">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="color_info" class="form-label">Color Info</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="color_info" name="color_info" value="<?php echo $configs['color_info']; ?>">
                                    <input type="text" class="form-control" value="<?php echo $configs['color_info']; ?>" id="color_info_text" oninput="document.getElementById('color_info').value = this.value">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="color_fondo" class="form-label">Color de Fondo</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="color_fondo" name="color_fondo" value="<?php echo $configs['color_fondo']; ?>">
                                    <input type="text" class="form-control" value="<?php echo $configs['color_fondo']; ?>" id="color_fondo_text" oninput="document.getElementById('color_fondo').value = this.value">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Personalización de textos -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-type"></i> Textos del Sistema</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="titulo_sistema" class="form-label">Título del Sistema</label>
                                <input type="text" class="form-control" id="titulo_sistema" name="titulo_sistema" value="<?php echo $configs['titulo_sistema']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="subtitulo_sistema" class="form-label">Subtítulo del Sistema</label>
                                <input type="text" class="form-control" id="subtitulo_sistema" name="subtitulo_sistema" value="<?php echo $configs['subtitulo_sistema']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="mensaje_bienvenida" class="form-label">Mensaje de Bienvenida</label>
                                <textarea class="form-control" id="mensaje_bienvenida" name="mensaje_bienvenida" rows="3"><?php echo $configs['mensaje_bienvenida']; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="footer_texto" class="form-label">Texto del Pie de Página</label>
                                <input type="text" class="form-control" id="footer_texto" name="footer_texto" value="<?php echo $configs['footer_texto']; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vista previa -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-eye"></i> Vista Previa</h5>
                        </div>
                        <div class="card-body">
                            <div class="preview-container p-3 rounded" id="preview" style="background-color: <?php echo $configs['color_fondo']; ?>;">
                                <h2 id="preview_titulo" style="color: <?php echo $configs['color_primario']; ?>;"><?php echo $configs['titulo_sistema']; ?></h2>
                                <p id="preview_subtitulo" style="color: <?php echo $configs['color_secundario']; ?>;"><?php echo $configs['subtitulo_sistema']; ?></p>
                                <div class="alert" style="background-color: <?php echo $configs['color_info']; ?>; color: white;" id="preview_mensaje">
                                    <?php echo $configs['mensaje_bienvenida']; ?>
                                </div>
                                <button class="btn" style="background-color: <?php echo $configs['color_primario']; ?>; color: white;">Botón Primario</button>
                                <button class="btn" style="background-color: <?php echo $configs['color_secundario']; ?>; color: white;">Botón Secundario</button>
                                <button class="btn" style="background-color: <?php echo $configs['color_exito']; ?>; color: white;">Botón Éxito</button>
                                <button class="btn" style="background-color: <?php echo $configs['color_peligro']; ?>; color: white;">Botón Peligro</button>
                                <hr>
                                <footer id="preview_footer" style="color: <?php echo $configs['color_secundario']; ?>;"><?php echo $configs['footer_texto']; ?></footer>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($_SESSION['admin_rol']) && $_SESSION['admin_rol'] === 'super'): ?>
                <div class="col-md-3 mb-3">
                    <a href="<?php echo BASE_URL; ?>?view=admin/admins" class="btn btn-danger w-100 p-3">
                        <i class="bi bi-people-fill"></i> Gestionar Admins
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Nueva tarjeta para Zona Horaria -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Configuración de Zona Horaria</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Zona horaria del sistema:</strong> La zona horaria determina cómo se calculan y muestran todas las fechas y horas en el sistema, incluyendo el inicio y fin de elecciones.<br>
                        <b>Importante:</b> Si cambias la zona horaria, el cambio será inmediato y puede afectar qué elecciones aparecen como activas para los usuarios.
                    </div>
                    <div class="mb-3">
                        <label for="timezone" class="form-label">Zona Horaria del Sistema</label>
                        <select name="timezone" id="timezone" class="form-select" required>
                            <?php foreach ($zonas_horarias as $zona): ?>
                                <option value="<?php echo $zona; ?>" <?php echo ($zona_actual == $zona) ? 'selected' : ''; ?>><?php echo $zona; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Afecta la visualización y lógica de fechas en todo el sistema.</small>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Guardar Configuración
                </button>
                <a href="<?php echo BASE_URL; ?>?view=admin/dashboard" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Panel
                </a>
            </div>
        </form>
    </div>
    
    <script>
    // Actualizar vista previa en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        // Colores
        const colorInputs = [
            { input: 'color_primario', text: 'color_primario_text', preview: ['preview_titulo'] },
            { input: 'color_secundario', text: 'color_secundario_text', preview: ['preview_subtitulo', 'preview_footer'] },
            { input: 'color_info', text: 'color_info_text', preview: ['preview_mensaje'] },
            { input: 'color_fondo', text: 'color_fondo_text', preview: ['preview'] }
        ];
        
        colorInputs.forEach(item => {
            const inputEl = document.getElementById(item.input);
            const textEl = document.getElementById(item.text);
            
            inputEl.addEventListener('input', function() {
                textEl.value = this.value;
                item.preview.forEach(previewId => {
                    const previewEl = document.getElementById(previewId);
                    if (previewId === 'preview') {
                        previewEl.style.backgroundColor = this.value;
                    } else {
                        previewEl.style.color = this.value;
                    }
                });
            });
            
            textEl.addEventListener('input', function() {
                inputEl.value = this.value;
                item.preview.forEach(previewId => {
                    const previewEl = document.getElementById(previewId);
                    if (previewId === 'preview') {
                        previewEl.style.backgroundColor = this.value;
                    } else {
                        previewEl.style.color = this.value;
                    }
                });
            });
        });
        
        // Textos
        const textInputs = [
            { input: 'titulo_sistema', preview: 'preview_titulo' },
            { input: 'subtitulo_sistema', preview: 'preview_subtitulo' },
            { input: 'mensaje_bienvenida', preview: 'preview_mensaje' },
            { input: 'footer_texto', preview: 'preview_footer' }
        ];
        
        textInputs.forEach(item => {
            const inputEl = document.getElementById(item.input);
            const previewEl = document.getElementById(item.preview);
            
            inputEl.addEventListener('input', function() {
                previewEl.textContent = this.value;
            });
        });
    });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>