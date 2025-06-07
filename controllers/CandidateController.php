<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Candidato.php';
require_once __DIR__ . '/../models/Voto.php';

use Models\Candidato;

class CandidateController {
    // Crear un nuevo candidato
    public function createCandidate($data) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['admin_id'])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ];
        }
        
        // Validar datos
        if (empty($data['nombre']) || empty($data['apellido']) || empty($data['eleccion_id'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos marcados son obligatorios'
            ];
        }
        
        // Crear candidato
        $candidato = new Candidato();
        $candidato_id = $candidato->create($data);
        
        if ($candidato_id) {
            return [
                'success' => true,
                'message' => 'Candidato creado correctamente',
                'candidato_id' => $candidato_id
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al crear el candidato. Por favor, intenta nuevamente.'
        ];
    }
    
    // Actualizar candidato
    public function updateCandidate($id, $data) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['admin_id'])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ];
        }
        
        // Validar datos
        if (empty($data['nombre']) || empty($data['apellido']) || empty($data['eleccion_id'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos marcados son obligatorios'
            ];
        }
        
        // Verificar si el candidato existe
        $candidato = new Candidato();
        $candidato_data = $candidato->getById($id);
        
        if (!$candidato_data) {
            return [
                'success' => false,
                'message' => 'El candidato no existe'
            ];
        }
        
        // Actualizar candidato
        if ($candidato->update($id, $data)) {
            return [
                'success' => true,
                'message' => 'Candidato actualizado correctamente'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al actualizar el candidato. Por favor, intenta nuevamente.'
        ];
    }
    
    // Eliminar candidato
    public function deleteCandidate($id) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['admin_id'])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ];
        }
            
        // Verificar si el candidato existe
        $candidato = new Candidato();
        $candidato_data = $candidato->getById($id);
        
        if (!$candidato_data) {
            return [
                'success' => false,
                'message' => 'El candidato no existe'
            ];
        }
        
        // Verificar si el candidato tiene votos
        $voto = new Voto();
        $query = "SELECT COUNT(*) as total FROM votos WHERE candidato_id = :candidato_id";
        
        $stmt = $voto->getConnection()->prepare($query);
        $stmt->bindParam(':candidato_id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar un candidato que ya tiene votos registrados'
            ];
        }
        
        // Eliminar candidato
        if ($candidato->delete($id)) {
            return [
                'success' => true,
                'message' => 'Candidato eliminado correctamente'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al eliminar el candidato. Por favor, intenta nuevamente.'
        ];
    }
    
    // Obtener todos los candidatos
    public function getAllCandidates() {
        $candidato = new Candidato();
        return $candidato->getAll();
    }
    
    // Obtener candidato por ID
    public function getCandidateById($id) {
        $candidato = new Candidato();
        return $candidato->getById($id);
    }
    
    // Obtener candidatos por elección
    public function getCandidatesByElection($eleccion_id) {
        $candidato = new Candidato();
        $query = "SELECT * FROM candidatos WHERE eleccion_id = :eleccion_id ORDER BY numero ASC";
        $stmt = $candidato->getConnection()->prepare($query);
        $stmt->bindParam(':eleccion_id', $eleccion_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>