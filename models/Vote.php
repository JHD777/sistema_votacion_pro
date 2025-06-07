<?php

namespace Models;

require_once __DIR__ . '/Database.php';
use Models\Database;

class Vote {
    private $conn;
    private $table_name = "votos";
    private $database;
    
    // Constructor
    public function __construct() {
        $this->database = Database::getInstance();
        $this->conn = $this->database->getConnection();
    }
    
    // Método para obtener el total de votos
    public function getTotalVotes() {
        $query = "SELECT COUNT(*) as total FROM votos";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (\PDOException $e) {
            error_log("Error en getTotalVotes: " . $e->getMessage());
            return 0;
        }
    }
    
    // Otros métodos para gestionar votos...

    // Método para verificar si un usuario ya votó en una elección específica
    public function userHasVoted($user_id, $eleccion_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE usuario_id = :usuario_id AND eleccion_id = :eleccion_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':eleccion_id', $eleccion_id, \PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (\PDOException $e) {
            error_log("Error en userHasVoted: " . $e->getMessage());
            return false;
        }
    }

    // Método para registrar un voto
    public function registerVote($user_id, $eleccion_id, $candidato_id) {
        $query = "INSERT INTO " . $this->table_name . " (usuario_id, eleccion_id, candidato_id, fecha_voto, ip_address, user_agent) VALUES (:usuario_id, :eleccion_id, :candidato_id, NOW(), :ip_address, :user_agent)";
        try {
            $stmt = $this->conn->prepare($query);
            
            // Obtener IP y User Agent (considerar helper para esto)
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';

            $stmt->bindParam(':usuario_id', $user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':eleccion_id', $eleccion_id, \PDO::PARAM_INT);
            $stmt->bindParam(':candidato_id', $candidato_id, \PDO::PARAM_INT);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':user_agent', $user_agent);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error en registerVote: " . $e->getMessage());
            return false;
        }
    }

    // Método para obtener resultados de una elección (conteo de votos por candidato)
    public function getElectionResults($eleccion_id) {
        // Considerar un JOIN con la tabla candidatos para obtener nombres, etc.
        $query = "SELECT candidato_id, COUNT(*) as total_votos FROM " . $this->table_name . " WHERE eleccion_id = :eleccion_id GROUP BY candidato_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':eleccion_id', $eleccion_id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en getElectionResults: " . $e->getMessage());
            return [];
        }
    }

    // Método para obtener estadísticas de participación en una elección
    public function getElectionParticipationStats($eleccion_id) {
        // Total de votos en la elección
        $total_votos = $this->getElectionResults($eleccion_id) ? count($this->getElectionResults($eleccion_id)) : 0;
        
        // Corregir: Contar el número total de votos para la elección
        $queryTotalVotos = "SELECT COUNT(*) as total_votos_eleccion FROM " . $this->table_name . " WHERE eleccion_id = :eleccion_id";
        $stmtTotalVotos = $this->conn->prepare($queryTotalVotos);
        $stmtTotalVotos->bindParam(':eleccion_id', $eleccion_id, \PDO::PARAM_INT);
        $stmtTotalVotos->execute();
        $total_votos_eleccion = $stmtTotalVotos->fetch(\PDO::FETCH_ASSOC)['total_votos_eleccion'];

        // Total de usuarios verificados (necesitamos acceder al modelo User)
        // Esto requeriría una instancia del modelo User o un método estático
        // Por ahora, obtengamos el total de usuarios de la base de datos (simplificado)
        // $queryTotalUsuarios = "SELECT COUNT(*) as total_usuarios FROM usuarios WHERE verificado = 1";
        // $stmtTotalUsuarios = $this->conn->prepare($queryTotalUsuarios);
        // $stmtTotalUsuarios->execute();
        // $total_usuarios_verificados = $stmtTotalUsuarios->fetch(\PDO::FETCH_ASSOC)['total_usuarios'];

        // Obtenemos el total de usuarios verificados de AdminController o un modelo de usuario apropiado
        // Temporalmente, usaremos una consulta directa (NO es la mejor práctica a largo plazo)
        $total_usuarios_verificados = 0;
        try {
             $queryUsuarios = "SELECT COUNT(*) as total FROM usuarios WHERE verificado = 1";
             $stmtUsuarios = $this->conn->prepare($queryUsuarios);
             $stmtUsuarios->execute();
             $rowUsuarios = $stmtUsuarios->fetch(\PDO::FETCH_ASSOC);
             $total_usuarios_verificados = $rowUsuarios['total'];
        } catch (\PDOException $e) {
            error_log("Error al obtener total de usuarios verificados en VoteModel: " . $e->getMessage());
        }

        // Calcular porcentaje de participación
        $porcentaje_participacion = ($total_usuarios_verificados > 0) ? 
            round(($total_votos_eleccion / $total_usuarios_verificados) * 100, 2) : 0;

        return [
            'total_votos' => $total_votos_eleccion,
            'total_usuarios_verificados' => $total_usuarios_verificados,
            'porcentaje_participacion' => $porcentaje_participacion
        ];
    }

}
?>