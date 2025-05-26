<?php
require_once __DIR__ . '/Database.php';

use Models\Database;

class Voto {
    private $conn;
    private $table_name = "votos";
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
    
    // Registrar un nuevo voto
    public function save($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_id, candidato_id, eleccion_id, fecha_voto, ip_address, user_agent) 
                  VALUES (:usuario_id, :candidato_id, :eleccion_id, :fecha_voto, :ip_address, :user_agent)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular parámetros
            $stmt->bindParam(':usuario_id', $data['usuario_id']);
            $stmt->bindParam(':candidato_id', $data['candidato_id']);
            $stmt->bindParam(':eleccion_id', $data['eleccion_id']);
            $stmt->bindParam(':fecha_voto', $data['fecha_voto']);
            $stmt->bindParam(':ip_address', $data['ip_address']);
            $stmt->bindParam(':user_agent', $data['user_agent']);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Verificar si un usuario ya ha votado en una elección específica
    public function userHasVoted($userId, $eleccionId) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE usuario_id = :usuario_id AND eleccion_id = :eleccion_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $userId);
        $stmt->bindParam(':eleccion_id', $eleccionId);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Obtener voto de un usuario
    public function getByUserId($userId) {
        $query = "SELECT v.*, c.nombre as candidato_nombre 
                FROM " . $this->table_name . " v
                JOIN candidatos c ON v.candidato_id = c.id
                WHERE v.usuario_id = :usuario_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener estadísticas de participación
    public function getEstadisticasParticipacion($eleccion_id) {
        // Total de usuarios verificados
        $query_usuarios = "SELECT COUNT(*) as total FROM usuarios WHERE verificado = 1";
        $stmt_usuarios = $this->conn->prepare($query_usuarios);
        $stmt_usuarios->execute();
        $row_usuarios = $stmt_usuarios->fetch(PDO::FETCH_ASSOC);
        
        // Total de votos emitidos
        $query_votos = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE eleccion_id = :eleccion_id";
        $stmt_votos = $this->conn->prepare($query_votos);
        $stmt_votos->bindParam(':eleccion_id', $eleccion_id);
        $stmt_votos->execute();
        $row_votos = $stmt_votos->fetch(PDO::FETCH_ASSOC);
        
        // Calcular porcentaje de participación
        $total_usuarios = $row_usuarios['total'];
        $total_votos = $row_votos['total'];
        $porcentaje = $total_usuarios > 0 ? round(($total_votos / $total_usuarios) * 100, 2) : 0;
        
        return [
            'total_usuarios_verificados' => $total_usuarios,
            'votos_emitidos' => $total_votos,
            'porcentaje_participacion' => $porcentaje
        ];
    }

    // Verificar si una elección tiene votos
    public function eleccionTieneVotos($eleccion_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . "
                WHERE eleccion_id = :eleccion_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':eleccion_id', $eleccion_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }
}
?>