<?php

namespace Models;

require_once __DIR__ . '/Database.php';

use Models\Database;

class Eleccion {
    private $conn;
    private $table_name = "elecciones";
    private $database;
    
    // Constructor
    public function __construct() {
        $this->database = Database::getInstance();
        $this->conn = $this->database->getConnection();
    }
    
    // Crear una nueva elección
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (titulo, descripcion, fecha_inicio, fecha_fin, activo, fecha_creacion) 
                  VALUES (:titulo, :descripcion, :fecha_inicio, :fecha_fin, :activo, NOW())";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular parámetros
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':fecha_inicio', $data['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $data['fecha_fin']);
            $stmt->bindParam(':activo', $data['activo']);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(\PDOException $e) {
            error_log("Error en create: " . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar elección
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET titulo = :titulo, descripcion = :descripcion, fecha_inicio = :fecha_inicio, 
                  fecha_fin = :fecha_fin, activo = :activo 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular parámetros
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':fecha_inicio', $data['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $data['fecha_fin']);
            $stmt->bindParam(':activo', $data['activo']);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(\PDOException $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar elección
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(\PDOException $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener elección por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener todas las elecciones
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_creacion DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener elecciones actuales (en curso)
    public function getCurrentElections() {
        $now = date('Y-m-d H:i:s');
        
        // Debugging: Mostrar la hora utilizada en la consulta
        error_log("Debug: Hora utilizada en getCurrentElections: " . $now);
        
        // *** TACTICA DE DEBUG TEMPORAL: Insertar valor directamente en la consulta ***
        // WARNING: NO HACER ESTO CON ENTRADA DE USUARIO - RIESGO DE SQL INJECTION
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE fecha_inicio <= '{$now}' AND fecha_fin >= '{$now}' AND activo = 1 
                  ORDER BY fecha_inicio ASC";
        
        // Debugging: Mostrar la consulta que se ejecutará
        error_log("Debug: Consulta SQL (sin preparar) en getCurrentElections: " . $query);

        try {
            // Ejecutar la consulta directamente (sin prepare ni bindParam)
            $stmt = $this->conn->query($query);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Debugging: Mostrar los resultados obtenidos de la base de datos
            error_log("Debug: Resultados de getCurrentElections: " . print_r($results, true));
            
            return $results;
        } catch(\PDOException $e) {
            error_log("Error en getCurrentElections: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener elecciones próximas
    public function getUpcomingElections() {
        $now = date('Y-m-d H:i:s');
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE fecha_inicio > :now AND activo = 1 
                  ORDER BY fecha_inicio ASC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':now', $now);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            error_log("Error en getUpcomingElections: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener elecciones pasadas
    public function getPastElections() {
        $now = date('Y-m-d H:i:s');
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE fecha_fin < :now 
                  ORDER BY fecha_fin DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':now', $now);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            error_log("Error en getPastElections: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener el total de elecciones
    public function getTotalElections() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['total'];
        } catch(\PDOException $e) {
            error_log("Error en getTotalElections: " . $e->getMessage());
            return 0;
        }
    }

    // Obtener el número de candidatos por elección
    public function getCandidatesCount($eleccion_id) {
        $query = "SELECT COUNT(*) as total FROM candidatos WHERE eleccion_id = :eleccion_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':eleccion_id', $eleccion_id);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['total'];
        } catch(\PDOException $e) {
            error_log("Error en getCandidatesCount: " . $e->getMessage());
            return 0;
        }
    }
}
?>