  <!-- Modal de Aviso de Cierre de Sesión -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="bi bi-shield-exclamation"></i>
                        <span>Confirmar Cierre de Sesión</span>
                    </h5>
                    <!-- Close button for the modal header -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="warning-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5>¿Estás seguro de que deseas cerrar sesión?</h5>
                    <p>Tu sesión actual será terminada y deberás iniciar sesión nuevamente para acceder al sistema.</p>

                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="user-details">
                            <h6>Juan Pérez</h6>
                            <small class="text-muted">Administrador del Sistema</small>
                        </div>
                    </div>

                    <div class="session-info">
                        <i class="bi bi-clock"></i>
                        <span>Sesión iniciada: Hoy a las 09:30 AM</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="logout()">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast para confirmación -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="logoutToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle me-2"></i>
                <strong class="me-auto">Éxito</strong>
                <small>Ahora</small>
            </div>
            <div class="toast-body">
                Sesión cerrada correctamente. Redirigiendo...
            </div>
        </div>
    </div>

     <script>
        function logout() {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('logoutModal'));
            if (modal) { // Check if modal instance exists
                modal.hide();
            }

            // Mostrar toast de confirmación
            const toastElement = document.getElementById('logoutToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            // Simular redirección después de 2 segundos
            setTimeout(() => {
                // Aquí iría la lógica real de cierre de sesión
                console.log('Cerrando sesión...');
                // window.location.href = '/login'; // Descomenta esta línea para la redirección real
                // Instead of alert(), use a custom message box or console log in production
                // For demonstration purposes, we'll keep the alert for now, but it's not recommended in iframes.
                // alert('Sesión cerrada exitosamente (esto es una demostración)');
            }, 2000);
        }

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modalElement = document.getElementById('logoutModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        });
    </script>