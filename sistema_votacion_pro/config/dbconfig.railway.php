<?php
// Configuración de la base de datos para Railway
if (!defined('DB_CONFIG')) {
    define('DB_CONFIG', [
        'host' => getenv('RAILWAY_DATABASE_HOST') ?: 'localhost',
        'database' => getenv('RAILWAY_DATABASE_NAME') ?: 'sistema_votacion_pro',
        'username' => getenv('RAILWAY_DATABASE_USERNAME') ?: 'root',
        'password' => getenv('RAILWAY_DATABASE_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'port' => getenv('RAILWAY_DATABASE_PORT') ?: 3306,
        'strict' => true,
        'engine' => 'InnoDB',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    ]);
} 