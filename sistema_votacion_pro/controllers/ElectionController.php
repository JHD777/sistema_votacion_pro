<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Eleccion.php';
require_once __DIR__ . '/../models/Voto.php';

use Models\Eleccion;

class ElectionController {
    // Crear una nueva elección
    public function createElection($data) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['admin_id'])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ];
        }
        
        // Validar datos
        if (empty($data['titulo']) || empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos marcados son obligatorios'
            ];
        }
        
        // Validar fechas
        $fecha_inicio = new DateTime($data['fecha_inicio']);
        $fecha_fin = new DateTime($data['fecha_fin']);
        $now = new DateTime();
        
        if ($fecha_inicio > $fecha_fin) {
            return [
                'success' => false,
                'message' => 'La fecha de inicio debe ser anterior a la fecha de fin'
            ];
        }
        
        // Crear elección
        $eleccion = new Eleccion();
        $eleccion_id = $eleccion->create($data);
        
        if ($eleccion_id) {
            return [
                'success' => true,
                'message' => 'Elección creada correctamente',
                'eleccion_id' => $eleccion_id
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al crear la elección. Por favor, intenta nuevamente.'
        ];
    }
    
    // Actualizar elección
    public function updateElection($id, $data) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['admin_id'])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ];
        }
        
        // Validar datos
        if (empty($data['titulo']) || empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos marcados son obligatorios'
            ];
        }
        
        // Validar fechas
        $fecha_inicio = new DateTime($data['fecha_inicio']);
        $fecha_fin = new DateTime($data['fecha_fin']);
        
        if ($fecha_inicio > $fecha_fin) {
            return [
                'success' => false,
                'message' => 'La fecha de inicio debe ser anterior a la fecha de fin'
            ];
        }
        
        // Verificar si la elección existe
        $eleccion = new Eleccion();
        $eleccion_data = $eleccion->getById($id);
        
        if (!$eleccion_data) {
            return [
                'success' => false,
                'message' => 'La elección no existe'
            ];
        }
        
        // Verificar si la elección tiene votos
        $voto = new Voto();
        if ($voto->eleccionTieneVotos($id)) {
            return [
                'success' => false,
                'message' => 'No se puede modificar una elección que ya tiene votos registrados'
            ];
        }
        
        // Actualizar elección
        if ($eleccion->update($id, $data)) {
            return [
                'success' => true,
                'message' => 'Elección actualizada correctamente'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al actualizar la elección. Por favor, intenta nuevamente.'
        ];
    }
    
    // Eliminar elección
    public function deleteElection($id) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['admin_id'])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ];
        }
        
        // Verificar si la elección existe
        $eleccion = new Eleccion();
        $eleccion_data = $eleccion->getById($id);
        
        if (!$eleccion_data) {
            return [
                'success' => false,
                'message' => 'La elección no existe'
            ];
        }
        
        // Verificar si la elección tiene votos
        $voto = new Voto();
        if ($voto->eleccionTieneVotos($id)) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar una elección que ya tiene votos registrados'
            ];
        }
        
        // Eliminar elección
        if ($eleccion->delete($id)) {
            return [
                'success' => true,
                'message' => 'Elección eliminada correctamente'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al eliminar la elección. Por favor, intenta nuevamente.'
        ];
    }
    
    // Obtener todas las elecciones
    public function getAllElections() {
        $eleccion = new Eleccion();
        return $eleccion->getAll();
    }
    
    // Obtener elección por ID
    public function getElectionById($id) {
        $eleccion = new Eleccion();
        return $eleccion->getById($id);
    }
    
    // Obtener elecciones actuales
    public function getCurrentElections() {
        $eleccion = new Eleccion();
        return $eleccion->getCurrentElections();
    }
    
    // Obtener elecciones próximas
    public function getUpcomingElections() {
        $eleccion = new Eleccion();
        return $eleccion->getUpcomingElections();
    }

    // Obtener el número de candidatos por elección
    public function getCandidatesCount($eleccion_id) {
        $eleccion = new Eleccion();
        return $eleccion->getCandidatesCount($eleccion_id);
    }
}
?>