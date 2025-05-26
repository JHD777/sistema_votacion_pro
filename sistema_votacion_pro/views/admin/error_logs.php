<?php
// Mostrar todos los errores durante el desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario es administrador supremo
if (!isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] !== 'super') {
    header('Location: ' . BASE_URL . '?view=admin/dashboard');
    exit;
}

// Verificar que la carpeta logs exista y tenga permisos de escritura
$logsDir = __DIR__ . '/../../logs';
if (!file_exists($logsDir)) {
    mkdir($logsDir, 0755, true);
}

// Verificar permisos del archivo de logs
$logFile = $logsDir . '/errors.log';
if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0666);
}

// Incluir el sistema de registro de errores
require_once __DIR__ . '/../../logs/error_log.php';

// Limpiar logs si se solicita
if (isset($_GET['clear']) && $_GET['clear'] == 1) {
    ErrorLogger::clearLog();
    header('Location: ' . BASE_URL . '?view=admin/error_logs');
    exit;
}

// Obtener los errores
try {
    $errores = ErrorLogger::getErrors();
} catch (Exception $e) {
    $errores = "Error al leer el archivo de logs: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Errores - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><i class="bi bi-exclamation-triangle"></i> Registro de Errores</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo BASE_URL; ?>?view=admin/error_logs&clear=1" class="btn btn-danger" onclick="return confirm('¿Estás seguro de limpiar todos los registros de errores?')">
                    <i class="bi bi-trash"></i> Limpiar Registros
                </a>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Errores Registrados</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Información de diagnóstico</h5>
                    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                    <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
                </div>
                <pre class="bg-light p-3" style="max-height: 600px; overflow-y: auto;"><?php echo htmlspecialchars($errores); ?></pre>
            </div>
        </div>
        
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-tools"></i> Herramientas de diagnóstico</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">Verificar archivo .htaccess</div>
                            <div class="card-body">
                                <p>Si estás usando Apache, asegúrate de que tu archivo .htaccess esté correctamente configurado.</p>
                                <pre class="bg-light p-2">
RewriteEngine On
RewriteBase /sistema_votacion_pro/

# Si el archivo o directorio no existe, redirigir a index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Configuración de PHP
php_flag display_errors On
php_value error_reporting E_ALL</pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Verificar permisos de archivos</div>
                            <div class="card-body">
                                <p>Ejecuta estos comandos para asegurar permisos correctos:</p>
                                <pre class="bg-light p-2">icacls c:\laragon\www\sistema_votacion_pro\logs /grant IUSR:(OI)(CI)F
icacls c:\laragon\www\sistema_votacion_pro\logs\errors.log /grant IUSR:F</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>