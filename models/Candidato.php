<?php
namespace Models;

require_once __DIR__ . '/Database.php';

use Models\Database;

class Candidato {
    private $conn;
    private $table_name = "candidatos";
    private $database;
    
    // Constructor
    public function __construct() {
        $this->database = Database::getInstance();
        $this->conn = $this->database->getConnection();
    }
    
    // Obtener conexión
    public function getConnection() {
        return $this->conn;
    }
    
    // Crear un nuevo candidato
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, apellido, biografia, foto, eleccion_id, numero, activo, fecha_creacion) 
                  VALUES (:nombre, :apellido, :biografia, :foto, :eleccion_id, :numero, :activo, NOW())";
        
        try {
            // Obtener el siguiente número disponible para la elección
            $numero = $this->getNextNumero($data['eleccion_id']);
            
            $stmt = $this->conn->prepare($query);
            
            // Vincular parámetros
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':biografia', $data['biografia']);
            $stmt->bindParam(':eleccion_id', $data['eleccion_id']);
            $stmt->bindParam(':numero', $numero);
            
            // Valores por defecto
            $activo = 1;
            $stmt->bindParam(':activo', $activo);
            
            // Foto (opcional)
            $foto = isset($data['foto']) ? $data['foto'] : null;
            $stmt->bindParam(':foto', $foto);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Obtener el siguiente número disponible para un candidato en una elección
    private function getNextNumero($eleccion_id) {
        $query = "SELECT MAX(numero) as max_numero FROM " . $this->table_name . " WHERE eleccion_id = :eleccion_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':eleccion_id', $eleccion_id);
        $stmt->execute();
        
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return ($row['max_numero'] ? $row['max_numero'] + 1 : 1);
    }
    
    // Actualizar candidato
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, apellido = :apellido, biografia = :biografia, 
                  eleccion_id = :eleccion_id";
        
        // Si se proporciona una foto, actualizarla
        if (isset($data['foto']) && !empty($data['foto'])) {
            $query .= ", foto = :foto";
        }
        
        $query .= " WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular parámetros
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':biografia', $data['biografia']);
            $stmt->bindParam(':eleccion_id', $data['eleccion_id']);
            $stmt->bindParam(':id', $id);
            
            // Si se proporciona una foto, vincularla
            if (isset($data['foto']) && !empty($data['foto'])) {
                $stmt->bindParam(':foto', $data['foto']);
            }
            
            return $stmt->execute();
        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Eliminar candidato
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Obtener candidato por ID
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
    
    // Obtener todos los candidatos
    public function getAll() {
        $query = "SELECT c.*, e.titulo as eleccion_titulo 
                  FROM " . $this->table_name . " c
                  JOIN elecciones e ON c.eleccion_id = e.id
                  ORDER BY c.eleccion_id, c.numero";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener candidatos por ID de elección
    public function getCandidatosByElectionId($eleccion_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE eleccion_id = :eleccion_id 
                  ORDER BY numero ASC"; // O algún otro criterio de ordenación relevante
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':eleccion_id', $eleccion_id);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            error_log("Error en getCandidatosByElectionId: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener conteo de votos por candidato
    public function getVoteCount($eleccion_id) {
        $query = "SELECT c.id, c.nombre, c.apellido, c.foto, COUNT(v.id) as votos
                  FROM " . $this->table_name . " c
                  LEFT JOIN votos v ON c.id = v.candidato_id AND v.eleccion_id = :eleccion_id
                  WHERE c.eleccion_id = :eleccion_id
                  GROUP BY c.id
                  ORDER BY votos DESC, c.numero ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':eleccion_id', $eleccion_id);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Obtener el total de candidatos
    public function getTotalCandidates() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['total'];
        } catch(\PDOException $e) {
            error_log("Error en getTotalCandidates: " . $e->getMessage());
            return 0;
        }
    }
}
?>