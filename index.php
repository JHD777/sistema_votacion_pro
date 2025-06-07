<?php
// Cargar la configuración
$config = require_once 'config/app_config.php';

// Configurar la zona horaria
date_default_timezone_set($config['app']['timezone']);

// Configurar el manejo de errores
if ($config['app']['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Configurar la sesión
session_set_cookie_params([
    'lifetime' => $config['session']['lifetime'] * 60,
    'path' => $config['session']['path'],
    'domain' => $config['session']['domain'],
    'secure' => $config['session']['secure'],
    'httponly' => $config['session']['httponly'],
    'samesite' => $config['session']['samesite']
]);

// Iniciar la sesión
session_start();

// Definir la ruta base
define('BASE_PATH', __DIR__);

// Redirigir a public/index.php
require_once 'public/index.php';

<!-- Scripts -->
<script src="assets/js/admin-access.js"></script>
</body>
</html>