<?php
// Configuración específica para AeonFree
return [
    'base_url' => 'https://' . $_SERVER['HTTP_HOST'],
    'upload_path' => 'uploads/',
    'max_upload_size' => 10 * 1024 * 1024, // 10MB
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
    'session_timeout' => 3600, // 1 hora
    'timezone' => 'America/La_Paz',
    'debug' => false,
    'maintenance_mode' => false
]; 