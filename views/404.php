<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | <?php echo defined('APP_NAME') ? APP_NAME : 'Sistema de Votación'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5 text-center">
        <h1 class="display-1">404</h1>
        <h2 class="mb-4">Página no encontrada</h2>
        <p class="lead mb-5">Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
        <a href="<?php echo BASE_URL; ?>?view=home" class="btn btn-primary">Volver al inicio</a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>