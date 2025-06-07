<?php

namespace Models;

require_once __DIR__ . '/Database.php';

use Models\Database;

class Admin {
    private $conn;
    private $table_name = "administradores";
    private $database;
    
    // Constructor
    public function __construct() {
        $this->database = Database::getInstance();
        $this->conn = $this->database->getConnection();
    }
    
    // Crear un nuevo administrador
    public function create($data) {
        // Verificar si el email ya existe
        if ($this->emailExists($data['email'])) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, apellido, email, password, rol, activo, fecha_creacion) 
                  VALUES (:nombre, :apellido, :username, :email, :password, :rol, :activo, NOW())";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Hash de la contraseña
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Vincular parámetros
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':rol', $data['rol']);
            $stmt->bindParam(':activo', $data['activo']);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Verificar si el email ya existe
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return ($stmt->rowCount() > 0);
    }
    
    // Actualizar administrador
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, apellido = :apellido, email = :email, 
                  rol = :rol, activo = :activo 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular parámetros
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':rol', $data['rol']);
            $stmt->bindParam(':activo', $data['activo']);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Cambiar contraseña
    public function updatePassword($id, $password) {
        $query = "UPDATE " . $this->table_name . " 
                  SET password = :password 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Vincular parámetros
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Eliminar administrador
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
    
    // Obtener administrador por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    // Obtener todos los administradores
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_registro DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en getAll (AdminModel): " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener el total de administradores
    public function getTotalAdmins() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row['total'];
    }
    
    // Login de administrador
    public function login($email, $password) {
        $query = "SELECT id, nombre, apellido, email, password, rol FROM " . $this->table_name . " 
                  WHERE email = :email AND activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        
        return false;
    }

    // Método para contar superadministradores
    public function countSuperAdmins() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE rol_id = 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (\PDOException $e) {
            error_log("Error en countSuperAdmins: " . $e->getMessage());
            return 0;
        }
    }

     // Verificar si el username existe
     public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return ($stmt->rowCount() > 0);
    }

     // Método para obtener administrador por ID (usado en deleteAdmin)
    public function getAdminById($admin_id) {
         $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
         try {
             $stmt = $this->conn->prepare($query);
             $stmt->bindParam(':id', $admin_id, \PDO::PARAM_INT);
             $stmt->execute();
             return $stmt->fetch(\PDO::FETCH_ASSOC);
         } catch (\PDOException $e) {
             error_log("Error en getAdminById (AdminModel): " . $e->getMessage());
             return false;
         }
    }


}
?>