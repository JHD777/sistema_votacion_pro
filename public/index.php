<?php
// Definir constantes esenciales primero
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);

// Incluir archivos de configuración base
if (file_exists(BASE_PATH . '/config/config.php')) {
    require_once BASE_PATH . '/config/config.php';
} else {
    // Si no existe, crear un archivo de configuración básico
    define('APP_NAME', 'Sistema de Votación Pro');
}

if (file_exists(BASE_PATH . '/config/dbconfig.php')) {
    require_once BASE_PATH . '/config/dbconfig.php';
} else {
    // Si no existe, mostrar un mensaje de error
    die("Error: El archivo de configuración de la base de datos no existe.");
}

// Cargar zona horaria desde la base de datos (system_config)
$defaultTimezone = 'America/Mexico_City';
$timezone = $defaultTimezone;
try {
    $configFile = BASE_PATH . '/models/SystemConfig.php';
    if (file_exists($configFile)) {
        require_once $configFile;
        $sysConfig = new \Models\SystemConfig();
        $tz = $sysConfig->getConfig('timezone', $defaultTimezone);
        if ($tz) $timezone = $tz;
    }
} catch (Exception $e) {
    // Si hay error, usar la zona horaria por defecto
}
define('APP_TIMEZONE', $timezone);
date_default_timezone_set(APP_TIMEZONE);

// Cargar todas las configuraciones del sistema desde la base de datos
$systemConfigs = [];
try {
    $configFile = BASE_PATH . '/models/SystemConfig.php';
    if (file_exists($configFile)) {
        require_once $configFile;
        $sysConfig = new \Models\SystemConfig();
        $result = $sysConfig->getAllConfigs();
        if ($result['success']) {
            $systemConfigs = $result['configs'];
        }
    }
} catch (Exception $e) {
    // Si hay error al cargar configuraciones, usar un array vacío o valores por defecto
    error_log("Error al cargar configuraciones del sistema: " . $e->getMessage());
}

// Combinar con configuraciones por defecto (si las hay en config.php u otro lugar)
// Aunque ya SystemConfig tiene defaults, esto es una capa extra si se desea
if (defined('APP_CONFIG')) {
    $systemConfigs = array_merge(APP_CONFIG, $systemConfigs);
}

// Definir una constante global para las configuraciones
define('APP_CONFIGS', $systemConfigs);

// Mostrar todos los errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Obtener la vista solicitada
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// Enrutamiento básico
try {
    // Definir la ruta del archivo a incluir
    $file_to_include = '';
    
    switch ($view) {
        case 'register':
            require_once BASE_PATH . '/views/auth/register.php';
            break;
        
        case 'login':
            require_once BASE_PATH . '/views/auth/login.php';
            break;
        
        case 'admin/login':
            require_once BASE_PATH . '/views/admin/login.php';
            break;
        
        case 'verify':
            require_once BASE_PATH . '/views/verify.php';
            break;
        
        case 'logout':
            require_once BASE_PATH . '/controllers/AuthController.php';
            $authController = new AuthController();
            $authController->logout();
            header('Location: ' . BASE_URL);
            exit;
            break;
        
        // Panel de administración
        case 'admin/dashboard':
            require_once BASE_PATH . '/views/admin/dashboard.php';
            break;
            
        case 'admin/verificar_supremo':
            require_once BASE_PATH . '/views/admin/verificar_supremo.php';
            break;
            
        case 'admin/elections':
            require_once BASE_PATH . '/views/admin/elections.php';
            break;
        
        case 'admin/candidates':
            require_once BASE_PATH . '/views/admin/candidates.php';
            break;
        
        case 'admin/users':
            require_once BASE_PATH . '/views/admin/users.php';
            break;
        
        case 'admin/results':
            require_once BASE_PATH . '/views/admin/results.php';
            break;
            
        // Nuevas rutas para administrador supremo
        case 'admin/admins':
            require_once BASE_PATH . '/views/admin/admins.php';
            break;
            
        case 'admin/settings':
            require_once BASE_PATH . '/views/admin/settings.php';
            break;
        
        // Panel de usuario
        case 'user/dashboard':
            require_once BASE_PATH . '/views/user/dashboard.php';
            break;
        
        case 'user/vote':
            require_once BASE_PATH . '/views/user/vote.php';
            break;
        
        // API
        case 'api/auth/login':
            header('Content-Type: application/json');
            require_once BASE_PATH . '/controllers/AuthController.php';
            $authController = new AuthController();
            
            // Obtener datos de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['email']) && isset($data['password'])) {
                echo json_encode($authController->login($data['email'], $data['password']));
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
            }
            break;
        
        case 'api/auth/admin-login':
            header('Content-Type: application/json');
            require_once BASE_PATH . '/controllers/AuthController.php';
            $authController = new AuthController();
            
            // Obtener datos de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['username']) && isset($data['password'])) {
                echo json_encode($authController->adminLogin($data['username'], $data['password']));
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
            }
            break;
        
        case 'api/auth/register':
            header('Content-Type: application/json');
            require_once BASE_PATH . '/controllers/AuthController.php';
            $authController = new AuthController();
            
            // Obtener datos de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            
            echo json_encode($authController->register($data));
            break;
        
        // Página principal
        case 'home':
            $file_to_include = BASE_PATH . '/views/home.php';
            break;
        
        // Página no encontrada
        default:
            // Verificar si es una ruta de administrador
            if (strpos($view, 'admin/') === 0) {
                $admin_view = substr($view, 6); // Eliminar 'admin/'
                $file_to_include = BASE_PATH . '/views/admin/' . $admin_view . '.php';
                
                if (file_exists($file_to_include)) {
                    require_once BASE_PATH . '/helpers/AuthHelper.php';
                    
                    // Excepción para verificar_supremo que no requiere autenticación
                    if ($admin_view !== 'verificar_supremo') {
                        AuthHelper::verificarAdmin();
                    }
                    
                    require_once $file_to_include;
                    break;
                }
            }
            
            $file_to_include = BASE_PATH . '/views/404.php';
            require_once $file_to_include;
            break;
    }
    
    // Verificar si el archivo existe antes de incluirlo
    if (!empty($file_to_include)) {
        if (file_exists($file_to_include)) {
            require_once $file_to_include;
        } else {
            throw new Exception("El archivo '$file_to_include' no existe.");
        }
    }
} catch (Exception $e) {
    // Mostrar error
    echo "<h1>Error</h1>";
    echo "<p>Se ha producido un error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . " en la línea " . $e->getLine() . "</p>";
}
?>

