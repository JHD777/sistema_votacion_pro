<?php
// Verificar si ya hay una sesión de administrador supremo
if (isset($_SESSION['admin_rol']) && $_SESSION['admin_rol'] === 'super') {
    header('Location: ' . BASE_URL . '?view=admin/dashboard');
    exit;
}

// Procesar el formulario si se envió
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
    
    // Verificar el código (usando la constante definida en config.php)
    if (defined('SECRET_CODE')) {
        $codigo_correcto = SECRET_CODE;
    } else {
        $codigo_correcto = "MASTER2023"; // Valor por defecto
    }
    
    if ($codigo === $codigo_correcto) {
        // Iniciar sesión como administrador supremo
        $_SESSION['admin_id'] = 0; // ID especial para administrador supremo
        $_SESSION['admin_name'] = 'Administrador Supremo';
        $_SESSION['admin_rol'] = 'super';
        $_SESSION['user_type'] = 'admin'; // Para compatibilidad con el resto del sistema
        $_SESSION['is_supreme'] = true;
        
        // Redirigir al panel de administración
        header('Location: ' . BASE_URL . '?view=admin/dashboard');
        exit;
    } else {
        $mensaje = 'Código incorrecto. Por favor, intente nuevamente.';
        $tipo_mensaje = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Administrador Supremo - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Acceso de Administrador Supremo</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>
                        
                        <p class="lead">Ingrese el código de acceso para continuar:</p>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">Código de Acceso</label>
                                <input type="password" class="form-control" id="codigo" name="codigo" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-unlock"></i> Verificar Acceso
                                </button>
                                <a href="<?php echo BASE_URL; ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver al Inicio
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>