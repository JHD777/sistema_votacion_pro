<?php
// Establecer el tipo de contenido como CSS
header('Content-Type: text/css');

// Definir colores por defecto
$defaultColors = [
    'primary_color' => '#0d6efd',
    'secondary_color' => '#6c757d',
    'accent_color' => '#ffc107',
    'text_color' => '#212529',
    'background_color' => '#ffffff'
];

// Intentar cargar configuraciones desde la base de datos
try {
    require_once dirname(__DIR__) . '/config/Database.php';
    require_once dirname(__DIR__) . '/controllers/SettingsController.php';
    
    $settingsController = new SettingsController();
    
    // Obtener configuraciones con valores por defecto
    $primaryColor = $settingsController->getSetting('primary_color', $defaultColors['primary_color']);
    $secondaryColor = $settingsController->getSetting('secondary_color', $defaultColors['secondary_color']);
    $accentColor = $settingsController->getSetting('accent_color', $defaultColors['accent_color']);
    $textColor = $settingsController->getSetting('text_color', $defaultColors['text_color']);
    $backgroundColor = $settingsController->getSetting('background_color', $defaultColors['background_color']);
} catch (Exception $e) {
    // Si hay error, usar colores por defecto
    $primaryColor = $defaultColors['primary_color'];
    $secondaryColor = $defaultColors['secondary_color'];
    $accentColor = $defaultColors['accent_color'];
    $textColor = $defaultColors['text_color'];
    $backgroundColor = $defaultColors['background_color'];
}

// Calcular colores derivados para hover y focus
function darkenColor($hex, $percent) {
    $hex = ltrim($hex, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r - $r * $percent / 100));
    $g = max(0, min(255, $g - $g * $percent / 100));
    $b = max(0, min(255, $b - $b * $percent / 100));
    
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

$primaryColorDark = darkenColor($primaryColor, 20);
$secondaryColorDark = darkenColor($secondaryColor, 20);
$accentColorDark = darkenColor($accentColor, 20);

// Generar CSS
?>
<?php
:root {
    --primary-color: <?php echo $primaryColor; ?>;
    --secondary-color: <?php echo $secondaryColor; ?>;
    --accent-color: <?php echo $accentColor; ?>;
    --text-color: <?php echo $textColor; ?>;
    --background-color: <?php echo $backgroundColor; ?>;
    --primary-color-dark: <?php echo $primaryColorDark; ?>;
    --secondary-color-dark: <?php echo $secondaryColorDark; ?>;
    --accent-color-dark: <?php echo $accentColorDark; ?>;
}

/* Estilos generales */
body {
    color: var(--text-color);
    background-color: var(--background-color);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Botones primarios */
.btn-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    color: white !important;
}

.btn-primary:hover, .btn-primary:focus {
    background-color: var(--primary-color-dark) !important;
    border-color: var(--primary-color-dark) !important;
}

/* Botones secundarios */
.btn-secondary {
    background-color: var(--secondary-color) !important;
    border-color: var(--secondary-color) !important;
    color: white !important;
}

.btn-secondary:hover, .btn-secondary:focus {
    background-color: var(--secondary-color-dark) !important;
    border-color: var(--secondary-color-dark) !important;
}

/* Botones de acento */
.btn-accent {
    background-color: var(--accent-color) !important;
    border-color: var(--accent-color) !important;
    color: black !important;
}

.btn-accent:hover, .btn-accent:focus {
    background-color: var(--accent-color-dark) !important;
    border-color: var(--accent-color-dark) !important;
}

/* Fondos */
.bg-primary {
    background-color: var(--primary-color) !important;
}

.bg-secondary {
    background-color: var(--secondary-color) !important;
}

.bg-accent {
    background-color: var(--accent-color) !important;
}

/* Enlaces */
a {
    color: var(--primary-color);
    transition: color 0.3s ease;
}

a:hover {
    color: var(--primary-color-dark);
    text-decoration: none;
}

/* Navbar */
.navbar-dark.bg-primary {
    background-color: var(--primary-color) !important;
}

.navbar-dark .navbar-nav .nav-link {
    color: rgba(255, 255, 255, 0.85);
}

.navbar-dark .navbar-nav .nav-link:hover {
    color: rgba(255, 255, 255, 1);
}

/* Cards */
.card {
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    border: none;
}

.card-header.bg-primary {
    background-color: var(--primary-color) !important;
    color: white;
    border-radius: 8px 8px 0 0;
}

/* Formularios */
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Tablas */
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

/* Alertas */
.alert-primary {
    background-color: rgba(13, 110, 253, 0.15);
    border-color: rgba(13, 110, 253, 0.2);
    color: var(--primary-color-dark);
}

/* Badges */
.badge.bg-primary {
    background-color: var(--primary-color) !important;
}

.badge.bg-secondary {
    background-color: var(--secondary-color) !important;
}

.badge.bg-accent {
    background-color: var(--accent-color) !important;
    color: black;
}

/* Mejoras de accesibilidad */
.btn:focus, .form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.5);
    outline: 0;
}

/* Mejoras para dispositivos móviles */
@media (max-width: 768px) {
    .card {
        margin-bottom: 15px;
    }
    
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
}
?>