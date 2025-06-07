<?php
require_once '../config/config.php';
require_once '../includes/funciones.php';

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }
    
    $codigo = $data['codigo'] ?? '';
    $usuario = $data['usuario'] ?? '';
    $password = $data['password'] ?? '';
    
    // Verificar código secreto (por ahora, un código fijo: "MASTER2023")
    $codigoSecreto = "MASTER2023";
    
    if ($codigo !== $codigoSecreto) {
        echo json_encode(['success' => false, 'message' => 'Código inválido']);
        exit;
    }
    
    // Verificar credenciales de administrador
    $stmt = $conn->prepare("SELECT id, password, rol_id FROM administradores 
                           WHERE username = ? AND activo = 1");
    $stmt->execute([$usuario]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si la contraseña está hasheada o no
    if (!$admin) {
        echo json_encode(['success' => false, 'message' => 'Credenciales inválidas']);
        exit;
    }
    
    // Intentar verificar con password_verify primero
    if (password_verify($password, $admin['password'])) {
        // Contraseña correcta (hasheada)
    } 
    // Si no funciona, verificar si coinciden directamente (para sistemas antiguos)
    else if ($password === $admin['password']) {
        // Contraseña correcta (sin hash)
    } 
    else {
        echo json_encode(['success' => false, 'message' => 'Credenciales inválidas']);
        exit;
    }
    
    // Verificar si es Super Administrador
    if ($admin['rol_id'] != 1) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos de Super Administrador']);
        exit;
    }
    
    // Iniciar sesión como Super Administrador
    session_start();
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_rol'] = 'super';
    $_SESSION['ultimo_acceso'] = time();
    
    // Registrar acceso
    $stmt = $conn->prepare("UPDATE administradores SET ultimo_login = NOW() WHERE id = ?");
    $stmt->execute([$admin['id']]);
    
    echo json_encode(['success' => true]);
    exit;
}

// Si no es POST, redirigir
header('Location: ../index.php');
exit;