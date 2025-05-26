<?php
// Configuración de la aplicación
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://sistema_votacion_pro.test');
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Sistema de Votación Electrónica');
}

// Configuración de correo
if (!defined('MAIL_HOST')) {
    define('MAIL_HOST', 'smtp.example.com');
}
if (!defined('MAIL_PORT')) {
    define('MAIL_PORT', 587);
}
if (!defined('MAIL_USERNAME')) {
    define('MAIL_USERNAME', 'noreply@example.com');
}
if (!defined('MAIL_PASSWORD')) {
    define('MAIL_PASSWORD', 'tu-contraseña');
}
if (!defined('MAIL_FROM_ADDRESS')) {
    define('MAIL_FROM_ADDRESS', 'noreply@example.com');
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', 'Sistema de Votación Pro');
}

// Configuración de seguridad
if (!defined('SECRET_CODE')) {
    define('SECRET_CODE', 'MASTER2023');
}

// Configuración de seguridad adicional
if (!defined('SECURITY_CONFIG')) {
    define('SECURITY_CONFIG', [
        'password_min_length' => 8,
        'password_require_special' => true,
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutos en segundos
        'session_lifetime' => 3600, // 1 hora en segundos
        'csrf_token_lifetime' => 7200, // 2 horas en segundos
        'remember_me_lifetime' => 2592000, // 30 días en segundos
    ]);
}

// Configuración de correo mejorada
if (!defined('MAIL_CONFIG')) {
    define('MAIL_CONFIG', [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'noreply@example.com',
        'password' => 'tu-contraseña',
        'from_address' => 'noreply@example.com',
        'from_name' => 'Sistema de Votación Pro',
        'encryption' => 'tls',
        'auth_mode' => 'login'
    ]);
}

// Configuración de la aplicación
if (!defined('APP_CONFIG')) {
    define('APP_CONFIG', [
        'name' => 'Sistema de Votación Pro',
        'version' => '1.0.0',
        'debug' => false,
        'maintenance_mode' => false,
        'timezone' => 'America/Mexico_City',
        'locale' => 'es_MX',
        'upload_max_size' => 5242880, // 5MB en bytes
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf'],
        'session_name' => 'votacion_pro_session',
        'cookie_lifetime' => 86400, // 24 horas
        'cookie_path' => '/',
        'cookie_domain' => '',
        'cookie_secure' => true,
        'cookie_httponly' => true
    ]);
}

// Configuración de la base de datos
require_once __DIR__ . '/dbconfig.php';

// Zona horaria
if (!function_exists('date_default_timezone_set') || date_default_timezone_get() != 'America/Mexico_City') {
    date_default_timezone_set('America/Mexico_City');
}

// Otras configuraciones
if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}

// Configuración de logging
if (!defined('LOG_CONFIG')) {
    define('LOG_CONFIG', [
        'enabled' => true,
        'path' => __DIR__ . '/../logs',
        'level' => 'error', // debug, info, warning, error, critical
        'max_files' => 5,
        'max_size' => 5242880 // 5MB en bytes
    ]);
}

// Configuración de caché
if (!defined('CACHE_CONFIG')) {
    define('CACHE_CONFIG', [
        'enabled' => true,
        'driver' => 'file', // file, redis, memcached
        'path' => __DIR__ . '/../cache',
        'lifetime' => 3600, // 1 hora en segundos
        'prefix' => 'votacion_pro_'
    ]);
}

// Configuración de API
if (!defined('API_CONFIG')) {
    define('API_CONFIG', [
        'enabled' => true,
        'rate_limit' => 100, // peticiones por minuto
        'token_lifetime' => 3600, // 1 hora en segundos
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
        'allowed_headers' => ['Content-Type', 'Authorization']
    ]);
}

// Configuración de votación
if (!defined('VOTING_CONFIG')) {
    define('VOTING_CONFIG', [
        'min_voters' => 10,
        'max_candidates' => 20,
        'voting_duration' => 86400, // 24 horas en segundos
        'results_public' => true,
        'allow_abstention' => true,
        'require_verification' => true,
        'ip_check' => true,
        'device_check' => true
    ]);
}

