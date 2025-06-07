<?php
// Decide si quieres usar Voto.php o Vote.php, pero sé consistente
require_once __DIR__ . '/../models/Vote.php'; // O Voto.php, pero no ambos
require_once __DIR__ . '/../models/Candidato.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

use Models\Candidato;

class VoteController {
    // Registrar un voto
    public function registerVote($candidato_id) {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'message' => 'Debes iniciar sesión para votar'
            ];
        }
        
        $voto = new Voto();
        
        // Verificar si el candidato existe para obtener su eleccion_id
        $candidato = new Candidato();
        $candidato_data = $candidato->getById($candidato_id);
        
        if (!$candidato_data) {
            return [
                'success' => false,
                'message' => 'El candidato seleccionado no existe'
            ];
        }
        
        // Obtener eleccion_id del candidato
        $eleccion_id = $candidato_data['eleccion_id'];

        // Verificar si el usuario ya ha votado en esta elección
        if ($voto->userHasVoted($_SESSION['user_id'], $eleccion_id)) {
            return [
                'success' => false,
                'message' => 'Ya has emitido tu voto en esta elección anteriormente' // Mensaje más específico
            ];
        }
        
        // Registrar el voto
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'candidato_id' => $candidato_id,
            'eleccion_id' => $eleccion_id,
            'fecha_voto' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];
        
        $voto_id = $voto->save($data);
        
        if ($voto_id) {
            return [
                'success' => true,
                'message' => 'Tu voto ha sido registrado correctamente',
                'voto_id' => $voto_id
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al registrar el voto. Por favor, intenta nuevamente.'
        ];
    }
    
    // Obtener resultados de la votación
    public function getResults($eleccion_id) {
        $candidato = new Candidato();
        return $candidato->getVoteCount($eleccion_id);
    }

    // Obtener resultados de la elección para el panel de administración (solución temporal)
    public function getElectionResults($eleccion_id) {
        // Obtener conexión a la base de datos
        $database = \Models\Database::getInstance();
        $conn = $database->getConnection();

        // Consulta para obtener el conteo de votos por candidato
        // *** TACTICA DE DEBUG TEMPORAL: Insertar ID directamente en la consulta ***
        // WARNING: NO HACER ESTO CON ENTRADA DE USUARIO - RIESGO DE SQL INJECTION
        $query = "SELECT c.id, c.nombre, c.apellido, c.foto, COUNT(v.id) as votos
                  FROM candidatos c
                  LEFT JOIN votos v ON c.id = v.candidato_id AND v.eleccion_id = {$eleccion_id}
                  WHERE c.eleccion_id = {$eleccion_id}
                  GROUP BY c.id
                  ORDER BY votos DESC, c.numero ASC";
        
        // Debugging: Mostrar la consulta que se ejecutará
        error_log("Debug: Consulta SQL (sin preparar) en getElectionResults: " . $query);

        try {
            // Ejecutar la consulta directamente (sin prepare ni bindParam)
            $stmt = $conn->query($query);
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Debugging: Mostrar los resultados obtenidos de la base de datos
            error_log("Debug: Resultados de getElectionResults: " . print_r($results, true));

            return $results;
        } catch(\PDOException $e) {
            error_log("Error al obtener resultados de elección en VoteController: " . $e->getMessage());
            // Retornar un array vacío en caso de error
            return [];
        }
    }
    
    // Obtener estadísticas de participación para una elección
    public function getElectionParticipationStats($eleccion_id) {
        $voto = new Voto();
        return $voto->getEstadisticasParticipacion($eleccion_id);
    }

    public function getTotalVoters($eleccion_id) {
        $voto = new Voto();
        $stats = $voto->getEstadisticasParticipacion($eleccion_id);
        return $stats['total_usuarios_verificados'] ?? 0;
    }
}
?>