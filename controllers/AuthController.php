<?php
// Iniciar sesión solo si no hay una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Admin.php';

class AuthController {
    // Registrar un nuevo usuario
    public function register($data) {
        // Validar datos
        if (empty($data['nombre']) || empty($data['apellido']) || empty($data['email']) || 
            empty($data['password']) || empty($data['documento_identidad'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        // Validar email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'El formato del email no es válido'
            ];
        }

        // Validar contraseña (mínimo 8 caracteres, al menos una letra y un número)
        if (strlen($data['password']) < 8 || !preg_match('/[A-Za-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra y un número'
            ];
        }
        
        // Validar documento de identidad (solo números y letras)
        if (!preg_match('/^[A-Za-z0-9]+$/', $data['documento_identidad'])) {
            return [
                'success' => false,
                'message' => 'El documento de identidad solo debe contener letras y números'
            ];
        }
        
        // Crear usuario
        $user = new User();
        
        // Verificar si el email ya existe
        if ($user->emailExists($data['email'])) {
            return [
                'success' => false,
                'message' => 'El email ya está registrado'
            ];
        }
        
        // Verificar si el documento ya existe - corregido para usar el método correcto
        if ($user->documentoExists($data['documento_identidad'])) {
            return [
                'success' => false,
                'message' => 'El documento de identidad ya está registrado'
            ];
        }
        
        // Registrar usuario
        $result = $user->register($data);
        
        if ($result) {
            // Enviar email de verificación (implementar en producción)
            $this->sendVerificationEmail($data['email'], $result['codigo_verificacion']);
            
            return [
                'success' => true,
                'message' => 'Registro exitoso. Por favor, verifica tu correo electrónico para activar tu cuenta.',
                'user_id' => $result['user_id']
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al registrar el usuario. Por favor, intenta nuevamente.'
        ];
    }
    
    // Método para enviar email de verificación
    private function sendVerificationEmail($email, $codigo) {
        // En un entorno de desarrollo, simplemente registramos la acción
        error_log("Se enviaría un correo de verificación a $email con el código $codigo");
        
        // En producción, implementar el envío real de correo
        // Ejemplo: usar PHPMailer, SwiftMailer o la función mail() de PHP
        
        return true;
    }
    
    // Verificar cuenta de usuario
    public function verifyAccount($codigo) {
        $user = new User();
        
        if ($user->verifyAccount($codigo)) {
            return [
                'success' => true,
                'message' => 'Tu cuenta ha sido verificada correctamente. Ahora puedes iniciar sesión.'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Código de verificación inválido o expirado.'
        ];
    }
    
    // Iniciar sesión como usuario
    public function login($email, $password) {
        $user = new User();
        $result = $user->login($email, $password);
        
        if ($result['success']) {
            // Establecer variables de sesión
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_name'] = $result['user']['nombre'] . ' ' . $result['user']['apellido'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_type'] = 'user';
            
            // Actualizar último login
            $user->updateLastLogin($result['user']['id']);
            
            return [
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'user' => $result['user']
            ];
        }
        
        return $result;
    }
    
    // Iniciar sesión como administrador
    public function adminLogin($username, $password) {
        $admin = new Admin();
        $result = $admin->login($username, $password);
        
        if ($result['success']) {
            // Establecer variables de sesión
            $_SESSION['admin_id'] = $result['admin']['id'];
            $_SESSION['admin_name'] = $result['admin']['nombre'];
            $_SESSION['admin_username'] = $result['admin']['username'];
            $_SESSION['user_type'] = 'admin';
            
            // Actualizar último login
            $admin->updateLastLogin($result['admin']['id']);
            
            return [
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'admin' => $result['admin']
            ];
        }
        
        return $result;
    }
    
    // Cerrar sesión
    public function logout() {
        // Destruir todas las variables de sesión
        session_unset();
        
        // Destruir la sesión
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ];
    }
    
    // Solicitar restablecimiento de contraseña
    public function requestPasswordReset($email) {
        $user = new User();
        
        // Verificar si el email existe
        if (!$user->emailExists($email)) {
            return [
                'success' => false,
                'message' => 'El correo electrónico no está registrado en nuestro sistema'
            ];
        }

        // Generar token de restablecimiento y guardar en la base de datos
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $result = $user->updateResetToken($email, $token, $expiry);
        
        if ($result) {
            // Enviar email con el enlace de restablecimiento
            $this->sendResetEmail($email, $token);
            
            return [
                'success' => true,
                'message' => 'Se ha enviado un enlace de restablecimiento a tu correo electrónico.',
                'token' => $token
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al generar el token de restablecimiento'
        ];
    }
    
    // Método para enviar email de restablecimiento
    private function sendResetEmail($email, $token) {
        // En un entorno de desarrollo, simplemente registramos la acción
        error_log("Se enviaría un correo de restablecimiento a $email con el token $token");
        
        // En producción, implementar el envío real de correo
        // Ejemplo: usar PHPMailer, SwiftMailer o la función mail() de PHP
        
        return true;
    }
    
    // Verificar token de restablecimiento
    public function verifyResetToken($token) {
        $user = new User();
        
        if ($user->verifyResetToken($token)) {
            return [
                'success' => true,
                'message' => 'Token válido'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Token inválido o expirado'
        ];
    }
    
    // Restablecer contraseña
    public function resetPassword($token, $password, $confirm_password) {
        // Validar contraseñas
        if ($password !== $confirm_password) {
            return [
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ];
        }
        
        // Validar contraseña (mínimo 8 caracteres, al menos una letra y un número)
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra y un número'
            ];
        }
        
        $user = new User();
        $result = $user->resetPassword($token, $password);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Contraseña restablecida correctamente. Ahora puedes iniciar sesión con tu nueva contraseña.'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Token inválido o expirado'
        ];
    }
    
    // Cambiar contraseña (usuario autenticado)
    public function changePassword($current_password, $new_password, $confirm_password) {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'message' => 'Debes iniciar sesión para cambiar tu contraseña'
            ];
        }
        
        // Validar contraseñas
        if ($new_password !== $confirm_password) {
            return [
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ];
        }
        
        // Validar contraseña (mínimo 8 caracteres, al menos una letra y un número)
        if (strlen($new_password) < 8 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra y un número'
            ];
        }
        
        $user = new User();
        $result = $user->changePassword($_SESSION['user_id'], $current_password, $new_password);
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ];
        }
        
        return $result;
    }
}