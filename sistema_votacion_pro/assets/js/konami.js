// Código secreto para acceso de administrador supremo
// Versión simplificada y más robusta

document.addEventListener('DOMContentLoaded', function() {
    // Definimos la secuencia de teclas para el código secreto
    const codigoSecreto = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 
                          'ArrowLeft', 'ArrowRight', 'j', 'h', 'd'];
    
    // Variable para almacenar la posición actual en la secuencia
    let posicionActual = 0;
    
    // Función para manejar las pulsaciones de teclas
    function manejarTecla(e) {
        // Verificamos si la tecla presionada coincide con la esperada
        if (e.key === codigoSecreto[posicionActual]) {
            posicionActual++;
            
            // Si hemos completado toda la secuencia
            // Cuando se detecta el código secreto
        if (posicionActual === codigoSecreto.length) {
            // Mostramos un mensaje
            alert('¡Código secreto activado! Accediendo al modo Administrador Supremo...');
            
            // Redirigimos a la página de verificación (sin pasar el código)
            window.location.href = '<?php echo BASE_URL; ?>?view=admin/verificar_supremo';
            
            // Reiniciamos la posición
            posicionActual = 0;
        }
        } else {
            // Si no coincide, reiniciamos la secuencia
            posicionActual = 0;
            
            // Si la tecla actual coincide con la primera del código, avanzamos
            if (e.key === codigoSecreto[0]) {
                posicionActual = 1;
            }
        }
    }
    
    // Agregamos el evento de teclado
    document.addEventListener('keydown', manejarTecla);
});