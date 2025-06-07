<?php
require_once __DIR__ . '/Database.php';

use Models\Database;

class Configuracion {
    private $conn;
    private $table_name = "configuracion";
    private $database;
    
    // Constructor
    public function __construct() {
        $this->database = Database::getInstance();
    }
    
    // Obtener todas las configuraciones
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $configuraciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $configuraciones[$row['clave']] = $row['valor'];
        }
        
        return $configuraciones;
    }
    
    // Obtener una configuración específica
    public function get($clave) {
        $query = "SELECT valor FROM " . $this->table_name . " WHERE clave = :clave LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['valor'];
        }
        
        return null;
    }
    
    // Actualizar o crear una configuración
    public function set($clave, $valor) {
        // Verificar si la clave ya existe
        $query = "SELECT id FROM " . $this->table_name . " WHERE clave = :clave LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();
        
        try {
            if ($stmt->rowCount() > 0) {
                // Actualizar
                $query = "UPDATE " . $this->table_name . " SET valor = :valor WHERE clave = :clave";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':valor', $valor);
                $stmt->bindParam(':clave', $clave);
                
                return $stmt->execute();
            } else {
                // Crear
                $query = "INSERT INTO " . $this->table_name . " (clave, valor) VALUES (:clave, :valor)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':clave', $clave);
                $stmt->bindParam(':valor', $valor);
                
                return $stmt->execute();
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Actualizar múltiples configuraciones a la vez
    public function setMultiple($configuraciones) {
        $this->conn->beginTransaction();
        
        try {
            foreach ($configuraciones as $clave => $valor) {
                $this->set($clave, $valor);
            }
            
            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>