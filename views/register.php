<?php
require_once __DIR__ . '/../controllers/AuthController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Votación Electrónica</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once __DIR__ . '/partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Registro de Usuario</h2>
                            <div id="mensaje-registro"></div>
                            <form id="registro-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="documento_identidad" class="form-label">Documento de Identidad</label>
                                    <input type="text" class="form-control" id="documento_identidad" name="documento_identidad" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a></label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Registrarse</button>
                                </div>
                                <div class="text-center mt-3">
                                    ¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>/login">Inicia sesión aquí</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Términos y Condiciones -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Términos y Condiciones del Sistema de Votación Electrónica</h4>
                    <p>Al registrarte y utilizar este sistema, aceptas los siguientes términos:</p>
                    <ol>
                        <li>Proporcionar información veraz y actualizada durante el registro.</li>
                        <li>Mantener la confidencialidad de tu contraseña y cuenta.</li>
                        <li>No intentar manipular el sistema de votación ni realizar acciones fraudulentas.</li>
                        <li>Respetar la privacidad y los derechos de otros usuarios.</li>
                        <li>Aceptar que solo puedes emitir un voto por elección.</li>
                        <li>Entender que una vez emitido tu voto, no podrá ser modificado.</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#registro-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validar que las contraseñas coincidan
            if ($('#password').val() !== $('#confirm_password').val()) {
                $('#mensaje-registro').html('<div class="alert alert-danger">Las contraseñas no coinciden</div>');
                return;
            }
            
            // Recopilar datos del formulario
            var formData = {
                nombre: $('#nombre').val(),
                apellido: $('#apellido').val(),
                email: $('#email').val(),
                documento_identidad: $('#documento_identidad').val(),
                password: $('#password').val()
            };
            
            // Enviar solicitud AJAX
            $.ajax({
                url: '<?php echo BASE_URL; ?>/api/auth/register',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        $('#mensaje-registro').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#registro-form')[0].reset();
                    } else {
                        $('#mensaje-registro').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#mensaje-registro').html('<div class="alert alert-danger">Error al procesar la solicitud</div>');
                }
            });
        });
    });
    </script>
</body>
</html>