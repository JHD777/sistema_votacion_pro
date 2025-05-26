<?php
namespace Models;

use PDO;
use PDOException;
use Exception;

class Database {
    private $connection = null;
    private static $instance = null;
    
    private function __construct() {
        // El constructor está vacío ya que la conexión se establece en getConnection
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Obtener conexión a la base de datos
    public function getConnection() {
        if ($this->connection === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                    DB_CONFIG['host'],
                    DB_CONFIG['port'],
                    DB_CONFIG['database'],
                    DB_CONFIG['charset']
                );
                
                $this->connection = new PDO(
                    $dsn,
                    DB_CONFIG['username'],
                    DB_CONFIG['password'],
                    DB_CONFIG['options']
                );
                
                // Configurar el modo de error
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Configurar el modo de fetch por defecto
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Desactivar la emulación de prepared statements
                $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                
                // Establecer la zona horaria de la conexión si APP_TIMEZONE está definida
                if (defined('APP_TIMEZONE')) {
                    $timezone = APP_TIMEZONE;
                    // Para MySQL, se usa SET time_zone
                    $this->connection->exec("SET time_zone='{$timezone}'");
                    error_log("Debug: Zona horaria de la conexión a DB establecida a: " . $timezone);
                }

            } catch (PDOException $e) {
                // Log del error
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                
                // En producción, mostrar un mensaje genérico
                if (!defined('APP_DEBUG') || !APP_DEBUG) {
                    throw new Exception("Error de conexión a la base de datos. Por favor, contacte al administrador.");
                }
                
                // En desarrollo, mostrar el error completo
                throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        
        return $this->connection;
    }
    
    // Cerrar la conexión
    public function closeConnection() {
        $this->connection = null;
    }
    
    // Destructor para asegurar que la conexión se cierre
    public function __destruct() {
        $this->closeConnection();
    }
}
?>