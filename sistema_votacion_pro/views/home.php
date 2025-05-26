<?php
require_once __DIR__ . '/../controllers/ElectionController.php';

$electionController = new ElectionController();
$elecciones_actuales = $electionController->getCurrentElections();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'Sistema de Votación Pro'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Sistema de Votación Pro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=user/dashboard">Mi Panel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=logout">Cerrar Sesión</a>
                        </li>
                    <?php elseif (isset($_SESSION['admin_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/dashboard">Panel de Administración</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=logout">Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=login">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=register">Registrarse</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/login">Acceso Administrador</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 mb-4"><?php echo isset(APP_CONFIGS['titulo_sistema']) ? htmlspecialchars(APP_CONFIGS['titulo_sistema']) : 'Bienvenido al Sistema de Votación Electrónica'; ?></h1>
                <p class="lead mb-4"><?php echo isset(APP_CONFIGS['subtitulo_sistema']) ? htmlspecialchars(APP_CONFIGS['subtitulo_sistema']) : 'Una plataforma segura y confiable para realizar votaciones electrónicas.'; ?></p>
            </div>
        </div>
    </div>
    
    <div class="container mt-3">
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">¿Cómo funciona?</h2>
                        <div class="row">
                            <div class="col-md-7">
                                <ol>
                                    <li>Regístrate con tus datos personales</li>
                                    <li>Verifica tu cuenta a través del enlace enviado a tu correo</li>
                                    <li>Inicia sesión en el sistema</li>
                                    <li>Selecciona la elección en la que deseas participar</li>
                                    <li>Emite tu voto de manera segura y confidencial</li>
                                </ol>
                            </div>
                            <div class="col-md-5 d-flex align-items-center justify-content-center">
                                <div class="d-grid gap-3">
                                    <a href="<?php echo BASE_URL; ?>?view=register" class="btn btn-primary btn-lg">Registrarse</a>
                                    <a href="<?php echo BASE_URL; ?>?view=login" class="btn btn-outline-primary btn-lg">Iniciar Sesión</a>
                                    <a href="<?php echo BASE_URL; ?>?view=admin/login" class="btn btn-outline-secondary btn-lg">Acceso Administrativo</a>
                                    <!-- Se eliminó el botón de Administrador Supremo para que solo sea accesible mediante el código Konami -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Código Konami integrado directamente -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Definimos la secuencia de teclas para el código secreto
        const codigoSecreto = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 
                              'ArrowLeft', 'ArrowRight', 'j', 'h', 'd'];
        
        // Variable para almacenar la posición actual en la secuencia
        let posicionActual = 0;
        
        // Escuchamos eventos de teclado
        document.addEventListener('keydown', function(e) {
            console.log('Tecla presionada:', e.key);
            
            // Verificamos si la tecla presionada coincide con la esperada
            if (e.key === codigoSecreto[posicionActual]) {
                posicionActual++;
                console.log('Posición actual:', posicionActual);
                
                // Si hemos completado toda la secuencia
                // Cuando se detecta el código secreto
                if (posicionActual === codigoSecreto.length) {
                    // Mostramos un mensaje
                    alert('¡Código secreto activado! Accediendo al modo Administrador Supremo...');
                    
                    // Definimos el código secreto (debe coincidir con el definido en el servidor)
                    const secretCode = 'MASTER2023';
                    
                    // Redirigimos a la página de verificación (corregido a verificar_supremo)
                    window.location.href = '<?php echo BASE_URL; ?>?view=admin/verificar_supremo&code=' + secretCode;
                    
                    // Reiniciamos la posición
                    posicionActual = 0;
                }
            } else {
                // Si no coincide, reiniciamos la secuencia
                console.log('Secuencia reiniciada');
                posicionActual = 0;
                
                // Si la tecla actual coincide con la primera del código, avanzamos
                if (e.key === codigoSecreto[0]) {
                    posicionActual = 1;
                    console.log('Reinicio con primera tecla correcta');
                }
            }
        });
    });
    </script>
</body>
</html>