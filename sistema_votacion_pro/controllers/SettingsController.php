<?php
// Verificamos si la sesión ya está iniciada para evitar el warning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';

class SettingsController {
    private $conn;
    private $database;
    
    public function __construct() {
        $this->database = Database::getInstance();
        $this->conn = $this->database->getConnection();
        
        // Crear tabla de configuración si no existe
        $this->createSettingsTableIfNotExists();
    }
    
    private function createSettingsTableIfNotExists() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS configuracion (
                id INT AUTO_INCREMENT PRIMARY KEY,
                clave VARCHAR(50) NOT NULL UNIQUE,
                valor TEXT NOT NULL,
                descripcion VARCHAR(255),
                fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->conn->exec($query);
            
            // Insertar configuraciones por defecto si no existen
            $this->insertDefaultSettings();
        } catch (PDOException $e) {
            // Manejar error
        }
    }
    
    private function insertDefaultSettings() {
        $defaultSettings = [
            ['site_title', 'Sistema de Votación', 'Título del sitio web'],
            ['site_description', 'Sistema de votación electrónica seguro y confiable', 'Descripción del sitio'],
            ['contact_email', 'contacto@sistema-votacion.com', 'Email de contacto'],
            ['primary_color', '#0d6efd', 'Color primario del tema'],
            ['secondary_color', '#6c757d', 'Color secundario del tema'],
            ['accent_color', '#ffc107', 'Color de acento del tema'],
            ['text_color', '#212529', 'Color del texto principal'],
            ['background_color', '#ffffff', 'Color de fondo principal'],
            ['welcome_text', 'Bienvenido al Sistema de Votación Electrónica', 'Texto de bienvenida'],
            ['footer_text', '© ' . date('Y') . ' Sistema de Votación. Todos los derechos reservados.', 'Texto del pie de página']
        ];
        
        foreach ($defaultSettings as $setting) {
            try {
                // Verificar si ya existe
                $query = "SELECT COUNT(*) as total FROM configuracion WHERE clave = :clave";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':clave', $setting[0]);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row['total'] == 0) {
                    // Insertar configuración
                    $query = "INSERT INTO configuracion (clave, valor, descripcion) VALUES (:clave, :valor, :descripcion)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':clave', $setting[0]);
                    $stmt->bindParam(':valor', $setting[1]);
                    $stmt->bindParam(':descripcion', $setting[2]);
                    $stmt->execute();
                }
            } catch (PDOException $e) {
                // Manejar error
            }
        }
    }
    
    public function getAllSettings() {
        try {
            $query = "SELECT clave, valor FROM configuracion";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $settings = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['clave']] = $row['valor'];
            }
            
            return $settings;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getSetting($key, $default = '') {
        try {
            $query = "SELECT valor FROM configuracion WHERE clave = :clave";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':clave', $key);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row ? $row['valor'] : $default;
        } catch (PDOException $e) {
            return $default;
        }
    }
    
    public function updateSetting($key, $value) {
        try {
            $query = "INSERT INTO configuracion (clave, valor) VALUES (:clave, :valor) 
                      ON DUPLICATE KEY UPDATE valor = :valor";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':clave', $key);
            $stmt->bindParam(':valor', $value);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function updateGeneralSettings($settings) {
        try {
            $success = true;
            $message = 'Configuración actualizada correctamente';
            
            foreach ($settings as $key => $value) {
                if (!$this->updateSetting($key, $value)) {
                    $success = false;
                    $message = 'Error al actualizar algunas configuraciones';
                }
            }
            
            return [
                'success' => $success,
                'message' => $message
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar la configuración: ' . $e->getMessage()
            ];
        }
    }
    
    public function getSettingsByCategory($category) {
        $settings = [];
        
        switch ($category) {
            case 'general':
                $keys = ['site_title', 'site_description', 'contact_email', 'welcome_text', 'footer_text'];
                break;
            case 'appearance':
                $keys = ['primary_color', 'secondary_color', 'accent_color', 'text_color', 'background_color'];
                break;
            default:
                $keys = [];
        }
        
        foreach ($keys as $key) {
            $settings[$key] = $this->getSetting($key);
        }
        
        return $settings;
    }
}
?>