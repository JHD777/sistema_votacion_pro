<?php
// Incluir el sistema de registro de errores
require_once __DIR__ . '/../../logs/error_log.php';

// Si ya hay una sesión activa, redirigir según el tipo de usuario
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user') {
    header('Location: ' . BASE_URL . '?view=user/dashboard');
    exit;
} elseif (isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin') {
    header('Location: ' . BASE_URL . '?view=admin/dashboard');
    exit;
}

// Procesar el formulario de registro
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['password_confirm'] ?? '';
    $documento = $_POST['documento'] ?? '';

    // Validaciones básicas
    if ($password !== $confirm_password) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=sistema_votacion_pro', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Verificar si la tabla usuarios tiene las columnas necesarias
            $columnas_requeridas = ['nombre', 'apellido', 'email', 'password', 'documento_identidad'];
            $columnas_faltantes = [];
            
            // Obtener las columnas de la tabla usuarios
            $stmt = $pdo->query("SHOW COLUMNS FROM usuarios");
            $columnas_existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($columnas_requeridas as $columna) {
                if (!in_array($columna, $columnas_existentes)) {
                    $columnas_faltantes[] = $columna;
                }
            }
            
            // Si faltan columnas, intentar crearlas
            if (!empty($columnas_faltantes)) {
                ErrorLogger::log("Faltan columnas en la tabla usuarios: " . implode(', ', $columnas_faltantes), __FILE__, __LINE__);
                
                // Intentar crear las columnas faltantes
                foreach ($columnas_faltantes as $columna) {
                    try {
                        if ($columna == 'nombre') {
                            $pdo->exec("ALTER TABLE usuarios ADD COLUMN nombre VARCHAR(100) NOT NULL AFTER id");
                        } else if ($columna == 'apellido') {
                            $pdo->exec("ALTER TABLE usuarios ADD COLUMN apellido VARCHAR(100) NOT NULL AFTER nombre");
                        } else if ($columna == 'documento_identidad') {
                            $pdo->exec("ALTER TABLE usuarios ADD COLUMN documento_identidad VARCHAR(20) NOT NULL UNIQUE AFTER email");
                        }
                        ErrorLogger::log("Columna {$columna} creada con éxito", __FILE__, __LINE__);
                    } catch (PDOException $e) {
                        ErrorLogger::log("Error al crear la columna {$columna}: " . $e->getMessage(), __FILE__, __LINE__);
                    }
                }
            }

            // Verificar si el email ya existe
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $mensaje = 'El correo ya está registrado.';
                $tipo_mensaje = 'danger';
            } else {
                // Insertar usuario
                $hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Verificar si la columna documento_identidad existe, de lo contrario usar documento
                if (in_array('documento_identidad', $columnas_existentes)) {
                    $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, email, documento_identidad, password, fecha_registro) VALUES (?, ?, ?, ?, ?, NOW())');
                    $stmt->execute([$nombre, $apellido, $email, $documento, $hash]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, email, documento, password, fecha_registro) VALUES (?, ?, ?, ?, ?, NOW())');
                    $stmt->execute([$nombre, $apellido, $email, $documento, $hash]);
                }
                
                // Iniciar sesión automáticamente
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_type'] = 'user';
                $_SESSION['user_name'] = $nombre;
                
                // Registrar éxito
                ErrorLogger::log("Usuario registrado con éxito: {$email}", __FILE__, __LINE__);
                
                header('Location: ' . BASE_URL . '?view=user/vote');
                exit;
            }
        } catch (PDOException $e) {
            $mensaje = 'Error al registrar: ' . $e->getMessage();
            $tipo_mensaje = 'danger';
            
            // Registrar el error
            ErrorLogger::log($e->getMessage(), __FILE__, __LINE__, [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'documento' => $documento
            ]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Votación</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Registro de Usuario</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirm" tabindex="-1">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="documento" class="form-label">Número de Documento</label>
                                <input type="text" class="form-control" id="documento" name="documento" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terminos" name="terminos" required>
                                <label class="form-check-label" for="terminos">Acepto los términos y condiciones</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Registrarse</button>
                            </div>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <p>¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>?view=login">Inicia sesión aquí</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const target = document.getElementById(this.getAttribute('data-target'));
                if (target.type === 'password') {
                    target.type = 'text';
                    this.querySelector('i').classList.remove('bi-eye');
                    this.querySelector('i').classList.add('bi-eye-slash');
                } else {
                    target.type = 'password';
                    this.querySelector('i').classList.remove('bi-eye-slash');
                    this.querySelector('i').classList.add('bi-eye');
                }
            });
        });
    </script>
</body>
</html>