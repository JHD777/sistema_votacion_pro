<?php
require_once '../config/config.php';
require_once '../includes/funciones.php';

// Verificar sesión de Super Administrador
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_rol'] !== 'super') {
    header('Location: ../index.php');
    exit;
}

// Obtener información del administrador
$stmt = $conn->prepare("SELECT nombre, apellido FROM administradores WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener configuración actual del sistema
$stmt = $conn->prepare("SELECT * FROM configuracion_sistema WHERE id = 1");
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar formulario de actualización
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar configuración
    $nombre_sistema = $_POST['nombre_sistema'] ?? $config['nombre_sistema'];
    $color_primario = $_POST['color_primario'] ?? $config['color_primario'];
    $color_secundario = $_POST['color_secundario'] ?? $config['color_secundario'];
    $color_texto = $_POST['color_texto'] ?? $config['color_texto'];
    $footer_texto = $_POST['footer_texto'] ?? $config['footer_texto'];
    
    // Procesar logo si se ha subido
    $logo = $config['logo'];
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/img/';
        $temp_name = $_FILES['logo']['tmp_name'];
        $file_name = 'logo_' . time() . '_' . $_FILES['logo']['name'];
        
        if (move_uploaded_file($temp_name, $upload_dir . $file_name)) {
            $logo = $file_name;
        }
    }
    
    // Actualizar en la base de datos
    $stmt = $conn->prepare("UPDATE configuracion_sistema SET 
                           nombre_sistema = ?, 
                           logo = ?, 
                           color_primario = ?, 
                           color_secundario = ?, 
                           color_texto = ?, 
                           footer_texto = ?, 
                           ultima_actualizacion = NOW(), 
                           actualizado_por = ? 
                           WHERE id = 1");
    
    $resultado = $stmt->execute([
        $nombre_sistema,
        $logo,
        $color_primario,
        $color_secundario,
        $color_texto,
        $footer_texto,
        $_SESSION['admin_id']
    ]);
    
    if ($resultado) {
        $mensaje = '<div class="alert alert-success">Configuración actualizada correctamente</div>';
        // Actualizar variable de configuración
        $config['nombre_sistema'] = $nombre_sistema;
        $config['logo'] = $logo;
        $config['color_primario'] = $color_primario;
        $config['color_secundario'] = $color_secundario;
        $config['color_texto'] = $color_texto;
        $config['footer_texto'] = $footer_texto;
    } else {
        $mensaje = '<div class="alert alert-danger">Error al actualizar la configuración</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Super Administrador - <?php echo htmlspecialchars($config['nombre_sistema']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        :root {
            --color-primario: <?php echo $config['color_primario']; ?>;
            --color-secundario: <?php echo $config['color_secundario']; ?>;
            --color-texto: <?php echo $config['color_texto']; ?>;
        }
        
        body {
            background-color: #f8f9fa;
            color: var(--color-texto);
        }
        
        .navbar {
            background-color: var(--color-primario);
        }
        
        .btn-primary {
            background-color: var(--color-primario);
            border-color: var(--color-primario);
        }
        
        .btn-secondary {
            background-color: var(--color-secundario);
            border-color: var(--color-secundario);
        }
        
        .card-header {
            background-color: var(--color-primario);
            color: white;
        }
        
        .sidebar {
            background-color: #343a40;
            min-height: calc(100vh - 56px);
            padding-top: 15px;
        }
        
        .sidebar a {
            color: #f8f9fa;
            padding: 10px 15px;
            display: block;
        }
        
        .sidebar a:hover {
            background-color: #495057;
            text-decoration: none;
        }
        
        .sidebar a.active {
            background-color: var(--color-primario);
        }
        
        .content {
            padding: 20px;
        }
        
        .color-preview {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-left: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">
            <?php if (!empty($config['logo'])): ?>
                <img src="../assets/img/<?php echo htmlspecialchars($config['logo']); ?>" height="30" alt="Logo">
            <?php else: ?>
                <?php echo htmlspecialchars($config['nombre_sistema']); ?>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-user"></i> 
                        <?php echo htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido']); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <a href="#" class="active">
                    <i class="fas fa-cogs"></i> Configuración del Sistema
                </a>
                <a href="#">
                    <i class="fas fa-users-cog"></i> Gestión de Administradores
                </a>
                <a href="#">
                    <i class="fas fa-database"></i> Respaldo de Base de Datos
                </a>
                <a href="#">
                    <i class="fas fa-chart-line"></i> Estadísticas Avanzadas
                </a>
                <a href="../admin/index.php">
                    <i class="fas fa-tachometer-alt"></i> Panel de Administrador
                </a>
            </div>
            
            <div class="col-md-10 content">
                <h2><i class="fas fa-cogs"></i> Panel de Super Administrador</h2>
                <p class="lead">Bienvenido al panel de control avanzado. Aquí puedes personalizar el sistema.</p>
                
                <?php echo $mensaje; ?>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h4><i class="fas fa-palette"></i> Personalización del Sistema</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nombre del Sistema:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nombre_sistema" value="<?php echo htmlspecialchars($config['nombre_sistema']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Logo:</label>
                                <div class="col-sm-9">
                                    <?php if (!empty($config['logo'])): ?>
                                        <div class="mb-2">
                                            <img src="../assets/img/<?php echo htmlspecialchars($config['logo']); ?>" height="50" alt="Logo actual">
                                            <small class="text-muted ml-2">Logo actual</small>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control-file" name="logo" accept="image/*">
                                    <small class="text-muted">Recomendado: 200x50 píxeles</small>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Color Primario:</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="color_primario" value="<?php echo htmlspecialchars($config['color_primario']); ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?php echo htmlspecialchars($config['color_primario']); ?></span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Color principal para barras de navegación y botones</small>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Color Secundario:</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="color_secundario" value="<?php echo htmlspecialchars($config['color_secundario']); ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?php echo htmlspecialchars($config['color_secundario']); ?></span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Color para acentos y elementos secundarios</small>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Color de Texto:</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="color_texto" value="<?php echo htmlspecialchars($config['color_texto']); ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?php echo htmlspecialchars($config['color_texto']); ?></span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Color principal para textos</small>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Texto del Pie de Página:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="footer_texto" rows="2"><?php echo htmlspecialchars($config['footer_texto']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h4><i class="fas fa-eye"></i> Vista Previa</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">Encabezado</div>
                                    <div class="card-body">
                                        <button class="btn btn-primary mb-2">Botón Primario</button>
                                        <button class="btn btn-secondary">Botón Secundario</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h5>Ejemplo de Texto</h5>
                                <p>Este es un ejemplo de cómo se verá el texto con los colores seleccionados.</p>
                                <div class="alert" style="background-color: <?php echo $config['color_primario']; ?>; color: white;">
                                    Alerta con color primario
                                </div>
                                <div class="alert" style="background-color: <?php echo $config['color_secundario']; ?>; color: white;">
                                    Alerta con color secundario
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <?php echo htmlspecialchars($config['footer_texto']); ?>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Actualizar texto del color al cambiar el valor
        document.querySelectorAll('input[type="color"]').forEach(input => {
            input.addEventListener('change', function() {
                this.closest('.input-group').querySelector('.input-group-text').textContent = this.value;
            });
        });
    </script>
</body>
</html>