// Configuración de notificaciones
if (!defined('NOTIFICATION_CONFIG')) {
    define('NOTIFICATION_CONFIG', [
        'email' => true,
        'sms' => false,
        'push' => false,
        'templates_path' => __DIR__ . '/../views/notifications'
    ]);
}

// Configuración de validación
if (!defined('VALIDATION_CONFIG')) {
    define('VALIDATION_CONFIG', [
        'document_types' => ['dni', 'passport', 'license'],
        'min_name_length' => 2,
        'max_name_length' => 50,
        'phone_format' => '/^\+?[1-9]\d{1,14}$/',
        'document_format' => '/^[A-Za-z0-9]+$/'
    ]);
}

// Configuración de exportación
if (!defined('EXPORT_CONFIG')) {
    define('EXPORT_CONFIG', [
        'formats' => ['csv', 'xlsx', 'pdf'],
        'max_rows' => 10000,
        'compression' => true,
        'templates_path' => __DIR__ . '/../views/exports'
    ]);
}

// Configuración de respaldo
if (!defined('BACKUP_CONFIG')) {
    define('BACKUP_CONFIG', [
        'enabled' => true,
        'frequency' => 'daily', // daily, weekly, monthly
        'retention' => 30, // días
        'path' => __DIR__ . '/../backups',
        'compress' => true,
        'notify' => true
    ]);
}

// Configuración de monitoreo
if (!defined('MONITORING_CONFIG')) {
    define('MONITORING_CONFIG', [
        'enabled' => true,
        'check_interval' => 300, // 5 minutos en segundos
        'metrics' => ['cpu', 'memory', 'disk', 'network'],
        'alert_threshold' => 80, // porcentaje
        'notify_admins' => true
    ]);
}

// Configuración de auditoría
if (!defined('AUDIT_CONFIG')) {
    define('AUDIT_CONFIG', [
        'enabled' => true,
        'log_actions' => true,
        'log_changes' => true,
        'log_access' => true,
        'retention' => 365, // días
        'notify_admins' => true
    ]);
}

// Configuración de temas
if (!defined('THEME_CONFIG')) {
    define('THEME_CONFIG', [
        'default' => 'light',
        'available' => ['light', 'dark'],
        'custom_colors' => true,
        'allow_user_theme' => true
    ]);
}

// Configuración de accesibilidad
if (!defined('ACCESSIBILITY_CONFIG')) {
    define('ACCESSIBILITY_CONFIG', [
        'high_contrast' => true,
        'font_size' => true,
        'screen_reader' => true,
        'keyboard_navigation' => true
    ]);
}

// Configuración de rendimiento
if (!defined('PERFORMANCE_CONFIG')) {
    define('PERFORMANCE_CONFIG', [
        'minify_css' => true,
        'minify_js' => true,
        'compress_html' => true,
        'cache_static' => true,
        'lazy_loading' => true
    ]);
}

// Configuración de SEO
if (!defined('SEO_CONFIG')) {
    define('SEO_CONFIG', [
        'meta_description' => 'Sistema de Votación Electrónica Profesional',
        'meta_keywords' => 'votación, elecciones, sistema electrónico',
        'robots' => 'noindex, nofollow',
        'sitemap' => false
    ]);
}

// Configuración de mantenimiento
if (!defined('MAINTENANCE_CONFIG')) {
    define('MAINTENANCE_CONFIG', [
        'enabled' => false,
        'allowed_ips' => ['127.0.0.1'],
        'message' => 'El sistema está en mantenimiento. Por favor, intente más tarde.',
        'end_time' => null
    ]);
}

// Configuración de actualizaciones
if (!defined('UPDATE_CONFIG')) {
    define('UPDATE_CONFIG', [
        'auto_check' => true,
        'check_interval' => 86400, // 24 horas en segundos
        'auto_update' => false,
        'backup_before_update' => true,
        'notify_admins' => true
    ]);
}

