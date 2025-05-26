<?php
namespace Models;
require_once __DIR__ . '/Database.php';
use Models\Database;

class SystemConfig {
    private $conn;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    // Obtener todas las configuraciones
    public function getAllConfigs() {
        try {
            $query = "SELECT * FROM system_config";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $configs = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $configs[$row['config_key']] = $row['config_value'];
            }
            
            return [
                'success' => true,
                'configs' => $configs
            ];
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener configuraciones: ' . $e->getMessage()
            ];
        }
    }
    
    // Actualizar una configuración
    public function updateConfig($key, $value) {
        try {
            // Verificar si la configuración existe
            $query = "SELECT COUNT(*) FROM system_config WHERE config_key = :key";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                // Actualizar configuración existente
                $query = "UPDATE system_config SET config_value = :value WHERE config_key = :key";
            } else {
                // Crear nueva configuración
                $query = "INSERT INTO system_config (config_key, config_value) VALUES (:key, :value)";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            
            return [
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ];
        } catch (\PDOException $e) {
            // Registrar error detallado en log temporal
            $errorMessage = "Error PDO en updateConfig: " . $e->getMessage() . "\n";
            $logFile = dirname(__DIR__) . '/debug_logs/db_error.log';
            file_put_contents($logFile, $errorMessage, FILE_APPEND);

            // También registrar con error_log estándar por si acaso
            error_log("Error PDO en updateConfig: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al actualizar configuración: ' . $e->getMessage()
            ];
        }
    }
    
    // Obtener una configuración específica
    public function getConfig($key, $default = '') {
        try {
            $query = "SELECT config_value FROM system_config WHERE config_key = :key";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
            
            if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return $row['config_value'];
            }
            
            return $default;
        } catch (\PDOException $e) {
            return $default;
        }
    }
}