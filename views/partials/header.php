<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>"><?php echo isset(APP_CONFIGS['titulo_sistema']) ? htmlspecialchars(APP_CONFIGS['titulo_sistema']) : 'Sistema de Votación Pro'; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>">Inicio</a>
                    </li>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=user/dashboard">Mi Panel</a>
                        </li>
                    <?php elseif (isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/dashboard">Panel Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/elections">Elecciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/candidates">Candidatos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/users">Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/results">Resultados</a>
                        </li>
                        <?php if (isset($_SESSION['admin_rol']) && $_SESSION['admin_rol'] === 'super'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/settings">
                                    <i class="bi bi-gear"></i> Configuración
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>?view=admin/admins">
                                    <i class="bi bi-people"></i> Administradores
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> 
                                <?php 
                                    if (isset($_SESSION['user_id'])) {
                                        echo $_SESSION['user_name'];
                                    } elseif (isset($_SESSION['admin_id'])) {
                                        echo isset($_SESSION['admin_rol']) && $_SESSION['admin_rol'] === 'super' 
                                            ? 'Administrador Supremo' 
                                            : 'Administrador';
                                    }
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>?view=user/dashboard">Mi Panel</a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>?view=perfil">Mi Perfil</a></li>
                                <?php elseif (isset($_SESSION['admin_id'])): ?>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>?view=admin/dashboard">Panel Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>?view=logout">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=login">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?view=register">Registrarse</a>
                        </li>
                        <?php
                        // Mostrar botón solo si la sesión especial está activa
                        if (isset($_SESSION['admin_supremo']) && $_SESSION['admin_supremo'] === true) {
                            echo '<li class="nav-item"><a class="nav-link text-danger" href="' . BASE_URL . '?view=admin/panel_supremo"><i class="bi bi-shield-lock"></i> Admin Supremo</a></li>';
                        }
                        ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<head>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.php">
    <link rel="stylesheet" href="https://cdn