// Configuración de internacionalización
if (!defined('I18N_CONFIG')) {
    define('I18N_CONFIG', [
        'default_language' => 'es',
        'available_languages' => ['es', 'en'],
        'detect_browser_language' => true,
        'fallback_language' => 'en'
    ]);
}

// Configuración de impresión
if (!defined('PRINT_CONFIG')) {
    define('PRINT_CONFIG', [
        'enabled' => true,
        'templates_path' => __DIR__ . '/../views/prints',
        'paper_size' => 'A4',
        'orientation' => 'portrait',
        'margin' => '10mm'
    ]);
}

// Configuración de reportes
if (!defined('REPORT_CONFIG')) {
    define('REPORT_CONFIG', [
        'enabled' => true,
        'templates_path' => __DIR__ . '/../views/reports',
        'formats' => ['pdf', 'xlsx', 'csv'],
        'schedule' => false,
        'notify_admins' => true
    ]);
}

// Configuración de estadísticas
if (!defined('STATS_CONFIG')) {
    define('STATS_CONFIG', [
        'enabled' => true,
        'track_visitors' => true,
        'track_actions' => true,
        'track_errors' => true,
        'retention' => 90, // días
        'anonymize' => true
    ]);
}

// Configuración de integración
if (!defined('INTEGRATION_CONFIG')) {
    define('INTEGRATION_CONFIG', [
        'enabled' => false,
        'providers' => [],
        'webhooks' => [],
        'api_keys' => []
    ]);
}

// Configuración de validación de documentos
if (!defined('DOCUMENT_VALIDATION_CONFIG')) {
    define('DOCUMENT_VALIDATION_CONFIG', [
        'enabled' => true,
        'max_size' => 5242880, // 5MB en bytes
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf'],
        'ocr_enabled' => false,
        'face_recognition' => false
    ]);
}

// Configuración de geolocalización
if (!defined('GEO_CONFIG')) {
    define('GEO_CONFIG', [
        'enabled' => false,
        'provider' => 'ipapi',
        'api_key' => '',
        'cache_results' => true,
        'cache_time' => 86400 // 24 horas en segundos
    ]);
}

// Configuración de validación de edad
if (!defined('AGE_VALIDATION_CONFIG')) {
    define('AGE_VALIDATION_CONFIG', [
        'enabled' => true,
        'min_age' => 18,
        'max_age' => 120,
        'require_verification' => true
    ]);
}

// Configuración de validación de residencia
if (!defined('RESIDENCE_VALIDATION_CONFIG')) {
    define('RESIDENCE_VALIDATION_CONFIG', [
        'enabled' => false,
        'require_proof' => true,
        'allowed_documents' => ['utility_bill', 'bank_statement', 'government_letter'],
        'verification_period' => 90 // días
    ]);
}

// Configuración de validación de identidad
if (!defined('IDENTITY_VALIDATION_CONFIG')) {
    define('IDENTITY_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['document', 'biometric', 'facial'],
        'require_multiple' => false,
        'verification_threshold' => 0.8
    ]);
}

// Configuración de validación de votos
if (!defined('VOTE_VALIDATION_CONFIG')) {
    define('VOTE_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['token', 'biometric', 'facial'],
        'require_multiple' => false,
        'verification_threshold' => 0.8,
        'allow_revote' => false,
        'revote_timeout' => 300 // 5 minutos en segundos
    ]);
}

