<?php
// Configuración genérica para cualquier hosting
return [
    // Configuración de la aplicación
    'app' => [
        'name' => 'Sistema de Votación Pro',
        'version' => '1.0.0',
        'debug' => false,
        'maintenance_mode' => false,
        'timezone' => 'America/La_Paz',
        'locale' => 'es',
        'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]",
    ],

    // Configuración de la base de datos
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'tu_base_de_datos',
        'user' => getenv('DB_USER') ?: 'tu_usuario',
        'pass' => getenv('DB_PASS') ?: 'tu_contraseña',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],

    // Configuración de sesión
    'session' => [
        'lifetime' => 120, // minutos
        'path' => '/',
        'domain' => null,
        'secure' => true,
        'httponly' => true,
        'samesite' => 'lax',
    ],

    // Configuración de archivos
    'upload' => [
        'path' => 'uploads/',
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'document' => ['pdf', 'doc', 'docx'],
        ],
    ],

    // Configuración de seguridad
    'security' => [
        'password_hash_algo' => PASSWORD_BCRYPT,
        'password_hash_options' => ['cost' => 12],
        'token_lifetime' => 3600, // 1 hora
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutos
    ],

    // Configuración de correo
    'mail' => [
        'from_address' => 'noreply@tudominio.com',
        'from_name' => 'Sistema de Votación',
        'smtp' => [
            'host' => getenv('MAIL_HOST') ?: 'smtp.tudominio.com',
            'port' => getenv('MAIL_PORT') ?: 587,
            'username' => getenv('MAIL_USERNAME') ?: '',
            'password' => getenv('MAIL_PASSWORD') ?: '',
            'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
        ],
    ],

    // Configuración de caché
    'cache' => [
        'driver' => 'file',
        'path' => 'cache/',
        'lifetime' => 3600, // 1 hora
    ],

    // Configuración de logs
    'logging' => [
        'enabled' => true,
        'path' => 'logs/',
        'level' => 'error', // debug, info, warning, error, critical
    ],

    // Configuración de la interfaz
    'ui' => [
        'theme' => 'default',
        'colors' => [
            'primary' => '#3498db',
            'secondary' => '#2ecc71',
            'accent' => '#e74c3c',
            'background' => '#ffffff',
            'text' => '#333333',
        ],
        'pagination' => [
            'per_page' => 10,
            'max_pages' => 5,
        ],
    ],
]; 