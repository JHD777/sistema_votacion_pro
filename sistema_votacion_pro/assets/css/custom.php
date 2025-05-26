<?php
// Este archivo genera CSS personalizado basado en la configuración del sistema
header('Content-Type: text/css');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/SystemController.php';

$systemController = new SystemController();
$configs = $systemController->getAllConfigs();
$configs = $configs['success'] ? $configs['configs'] : [];

// Valores por defecto
$defaults = [
    'color_primario' => '#0d6efd',
    'color_secundario' => '#6c757d',
    'color_exito' => '#198754',
    'color_peligro' => '#dc3545',
    'color_advertencia' => '#ffc107',
    'color_info' => '#0dcaf0',
    'color_fondo' => '#f8f9fa'
];

// Combinar valores por defecto con configuraciones existentes
foreach ($defaults as $key => $value) {
    if (!isset($configs[$key])) {
        $configs[$key] = $value;
    }
}
?>

:root {
    --bs-primary: <?php echo $configs['color_primario']; ?>;
    --bs-secondary: <?php echo $configs['color_secundario']; ?>;
    --bs-success: <?php echo $configs['color_exito']; ?>;
    --bs-danger: <?php echo $configs['color_peligro']; ?>;
    --bs-warning: <?php echo $configs['color_advertencia']; ?>;
    --bs-info: <?php echo $configs['color_info']; ?>;
    --bs-light: <?php echo $configs['color_fondo']; ?>;
}

/* Sobrescribir colores de Bootstrap */
.bg-primary {
    background-color: var(--bs-primary) !important;
}
.bg-secondary {
    background-color: var(--bs-secondary) !important;
}
.bg-success {
    background-color: var(--bs-success) !important;
}
.bg-danger {
    background-color: var(--bs-danger) !important;
}
.bg-warning {
    background-color: var(--bs-warning) !important;
}
.bg-info {
    background-color: var(--bs-info) !important;
}
.bg-light {
    background-color: var(--bs-light) !important;
}

/* Botones */
.btn-primary {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}
.btn-secondary {
    background-color: var(--bs-secondary);
    border-color: var(--bs-secondary);
}
.btn-success {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}
.btn-danger {
    background-color: var(--bs-danger);
    border-color: var(--bs-danger);
}
.btn-warning {
    background-color: var(--bs-warning);
    border-color: var(--bs-warning);
}
.btn-info {
    background-color: var(--bs-info);
    border-color: var(--bs-info);
}

/* Textos */
.text-primary {
    color: var(--bs-primary) !important;
}
.text-secondary {
    color: var(--bs-secondary) !important;
}
.text-success {
    color: var(--bs-success) !important;
}
.text-danger {
    color: var(--bs-danger) !important;
}
.text-warning {
    color: var(--bs-warning) !important;
}
.text-info {
    color: var(--bs-info) !important;
}

/* Alertas */
.alert-primary {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}
.alert-secondary {
    background-color: var(--bs-secondary);
    border-color: var(--bs-secondary);
    color: white;
}
.alert-success {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
    color: white;
}
.alert-danger {
    background-color: var(--bs-danger);
    border-color: var(--bs-danger);
    color: white;
}
.alert-warning {
    background-color: var(--bs-warning);
    border-color: var(--bs-warning);
    color: black;
}
.alert-info {
    background-color: var(--bs-info);
    border-color: var(--bs-info);
    color: white;
}

/* Estilos personalizados adicionales */
body {
    background-color: var(--bs-light);
}