// Configuración de validación de resultados
if (!defined('RESULTS_VALIDATION_CONFIG')) {
    define('RESULTS_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['hash', 'signature', 'witness'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_recount' => true,
        'recount_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de auditoría
if (!defined('AUDIT_VALIDATION_CONFIG')) {
    define('AUDIT_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['log', 'hash', 'signature'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_review' => true,
        'review_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de seguridad
if (!defined('SECURITY_VALIDATION_CONFIG')) {
    define('SECURITY_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['2fa', 'captcha', 'ip'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_bypass' => false,
        'bypass_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de rendimiento
if (!defined('PERFORMANCE_VALIDATION_CONFIG')) {
    define('PERFORMANCE_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['load', 'stress', 'security'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_optimization' => true,
        'optimization_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de usabilidad
if (!defined('USABILITY_VALIDATION_CONFIG')) {
    define('USABILITY_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['accessibility', 'responsiveness', 'compatibility'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_improvement' => true,
        'improvement_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de accesibilidad
if (!defined('ACCESSIBILITY_VALIDATION_CONFIG')) {
    define('ACCESSIBILITY_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['wcag', 'aria', 'semantic'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_improvement' => true,
        'improvement_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de compatibilidad
if (!defined('COMPATIBILITY_VALIDATION_CONFIG')) {
    define('COMPATIBILITY_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['browser', 'device', 'os'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_improvement' => true,
        'improvement_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de rendimiento
if (!defined('PERFORMANCE_VALIDATION_CONFIG')) {
    define('PERFORMANCE_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['load', 'stress', 'security'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_optimization' => true,
        'optimization_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de seguridad
if (!defined('SECURITY_VALIDATION_CONFIG')) {
    define('SECURITY_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['2fa', 'captcha', 'ip'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_bypass' => false,
        'bypass_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de auditoría
if (!defined('AUDIT_VALIDATION_CONFIG')) {
    define('AUDIT_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['log', 'hash', 'signature'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_review' => true,
        'review_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de resultados
if (!defined('RESULTS_VALIDATION_CONFIG')) {
    define('RESULTS_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['hash', 'signature', 'witness'],
        'require_multiple' => true,
        'verification_threshold' => 0.9,
        'allow_recount' => true,
        'recount_threshold' => 0.1 // 10% de diferencia
    ]);
}

// Configuración de validación de votos
if (!defined('VOTE_VALIDATION_CONFIG')) {
    define('VOTE_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['token', 'biometric', 'facial'],
        'require_multiple' => false,
        'verification_threshold' => 0.8,
        'allow_revote' => false,
        'revote_timeout' => 300 // 5 minutos en segundos
    ]);
}

// Configuración de validación de identidad
if (!defined('IDENTITY_VALIDATION_CONFIG')) {
    define('IDENTITY_VALIDATION_CONFIG', [
        'enabled' => true,
        'methods' => ['document', 'biometric', 'facial'],
        'require_multiple' => false,
        'verification_threshold' => 0.8
    ]);
}

// Configuración de validación de residencia
if (!defined('RESIDENCE_VALIDATION_CONFIG')) {
    define('RESIDENCE_VALIDATION_CONFIG', [
        'enabled' => false,
        'require_proof' => true,
        'allowed_documents' => ['utility_bill', 'bank_statement', 'government_letter'],
        'verification_period' => 90 // días
    ]);
}

// Configuración de validación de edad
if (!defined('AGE_VALIDATION_CONFIG')) {
    define('AGE_VALIDATION_CONFIG', [
        'enabled' => true,
        'min_age' => 18,
        'max_age' => 120,
        'require_verification' => true
    ]);
}

// Configuración de geolocalización
if (!defined('GEO_CONFIG')) {
    define('GEO_CONFIG', [
        'enabled' => false,
        'provider' => 'ipapi',
        'api_key' => '',
        'cache_results' => true,
        'cache_time' => 86400 // 24 horas en segundos
    ]);
}

// Configuración de validación de documentos
if (!defined('DOCUMENT_VALIDATION_CONFIG')) {
    define('DOCUMENT_VALIDATION_CONFIG', [
        'enabled' => true,
        'max_size' => 5242880, // 5MB en bytes
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf'],
        'ocr_enabled' => false,
        'face_recognition' => false
    ]);
}

// Configuración de integración
if (!defined('INTEGRATION_CONFIG')) {
    define('INTEGRATION_CONFIG', [
        'enabled' => false,
        'providers' => [],
        'webhooks' => [],
        'api_keys' => []
    ]);
}

// Configuración de estadísticas
if (!defined('STATS_CONFIG')) {
    define('STATS_CONFIG', [
        'enabled' => true,
        'track_visitors' => true,
        'track_actions' => true,
        'track_errors' => true,
        'retention' => 90, // días
        'anonymize' => true
    ]);
}

// Configuración de reportes
if (!defined('REPORT_CONFIG')) {
    define('REPORT_CONFIG', [
        'enabled' => true,
        'templates_path' => __DIR__ . '/../views/reports',
        'formats' => ['pdf', 'xlsx', 'csv'],
        'schedule' => false,
        'notify_admins' => true
    ]);
}

// Configuración de impresión
if (!defined('PRINT_CONFIG')) {
    define('PRINT_CONFIG', [
        'enabled' => true,
        'templates_path' => __DIR__ . '/../views/prints',
        'paper_size' => 'A4',
        'orientation' => 'portrait',
        'margin' => '10mm'
    ]);
}

// Configuración de internacionalización
if (!defined('I18N_CONFIG')) {
    define('I18N_CONFIG', [
        'default_language' => 'es',
        'available_languages' => ['es', 'en'],
        'detect_browser_language' => true,
        'fallback_language' => 'en'
    ]);
}

// Configuración de actualizaciones
if (!defined('UPDATE_CONFIG')) {
    define('UPDATE_CONFIG', [
        'auto_check' => true,
        'check_interval' => 86400, // 24 horas en segundos
        'auto_update' => false,
        'backup_before_update' => true,
        'notify_admins' => true
    ]);
}

// Configuración de mantenimiento
if (!defined('MAINTENANCE_CONFIG')) {
    define('MAINTENANCE_CONFIG', [
        'enabled' => false,
        'allowed_ips' => ['127.0.0.1'],
        'message' => 'El sistema está en mantenimiento. Por favor, intente más tarde.',
        'end_time' => null
    ]);
}

// Configuración de SEO
if (!defined('SEO_CONFIG')) {
    define('SEO_CONFIG', [
        'meta_description' => 'Sistema de Votación Electrónica Profesional',
        'meta_keywords' => 'votación, elecciones, sistema electrónico',
        'robots' => 'noindex, nofollow',
        'sitemap' => false
    ]);
}

// Configuración de rendimiento
if (!defined('PERFORMANCE_CONFIG')) {
    define('PERFORMANCE_CONFIG', [
        'minify_css' => true,
        'minify_js' => true,
        'compress_html' => true,
        'cache_static' => true,
        'lazy_loading' => true
    ]);
}

// Configuración de accesibilidad
if (!defined('ACCESSIBILITY_CONFIG')) {
    define('ACCESSIBILITY_CONFIG', [
        'high_contrast' => true,
        'font_size' => true,
        'screen_reader' => true,
        'keyboard_navigation' => true
    ]);
}

// Configuración de temas
if (!defined('THEME_CONFIG')) {
    define('THEME_CONFIG', [
        'default' => 'light',
        'available' => ['light', 'dark'],
        'custom_colors' => true,
        'allow_user_theme' => true
    ]);
}

// Configuración de auditoría
if (!defined('AUDIT_CONFIG')) {
    define('AUDIT_CONFIG', [
        'enabled' => true,
        'log_actions' => true,
        'log_changes' => true,
        'log_access' => true,
        'retention' => 365, // días
        'notify_admins' => true
    ]);
}

// Configuración de monitoreo
if (!defined('MONITORING_CONFIG')) {
    define('MONITORING_CONFIG', [
        'enabled' => true,
        'check_interval' => 300, // 5 minutos en segundos
        'metrics' => ['cpu', 'memory', 'disk', 'network'],
        'alert_threshold' => 80, // porcentaje
        'notify_admins' => true
    ]);
}

// Configuración de respaldo
if (!defined('BACKUP_CONFIG')) {
    define('BACKUP_CONFIG', [
        'enabled' => true,
        'frequency' => 'daily', // daily, weekly, monthly
        'retention' => 30, // días
        'path' => __DIR__ . '/../backups',
        'compress' => true,
        'notify' => true
    ]);
}

// Configuración de exportación
if (!defined('EXPORT_CONFIG')) {
    define('EXPORT_CONFIG', [
        'formats' => ['csv', 'xlsx', 'pdf'],
        'max_rows' => 10000,
        'compression' => true,
        'templates_path' => __DIR__ . '/../views/exports'
    ]);
}

// Configuración de validación
if (!defined('VALIDATION_CONFIG')) {
    define('VALIDATION_CONFIG', [
        'document_types' => ['dni', 'passport', 'license'],
        'min_name_length' => 2,
        'max_name_length' => 50,
        'phone_format' => '/^\+?[1-9]\d{1,14}$/',
        'document_format' => '/^[A-Za-z0-9]+$/'
    ]);
}

// Configuración de notificaciones
if (!defined('NOTIFICATION_CONFIG')) {
    define('NOTIFICATION_CONFIG', [
        'email' => true,
        'sms' => false,
        'push' => false,
        'templates_path' => __DIR__ . '/../views/notifications'
    ]);
}

// Configuración de votación
if (!defined('VOTING_CONFIG')) {
    define('VOTING_CONFIG', [
        'min_voters' => 10,
        'max_candidates' => 20,
        'voting_duration' => 86400, // 24 horas en segundos
        'results_public' => true,
        'allow_abstention' => true,
        'require_verification' => true,
        'ip_check' => true,
        'device_check' => true
    ]);
}

// Configuración de API
if (!defined('API_CONFIG')) {
    define('API_CONFIG', [
        'enabled' => true,
        'rate_limit' => 100, // peticiones por minuto
        'token_lifetime' => 3600, // 1 hora en segundos
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
        'allowed_headers' => ['Content-Type', 'Authorization']
    ]);
}

// Configuración de caché
if (!defined('CACHE_CONFIG')) {
    define('CACHE_CONFIG', [
        'enabled' => true,
        'driver' => 'file', // file, redis, memcached
        'path' => __DIR__ . '/../cache',
        'lifetime' => 3600, // 1 hora en segundos
        'prefix' => 'votacion_pro_'
    ]);
}

// Configuración de logging
if (!defined('LOG_CONFIG')) {
    define('LOG_CONFIG', [
        'enabled' => true,
        'path' => __DIR__ . '/../logs',
        'level' => 'error', // debug, info, warning, error, critical
        'max_files' => 5,
        'max_size' => 5242880 // 5MB en bytes
    ]);
}

// Configuración de la aplicación
if (!defined('APP_CONFIG')) {
    define('APP_CONFIG', [
        'name' => 'Sistema de Votación Pro',
        'version' => '1.0.0',
        'debug' => false,
        'maintenance_mode' => false,
        'timezone' => 'America/Mexico_City',
        'locale' => 'es_MX',
        'upload_max_size' => 5242880, // 5MB en bytes
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf'],
        'session_name' => 'votacion_pro_session',
        'cookie_lifetime' => 86400, // 24 horas
        'cookie_path' => '/',
        'cookie_domain' => '',
        'cookie_secure' => true,
        'cookie_httponly' => true
    ]);
}

// Configuración de correo mejorada
if (!defined('MAIL_CONFIG')) {
    define('MAIL_CONFIG', [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'noreply@example.com',
        'password' => 'tu-contraseña',
        'from_address' => 'noreply@example.com',
        'from_name' => 'Sistema de Votación Pro',
        'encryption' => 'tls',
        'auth_mode' => 'login'
    ]);
}

// Configuración de seguridad adicional
if (!defined('SECURITY_CONFIG')) {
    define('SECURITY_CONFIG', [
        'password_min_length' => 8,
        'password_require_special' => true,
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutos en segundos
        'session_lifetime' => 3600, // 1 hora en segundos
        'csrf_token_lifetime' => 7200, // 2 horas en segundos
        'remember_me_lifetime' => 2592000, // 30 días en segundos
    ]);
}

// Configuración de seguridad
if (!defined('SECRET_CODE')) {
    define('SECRET_CODE', 'MASTER2023');
}

// Configuración de la base de datos
require_once __DIR__ . '/dbconfig.php';

// Zona horaria
if (!function_exists('date_default_timezone_set') || date_default_timezone_get() != 'America/Mexico_City') {
    date_default_timezone_set('America/Mexico_City');
}

// Otras configuraciones
if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}
?>
