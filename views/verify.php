<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$message = '';
$success = false;

// Verificar si se proporcionó un código de verificación
if (isset($_GET['code']) && !empty($_GET['code'])) {
    $authController = new AuthController();
    $result = $authController->verifyAccount($_GET['code']);
    
    $message = $result['message'];
    $success = $result['success'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta - Sistema de Votación Electrónica</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once __DIR__ . '/partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body p-5 text-center">
                        <h1 class="card-title mb-4">Verificación de Cuenta</h1>
                        
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                            
                            <?php if ($success): ?>
                                <p>Serás redirigido a la página de inicio de sesión en unos segundos...</p>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = '<?php echo BASE_URL; ?>?view=login';
                                    }, 5000);
                                </script>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="mb-4">Gracias por registrarte en nuestro Sistema de Votación Electrónica.</p>
                            <p>Hemos enviado un correo electrónico con un enlace de verificación a la dirección que proporcionaste durante el registro.</p>
                            <p>Por favor, revisa tu bandeja de entrada y haz clic en el enlace para verificar tu cuenta.</p>
                            <p class="text-muted">Si no recibes el correo en unos minutos, revisa tu carpeta de spam o solicita un nuevo correo de verificación.</p>
                            
                            <div class="mt-4">
                                <a href="<?php echo BASE_URL; ?>/login" class="btn btn-primary">Ir a Iniciar Sesión</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>