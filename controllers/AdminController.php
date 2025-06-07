<?php
namespace Controllers;

// Verificamos si la sesión ya está iniciada para evitar el warning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Eleccion.php';
require_once __DIR__ . '/../models/Candidato.php';
require_once __DIR__ . '/../models/Vote.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Admin.php';

use Models\Database;
use Models\Candidato;
use Models\Admin;
use Models\User;
use Models\Eleccion;
use Models\Vote;
use \PDOException;
use \PDO;

class AdminController {
    private $adminModel;
    private $conn;
    
    public function __construct() {
        $this->adminModel = new Admin();
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    public function getSystemStats() {
        $userModel = new User();
        $electionModel = new Eleccion();
        // $candidateModel = new Candidato(); // Descomentar si se resuelve el error
        $voteModel = new Vote();
        
        // Obtener total de candidatos directamente (solución temporal)
        $total_candidates = 0;
        try {
            $query = "SELECT COUNT(*) as total FROM candidatos";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $total_candidates = $row['total'];
        } catch(\PDOException $e) {
            error_log("Error al obtener total de candidatos en AdminController: " . $e->getMessage());
            // Manejar el error apropiadamente, quizás retornar 0 o registrar el error
        }

        return [
            'total_users' => $userModel->getTotalUsers(),
            'total_elections' => $electionModel->getTotalElections(),
            'total_candidates' => $total_candidates, // Usar el total obtenido directamente
            'total_votes' => $voteModel->getTotalVotes()
        ];
    }
    
    // Método para crear un nuevo administrador
    public function createAdmin($data) {
        // Validar que los datos necesarios estén presentes
        if (!isset($data['nombre'], $data['apellido'], $data['username'], $data['email'], $data['password'], $data['rol'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos para crear el administrador.'
            ];
        }
        
        // Verificar si el email ya existe antes de intentar crear
        if ($this->adminModel->emailExists($data['email'])) {
            return [
                'success' => false,
                'message' => 'El email ya está registrado.'
            ];
        }

        // Verificar si el username ya existe antes de intentar crear
        if ($this->adminModel->usernameExists($data['username'])) {
             return [
                 'success' => false,
                 'message' => 'El nombre de usuario ya está en uso.'
             ];
         }
        
        try {
            // Preparar la consulta SQL para insertar un nuevo administrador
            // Incluir 'fecha_registro' y establecerlo a NOW()
            $query = "INSERT INTO administradores (nombre, apellido, username, email, password, rol_id, activo, fecha_registro) 
                      VALUES (:nombre, :apellido, :username, :email, :password, :rol_id, 1, NOW())";
            $stmt = $this->conn->prepare($query);
            
            // Hash de la contraseña
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Vincular parámetros
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':rol_id', $data['rol']); // Usar rol_id ya que es el nombre de la columna
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Administrador creado correctamente',
                    'admin_id' => $this->conn->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear el administrador'
                ];
            }
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    // Método para obtener todos los administradores
    public function getAllAdmins() {
        // En lugar de llamar directamente al modelo, puedes usar la conexión aquí si prefieres
        // O asegurarte de que el modelo tenga un método getAll que devuelva los datos
        // Para mantener la consistencia, vamos a asumir que AdminModel tiene un método getAll
        $admins = $this->adminModel->getAll();
        
        // Aquí puedes añadir lógica adicional si es necesario
        
        return [
            'success' => true,
            'admins' => $admins
        ];
    }
    
    // Método para actualizar un administrador
    public function updateAdmin($admin_id, $data) {
        try {
            $query = "UPDATE administradores 
                      SET nombre = :nombre, apellido = :apellido, email = :email, activo = :activo
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':activo', $data['activo'], \PDO::PARAM_INT);
            $stmt->bindParam(':id', $admin_id, \PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Administrador actualizado correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar el administrador'
                ];
            }
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    // Método para cambiar la contraseña de un administrador
    public function changeAdminPassword($admin_id, $new_password) {
        try {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            $query = "UPDATE administradores SET password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':id', $admin_id, \PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Contraseña actualizada correctamente'
                ];
            }
            return [
                'success' => false,
                'message' => 'Error al actualizar la contraseña'
            ];
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    // Método para eliminar un administrador
    public function deleteAdmin($admin_id) {
        try {
            // Verificar si es el último superadministrador
            $admin = $this->adminModel->getAdminById($admin_id);
            if ($admin && $admin['rol_id'] == 1) { // ID 1 es superadministrador
                 $totalSuperAdmins = $this->adminModel->countSuperAdmins();
                 if ($totalSuperAdmins <= 1) {
                     return [
                         'success' => false,
                         'message' => 'No se puede eliminar el último administrador supremo.'
                     ];
                 }
            }
            
            $query = "DELETE FROM administradores WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $admin_id, \PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Administrador eliminado correctamente'
                ];
            }
            return [
                'success' => false,
                'message' => 'Error al eliminar el administrador'
            ];
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // Método para obtener un administrador por ID (útil para la edición)
    public function getAdminById($admin_id) {
        return $this->adminModel->getAdminById($admin_id);
    }

    // Método para obtener la configuración del sistema
    // Considerar mover esto a un controlador de configuración si hay más ajustes
    public function getSystemConfig() {
        try {
            $query = "SELECT clave, valor FROM configuracion";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $config = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $config[$row['clave']] = $row['valor'];
            }
            
            return $config;
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    public function getAdminRoles() {
        try {
            $query = "SELECT id, nombre FROM roles_admin ORDER BY id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $roles = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $roles[] = $row;
            }
            
            return $roles;
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    public function getAllUsers() {
        $userModel = new User();
        $users = $userModel->getAll();
        return [
            'success' => true,
            'users' => $users
        ];
    }

    // Método para verificar un usuario
    public function verifyUser($user_id) {
        // Lógica para verificar usuario
        // Deberías tener un método en el modelo User para esto
        $userModel = new \Models\User(); // Asegúrate de usar el namespace correcto
        return $userModel->verifyUser($user_id);
    }

    // Método para desactivar un usuario
    public function deactivateUser($user_id) {
        // Lógica para desactivar usuario
        // Deberías tener un método en el modelo User para esto
        $userModel = new \Models\User(); // Asegúrate de usar el namespace correcto
        return $userModel->deactivateUser($user_id);
    }
}
?>