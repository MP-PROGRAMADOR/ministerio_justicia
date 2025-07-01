<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mensaje = document.getElementById('mensajeFlash');
        if (mensaje) {
            setTimeout(() => {
                mensaje.style.transition = 'opacity 0.5s ease';
                mensaje.style.opacity = '0';
                setTimeout(() => mensaje.remove(), 500); // elimina después del desvanecimiento
            }, 5000);
        }
    });
</script>


</body>

</html>