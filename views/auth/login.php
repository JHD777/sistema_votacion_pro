<?php
// Si ya hay una sesión activa, redirigir según el tipo de usuario
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user') {
    header('Location: ' . BASE_URL . '/user/dashboard');
    exit;
} elseif (isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin') {
    header('Location: ' . BASE_URL . '/admin/dashboard');
    exit;
}

require_once __DIR__ . '/../../models/User.php';
use Models\User;

// Procesar el formulario de inicio de sesión
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $userModel = new User();
    $result = $userModel->login($email, $password);
    
    if ($result['success']) {
        // Guardar datos en sesión
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['user_name'] = $result['user']['nombre'] . ' ' . $result['user']['apellido'];
        $_SESSION['user_email'] = $result['user']['email'];
        $_SESSION['user_type'] = 'user';
        
        header('Location: ' . BASE_URL . '?view=user/dashboard');
        exit;
    } else {
        $mensaje = $result['message'];
        $tipo_mensaje = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Iniciar Sesión</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <p>¿No tienes una cuenta? <a href="<?php echo BASE_URL; ?>?view=register">Regístrate aquí</a></p>
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