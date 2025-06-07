<?php
// Configuración de la base de datos
if (!defined('DB_CONFIG')) {
    define('DB_CONFIG', [
        'host' => 'localhost',
        'database' => 'sistema_votacion_pro',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'port' => 3306,
        'strict' => true,
        'engine' => 'InnoDB',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    ]);
}

// Función para obtener la conexión a la base de datos
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                DB_CONFIG['host'],
                DB_CONFIG['port'],
                DB_CONFIG['database'],
                DB_CONFIG['charset']
            );
            
            $pdo = new PDO(
                $dsn,
                DB_CONFIG['username'],
                DB_CONFIG['password'],
                DB_CONFIG['options']
            );
            
            // Configurar el modo de error
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Configurar el modo de fetch por defecto
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Desactivar la emulación de prepared statements
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
        } catch (PDOException $e) {
            // Log del error
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            
            // En producción, mostrar un mensaje genérico
            if (!defined('APP_DEBUG') || !APP_DEBUG) {
                die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
            }
            
            // En desarrollo, mostrar el error completo
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Función para ejecutar consultas de forma segura
function executeQuery($sql, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Error en la consulta SQL: " . $e->getMessage());
        throw $e;
    }
}

// Función para obtener un solo registro
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

// Función para obtener múltiples registros
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

// Función para insertar un registro
function insert($table, $data) {
    $fields = array_keys($data);
    $placeholders = array_fill(0, count($fields), '?');
    
    $sql = sprintf(
        "INSERT INTO %s (%s) VALUES (%s)",
        $table,
        implode(', ', $fields),
        implode(', ', $placeholders)
    );
    
    executeQuery($sql, array_values($data));
    return getDBConnection()->lastInsertId();
}

// Función para actualizar registros
function update($table, $data, $where, $whereParams = []) {
    $fields = array_map(function($field) {
        return "$field = ?";
    }, array_keys($data));
    
    $sql = sprintf(
        "UPDATE %s SET %s WHERE %s",
        $table,
        implode(', ', $fields),
        $where
    );
    
    $params = array_merge(array_values($data), $whereParams);
    executeQuery($sql, $params);
}

// Función para eliminar registros
function delete($table, $where, $params = []) {
    $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
    executeQuery($sql, $params);
}

// Función para comenzar una transacción
function beginTransaction() {
    return getDBConnection()->beginTransaction();
}

// Función para confirmar una transacción
function commit() {
    return getDBConnection()->commit();
}

// Función para revertir una transacción
function rollback() {
    return getDBConnection()->rollBack();
}

// Función para escapar valores
function escape($value) {
    return getDBConnection()->quote($value);
}

// Función para verificar si una tabla existe
function tableExists($table) {
    $sql = "SHOW TABLES LIKE ?";
    $result = fetchOne($sql, [$table]);
    return !empty($result);
}

// Función para verificar si una columna existe
function columnExists($table, $column) {
    $sql = "SHOW COLUMNS FROM $table LIKE ?";
    $result = fetchOne($sql, [$column]);
    return !empty($result);
}

// Función para obtener el último ID insertado
function getLastInsertId() {
    return getDBConnection()->lastInsertId();
}

// Función para obtener el número de filas afectadas
function getAffectedRows() {
    return getDBConnection()->rowCount();
}

// Función para verificar si hay un error en la última operación
function hasError() {
    return getDBConnection()->errorCode() !== '00000';
}

// Función para obtener el último error
function getLastError() {
    $error = getDBConnection()->errorInfo();
    return $error[2] ?? null;
}

// Función para limpiar la conexión
function closeConnection() {
    $pdo = getDBConnection();
    $pdo = null;
}

// Registrar la función de cierre para cuando el script termine
register_shutdown_function('closeConnection');
?>