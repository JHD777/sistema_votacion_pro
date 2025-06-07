document.addEventListener('DOMContentLoaded', function() {
    // Detectar combinación de teclas (Ctrl+Alt+S)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.altKey && e.key === 's') {
            e.preventDefault();
            mostrarFormularioSecreto();
        }
    });
    
    function mostrarFormularioSecreto() {
        // Crear y mostrar el formulario modal
        const modal = document.createElement('div');
        modal.className = 'modal-secreto';
        modal.innerHTML = `
            <div class="modal-contenido">
                <h3>Acceso Restringido</h3>
                <form id="form-acceso-secreto">
                    <div class="form-group">
                        <label for="codigo-secreto">Código de Acceso:</label>
                        <input type="password" id="codigo-secreto" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario-admin">Usuario:</label>
                        <input type="text" id="usuario-admin" required>
                    </div>
                    <div class="form-group">
                        <label for="password-admin">Contraseña:</label>
                        <input type="password" id="password-admin" required>
                    </div>
                    <button type="submit">Acceder</button>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Manejar envío del formulario
        document.getElementById('form-acceso-secreto').addEventListener('submit', function(e) {
            e.preventDefault();
            const codigo = document.getElementById('codigo-secreto').value;
            const usuario = document.getElementById('usuario-admin').value;
            const password = document.getElementById('password-admin').value;
            
            verificarAccesoSuperAdmin(codigo, usuario, password);
        });
    }
    
    function verificarAccesoSuperAdmin(codigo, usuario, password) {
        // Enviar datos al servidor para verificación
        fetch('admin/verificar_acceso_secreto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                codigo: codigo,
                usuario: usuario,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'admin/panel_super_admin.php';
            } else {
                alert('Acceso denegado');
                document.querySelector('.modal-secreto').remove();
            }
        });
    }
});