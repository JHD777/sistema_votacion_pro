<?php

namespace Models;

require_once __DIR__ . '/Database.php';

use Models\Database;

class User {
    private $conn;
    private $table_name = "usuarios";
    private $database;
    
    // Constructor
    public function __construct() {
        $this->database = Database::getInstance();
        $this->conn = $this->database->getConnection();
    }
    
    // Registrar un nuevo usuario
    public function register($data) {
        try {
            $this->conn->beginTransaction();
            
            // Verificar si el email ya existe
            if ($this->emailExists($data['email'])) {
                return [
                    'success' => false,
                    'message' => 'El correo electrónico ya está registrado'
                ];
            }
            
            // Verificar si el documento de identidad ya existe
            if ($this->documentoExists($data['documento_identidad'])) {
                return [
                    'success' => false,
                    'message' => 'El documento de identidad ya está registrado'
                ];
            }
            
            // Generar código de verificación
            $codigo_verificacion = bin2hex(random_bytes(16));
            
            // Encriptar contraseña
            $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // Query para insertar usuario
            $query = "INSERT INTO " . $this->table_name . " 
                      (nombre, apellido, email, documento_identidad, password, codigo_verificacion, fecha_registro) 
                      VALUES (:nombre, :apellido, :email, :documento_identidad, :password, :codigo_verificacion, NOW())";
            
            try {
                $stmt = $this->conn->prepare($query);
                
                // Vincular parámetros
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':documento_identidad', $data['documento_identidad']);
                $stmt->bindParam(':password', $password_hash);
                $stmt->bindParam(':codigo_verificacion', $codigo_verificacion);
                
                if ($stmt->execute()) {
                    return [
                        'success' => true,
                        'message' => 'Registro exitoso. Por favor, verifica tu correo electrónico para activar tu cuenta.',
                        'codigo_verificacion' => $codigo_verificacion
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => 'Error al registrar el usuario. Por favor, intenta nuevamente.'
                ];
            } catch(\PDOException $e) {
                return [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
            
            $this->conn->commit();
            return [
                'success' => true,
                'user_id' => $this->conn->lastInsertId(),
                'codigo_verificacion' => $codigo_verificacion
            ];
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en registro: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si el email existe
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Verificar si el documento existe
    public function documentoExists($documento) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE documento_identidad = :documento LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Verificar cuenta
    public function verifyAccount($codigo) {
        $query = "UPDATE " . $this->table_name . " SET verificado = 1, codigo_verificacion = NULL WHERE codigo_verificacion = :codigo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Iniciar sesión
    public function login($email, $password) {
        $query = "SELECT id, nombre, apellido, email, password, verificado FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Desactivar la verificación para pruebas
            // if ($row['verificado'] == 0) {
            //     return [
            //         'success' => false,
            //         'message' => 'Tu cuenta aún no ha sido verificada. Por favor, revisa tu correo electrónico.'
            //     ];
            // }
            
            // Verificar contraseña
            if (password_verify($password, $row['password'])) {
                return [
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso',
                    'user' => [
                        'id' => $row['id'],
                        'nombre' => $row['nombre'],
                        'apellido' => $row['apellido'],
                        'email' => $row['email']
                    ]
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Credenciales inválidas'
        ];
    }
    
    // Obtener usuario por ID
    public function getById($id) {
        $query = "SELECT id, nombre, apellido, email, documento_identidad, verificado, activo, fecha_registro 
                  FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    // Obtener todos los usuarios
    public function getAll() {
        $query = "SELECT id, nombre, apellido, email, documento_identidad, verificado, activo, fecha_registro 
                  FROM " . $this->table_name . " ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Actualizar perfil de usuario
    public function updateProfile($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, apellido = :apellido 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Vincular parámetros
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(\PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // Cambiar contraseña
    public function changePassword($id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Asegúrate de que exista este método
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>