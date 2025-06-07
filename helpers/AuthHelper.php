<?php
class AuthHelper {
    public static function verificarAdmin() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
            echo '<div style="padding:2rem;text-align:center;color:red;font-weight:bold;">Sesión de administrador no activa o expirada. Por favor, vuelve a iniciar sesión.</div>';
            exit;
        }
    }
    
    public static function verificarUsuario() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
    
    public static function verificarUsuarioVerificado() {
        if (!isset($_SESSION['user_id']) || $_SESSION['verificado'] != 1) {
            header('Location: ' . BASE_URL . '/perfil?msg=no_verificado');
            exit;
        }
    }
    
    public static function verificarAdminSupremo() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin' || !isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] !== 'super') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}