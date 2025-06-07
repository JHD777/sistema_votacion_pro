<?php
// Verificamos que el código sea correcto
if (isset($_GET['code'])) {
    // Verificar que SECRET_CODE esté definido
    if (!defined('SECRET_CODE')) {
        define('SECRET_CODE', 'MASTER2023');
    }
    
    if ($_GET['code'] === SECRET_CODE) {
        // No iniciamos una nueva sesión si ya hay una activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Establecemos la sesión de administrador supremo
        $_SESSION['admin_id'] = 0; // ID especial para administrador supremo
        $_SESSION['admin_name'] = 'Administrador Supremo';
        $_SESSION['admin_rol'] = 'super'; // Rol de super administrador
        $_SESSION['is_supreme'] = true;
        $_SESSION['user_type'] = 'admin'; // Importante para la verificación en otras partes
        
        // Redirigimos al panel de administración
        echo "<script>window.location.href = '".BASE_URL."?view=admin/dashboard';</script>";
        exit;
    }
}

// Si el código es incorrecto o no se proporcionó, redirigimos a la página principal
echo "<script>window.location.href = '".BASE_URL."';</script>";
exit;
?>