<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Sistema de Votación Electrónica</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Acceso Administrativo</h2>
                        <div id="mensaje-login"></div>
                        <form id="admin-login-form">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?php echo BASE_URL; ?>?view=home">Volver al inicio</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#admin-login-form').on('submit', function(e) {
            e.preventDefault();
            
            // Recopilar datos del formulario
            var formData = {
                username: $('#username').val(),
                password: $('#password').val()
            };
            
            // Enviar solicitud AJAX
            $.ajax({
                url: '<?php echo BASE_URL; ?>/api/auth/admin-login',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        $('#mensaje-login').html('<div class="alert alert-success">Inicio de sesión exitoso. Redirigiendo...</div>');
                        setTimeout(function() {
                            window.location.href = '<?php echo BASE_URL; ?>?view=admin/dashboard';
                        }, 1500);
                    } else {
                        $('#mensaje-login').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#mensaje-login').html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
                }
            });
        });
    });
    </script>
</body>
</html>