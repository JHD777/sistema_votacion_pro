<?php
// Incluir la configuración de la base de datos desde dbconfig.php
require_once __DIR__ . '/dbconfig.php';

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'tu_base_de_datos',
    'username' => getenv('DB_USER') ?: 'tu_usuario',
    'password' => getenv('DB_PASS') ?: 'tu_contraseña',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];