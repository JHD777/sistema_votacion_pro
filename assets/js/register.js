document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener valores del formulario
            const nombre = document.getElementById('nombre').value;
            const apellido = document.getElementById('apellido').value;
            const email = document.getElementById('email').value;
            const documento_identidad = document.getElementById('documento_identidad').value;
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;
            
            // Validar campos
            if (!nombre || !apellido || !email || !documento_identidad || !password || !confirm_password) {
                showMessage('Por favor, completa todos los campos obligatorios', 'danger');
                return;
            }
            
            // Validar email
            if (!validateEmail(email)) {
                showMessage('Por favor, ingresa un correo electrónico válido', 'danger');
                return;
            }
            
            // Validar contraseña
            if (password.length < 8) {
                showMessage('La contraseña debe tener al menos 8 caracteres', 'danger');
                return;
            }
            
            if (!(/[A-Za-z]/.test(password) && /[0-9]/.test(password))) {
                showMessage('La contraseña debe incluir al menos una letra y un número', 'danger');
                return;
            }
            
            // Validar confirmación de contraseña
            if (password !== confirm_password) {
                showMessage('Las contraseñas no coinciden', 'danger');
                return;
            }
            
            // Validar términos y condiciones
            if (!terms) {
                showMessage('Debes aceptar los términos y condiciones', 'danger');
                return;
            }
            
            // Enviar solicitud al servidor
            fetch(BASE_URL + '/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nombre: nombre,
                    apellido: apellido,
                    email: email,
                    documento_identidad: documento_identidad,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Redireccionar a la página de verificación
                    setTimeout(() => {
                        window.location.href = BASE_URL + '/verify';
                    }, 2000);
                } else {
                    showMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage('Error al procesar la solicitud. Por favor, intenta nuevamente.', 'danger');
                console.error('Error:', error);
            });
        });
    }
    
    // Función para validar email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Función para mostrar mensajes
    function showMessage(message, type) {
        const messageContainer = document.getElementById('register-message');
        messageContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }
});