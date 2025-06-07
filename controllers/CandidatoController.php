<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Candidato.php';

use Models\Candidato;

class CandidatoController {
    
    // Obtener candidatos por ID de elección
    public function getCandidatosByElectionId($eleccion_id) {
        $candidatoModel = new Candidato();
        return $candidatoModel->getCandidatosByElectionId($eleccion_id);
    }

    // Métodos placeholder para añadir, editar y eliminar candidatos

    // Añadir nuevo candidato
    public function addCandidate($data, $fileData = null) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['admin_id'])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ];
        }

        // Validar datos básicos
        if (empty($data['nombre']) || empty($data['eleccion_id'])) {
            return [
                'success' => false,
                'message' => 'El nombre del candidato y el ID de la elección son obligatorios.'
            ];
        }

        $candidatoModel = new Candidato();

        // Manejar la subida de la foto si existe
        $foto_url = null;
        if ($fileData && $fileData['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/uploads/candidates/';
            // Asegurarse de que el directorio de subida exista
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid('candidate_') . '_' . basename($fileData['name']);
            $targetFilePath = $uploadDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Validar tipo de archivo (opcional pero recomendado)
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
            if (in_array(strtolower($fileType), $allowTypes)) {
                 // Subir archivo al servidor
                if (move_uploaded_file($fileData['tmp_name'], $targetFilePath)) {
                    $foto_url = $targetFilePath; // Guardar la ruta relativa en la base de datos
                } else {
                     return [
                        'success' => false,
                        'message' => 'Error al subir la imagen del candidato.'
                    ];
                }
            } else {
                 return [
                    'success' => false,
                    'message' => 'Tipo de archivo no permitido. Solo JPG, JPEG, PNG, GIF.'
                ];
            }
        }

        // Preparar datos para el modelo
        $candidateData = [
            'eleccion_id' => $data['eleccion_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? '',
            'apellido' => $data['apellido'] ?? null, // Asumiendo que podrías tener apellido en el futuro
            'foto' => $foto_url,
             'activo' => 1, // Asumiendo que un candidato nuevo está activo por defecto
             'numero' => null // El modelo getNextNumero se encargará de esto
        ];

        // Llamar al modelo para crear el candidato
        $candidato_id = $candidatoModel->create($candidateData);

        if ($candidato_id) {
            return [
                'success' => true,
                'message' => 'Candidato añadido correctamente.',
                'candidato_id' => $candidato_id
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al añadir el candidato. Por favor, intenta nuevamente.'
        ];
    }

    // Editar candidato
    public function updateCandidate($id, $data) {
         // Implementar lógica para actualizar candidato
        return ['success' => false, 'message' => 'Método updateCandidate no implementado.'];
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

        $candidatoModel = new Candidato();

        // Opcional: Obtener datos del candidato antes de eliminar para borrar la foto
        $candidato = $candidatoModel->getById($id);
        if ($candidato && !empty($candidato['foto'])) {
            // Eliminar el archivo de la foto si existe
            if (file_exists($candidato['foto'])) {
                unlink($candidato['foto']);
            }
        }

        // Llamar al modelo para eliminar el candidato
        if ($candidatoModel->delete($id)) {
             return [
                'success' => true,
                'message' => 'Candidato eliminado correctamente.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al eliminar el candidato. Por favor, intenta nuevamente.'
        ];
    }
}
?> 