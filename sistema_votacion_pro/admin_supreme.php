<?php
require_once 'config/config.php';

// Verificamos que el código sea correcto
if (isset($_GET['code']) && $_GET['code'] === SECRET_CODE) {
    // Iniciamos la sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Establecemos la sesión de administrador supremo
    $_SESSION['admin_id'] = 0; // ID especial para administrador supremo
    $_SESSION['admin_name'] = 'Administrador Supremo';
    $_SESSION['admin_rol'] = 'super'; // Rol de super administrador
    $_SESSION['is_supreme'] = true;
    
    // Redirigimos al panel de administración
    header('Location: ' . BASE_URL . '?view=admin/dashboard');
    exit;
} else {
    // Si el código es incorrecto, redirigimos a la página principal
    header('Location: ' . BASE_URL);
    exit;
}
?>