document.addEventListener('DOMContentLoaded', function() {
    // Formulario de inicio de sesión para usuarios
    const userLoginForm = document.getElementById('user-login-form');
    if (userLoginForm) {
        userLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('user-email').value;
            const password = document.getElementById('user-password').value;
            const messageContainer = document.getElementById('user-login-message');
            
            // Validar campos
            if (!email || !password) {
                showMessage(messageContainer, 'Por favor, completa todos los campos', 'danger');
                return;
            }
            
            // Enviar solicitud al servidor
            fetch(BASE_URL + '/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(messageContainer, data.message, 'success');
                    // Redireccionar al panel de usuario
                    setTimeout(() => {
                        window.location.href = BASE_URL + '/user/dashboard';
                    }, 1500);
                } else {
                    showMessage(messageContainer, data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage(messageContainer, 'Error al procesar la solicitud. Por favor, intenta nuevamente.', 'danger');
                console.error('Error:', error);
            });
        });
    }
    
    // Formulario de inicio de sesión para administradores
    const adminLoginForm = document.getElementById('admin-login-form');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('admin-username').value;
            const password = document.getElementById('admin-password').value;
            const messageContainer = document.getElementById('admin-login-message');
            
            // Validar campos
            if (!username || !password) {
                showMessage(messageContainer, 'Por favor, completa todos los campos', 'danger');
                return;
            }
            
            // Enviar solicitud al servidor
            fetch(BASE_URL + '/api/auth/admin-login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(messageContainer, data.message, 'success');
                    // Redireccionar al panel de administrador
                    setTimeout(() => {
                        window.location.href = BASE_URL + '/admin/dashboard';
                    }, 1500);
                } else {
                    showMessage(messageContainer, data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage(messageContainer, 'Error al procesar la solicitud. Por favor, intenta nuevamente.', 'danger');
                console.error('Error:', error);
            });
        });
    }
    
    // Función para mostrar mensajes
    function showMessage(container, message, type) {
        container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }
});