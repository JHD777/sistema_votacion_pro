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
    <?php include_once __DIR__ . '/partials/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="loginTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user-login" type="button" role="tab" aria-controls="user-login" aria-selected="true">Usuario</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin-login" type="button" role="tab" aria-controls="admin-login" aria-selected="false">Administrador</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="loginTabsContent">
                            <!-- Formulario de inicio de sesión para usuarios -->
                            <div class="tab-pane fade show active" id="user-login" role="tabpanel" aria-labelledby="user-tab">
                                <h2 class="card-title mb-4">Iniciar Sesión como Usuario</h2>
                                <div id="user-login-message"></div>
                                <form id="user-login-form">
                                    <div class="mb-3">
                                        <label for="user-email" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="user-email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user-password" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="user-password" name="password" required>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="remember-me">
                                        <label class="form-check-label" for="remember-me">Recordarme</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                </form>
                                <div class="mt-3">
                                    <a href="<?php echo BASE_URL; ?>/reset_password">¿Olvidaste tu contraseña?</a>
                                </div>
                                <div class="mt-3">
                                    ¿No tienes una cuenta? <a href="<?php echo BASE_URL; ?>/register">Regístrate aquí</a>
                                </div>
                            </div>
                            
                            <!-- Formulario de inicio de sesión para administradores -->
                            <div class="tab-pane fade" id="admin-login" role="tabpanel" aria-labelledby="admin-tab">
                                <h2 class="card-title text-center mb-4">Iniciar Sesión</h2>
                                <div id="mensaje-login"></div>
                                <form id="login-form">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Recordarme</label>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="<?php echo BASE_URL; ?>/forgot-password">¿Olvidaste tu contraseña?</a>
                                    </div>
                                    <div class="text-center mt-2">
                                        ¿No tienes una cuenta? <a href="<?php echo BASE_URL; ?>/register">Regístrate aquí</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#login-form').on('submit', function(e) {
            e.preventDefault();
            
            // Recopilar datos del formulario
            var formData = {
                email: $('#email').val(),
                password: $('#password').val()
            };
            
            // Enviar solicitud AJAX
            $.ajax({
                url: '<?php echo BASE_URL; ?>/api/auth/login',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        $('#mensaje-login').html('<div class="alert alert-success">Inicio de sesión exitoso. Redirigiendo...</div>');
                        setTimeout(function() {
                            window.location.href = '<?php echo BASE_URL; ?>/user/dashboard';
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