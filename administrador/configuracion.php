<?php


//session_start();

include_once '../includes/header.php';
include_once '../includes/conexion.php';



function mostrarMensaje($tipo, $texto) {
    $icon = ($tipo === 'success') ? 'fa-check-circle' : ($tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
    $clase_alerta = ($tipo === 'danger') ? 'danger' : $tipo;
    
    return "<div class='alert alert-{$clase_alerta} alert-dismissible fade show' role='alert'>
                <i class='fas {$icon} me-2'></i>
                <strong>" . ucfirst($tipo) . "!</strong> {$texto}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}


function inicializarTablaConfiguracion($pdo) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS configuracion (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    clave VARCHAR(100) UNIQUE NOT NULL,
                    valor LONGTEXT NOT NULL,
                    tipo VARCHAR(50) DEFAULT 'text',
                    descripcion VARCHAR(255),
                    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_clave (clave)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Error al crear tabla configuracion: " . $e->getMessage());
        return false;
    }
}


function obtenerConfig($pdo, $clave, $default = null) {
    try {
        $stmt = $pdo->prepare("SELECT valor FROM configuracion WHERE clave = ?");
        $stmt->execute([$clave]);
        $resultado = $stmt->fetch();
        return $resultado ? $resultado['valor'] : $default;
    } catch (PDOException $e) {
        error_log("Error al obtener configuración: " . $e->getMessage());
        return $default;
    }
}


function guardarConfig($pdo, $clave, $valor, $tipo = 'text', $descripcion = '') {
    try {
        // Validar longitud de clave
        if (strlen($clave) > 100) {
            return ['éxito' => false, 'error' => 'La clave es demasiado larga'];
        }
        
        // Sanitizar entrada
        $clave = trim($clave);
        $valor = trim($valor);
        
        // Usar INSERT...ON DUPLICATE KEY UPDATE para mejor compatibilidad
        $sql = "INSERT INTO configuracion (clave, valor, tipo, descripcion) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    valor = VALUES(valor),
                    tipo = VALUES(tipo),
                    descripcion = VALUES(descripcion)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clave, $valor, $tipo, $descripcion]);
        
        return ['éxito' => true];
    } catch (PDOException $e) {
        error_log("Error al guardar configuración: " . $e->getMessage());
        return ['éxito' => false, 'error' => 'Error al guardar: ' . $e->getMessage()];
    }
}


function cargarTodasLasConfiguraciones($pdo) {
    try {
        $stmt = $pdo->query("SELECT clave, valor FROM configuracion ORDER BY clave");
        $configs = [];
        
        while ($fila = $stmt->fetch()) {
            $configs[$fila['clave']] = $fila['valor'];
        }
        
        return $configs;
    } catch (PDOException $e) {
        error_log("Error al cargar configuraciones: " . $e->getMessage());
        return [];
    }
}

// VALIDAR PERMISOS Y CONEXIÓN

$mensaje = '';
$conexion_valida = false;
$es_admin = false;

// Verificar sesión y rol
if (!isset($_SESSION['ID_Usuario']) || $_SESSION['Rol_Usuario'] !== 'Administrador') {
    $_SESSION['error_login'] = "No tienes permiso para acceder a esta sección.";
    header("Location: ../index.php");
    exit;
}

$es_admin = true;
$nombre_usuario = $_SESSION['nombre_usuario'] ?? 'Administrador';

// Verificar conexión
if (!isset($pdo)) {
    $mensaje .= mostrarMensaje('danger', 'No hay conexión disponible a la base de datos.');
} else {
    $conexion_valida = true;
    
    // Inicializar tabla si no existe
    if (!inicializarTablaConfiguracion($pdo)) {
        $mensaje .= mostrarMensaje('warning', 'Advertencia al inicializar tabla de configuración.');
    }
}

// VALORES POR DEFECTO

$defaults = [
    'nombre_sistema' => 'THEMIS - Ministerio de Justicia',
    'email_soporte' => 'soporte@ministeriojusticia.gq',
    'tema_sistema' => 'claro',
    'fuente_sistema' => 'Arial, sans-serif',
    'tamano_letra' => '16px',
    'color_principal' => '#007bff',
    'limite_sesion_minutos' => '120',
    'habilitar_registro' => '0'
];

// PROCESAR FORMULARIO (POST)

if ($conexion_valida && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_config'])) {
    $errores = [];
    $config_a_guardar = [];
    
    // Validar campos obligatorios
    $nombre_sistema = trim($_POST['nombre_sistema'] ?? '');
    if (empty($nombre_sistema)) {
        $errores[] = 'El nombre del sistema es obligatorio.';
    } else if (strlen($nombre_sistema) > 255) {
        $errores[] = 'El nombre del sistema es demasiado largo (máximo 255 caracteres).';
    } else {
        $config_a_guardar['nombre_sistema'] = $nombre_sistema;
    }
    
    // Email de soporte
    $email_soporte = trim($_POST['email_soporte'] ?? '');
    if (!empty($email_soporte) && !filter_var($email_soporte, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email de soporte no es válido.';
    } else {
        $config_a_guardar['email_soporte'] = $email_soporte;
    }
    
    // Tema
    $temas_validos = ['claro', 'oscuro', 'azul', 'contraste'];
    $tema = $_POST['tema_sistema'] ?? 'claro';
    $config_a_guardar['tema_sistema'] = in_array($tema, $temas_validos) ? $tema : 'claro';
    
    // Fuentes válidas
    $fuentes_validas = ['Arial, sans-serif', 'Roboto, sans-serif', "'Times New Roman', serif", "'Inter', sans-serif"];
    $fuente = $_POST['fuente_sistema'] ?? 'Arial, sans-serif';
    $config_a_guardar['fuente_sistema'] = in_array($fuente, $fuentes_validas) ? $fuente : 'Arial, sans-serif';
    
    // Tamaño de letra
    $tamanos_validos = ['14px', '16px', '18px'];
    $tamano = $_POST['tamano_letra'] ?? '16px';
    $config_a_guardar['tamano_letra'] = in_array($tamano, $tamanos_validos) ? $tamano : '16px';
    
    // Color principal (validar formato hex)
    $color = $_POST['color_principal'] ?? '#007bff';
    if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
        $config_a_guardar['color_principal'] = $color;
    } else {
        $errores[] = 'El color principal no es válido.';
    }
    
    // Procesar errores o guardar
    if (!empty($errores)) {
        foreach ($errores as $error) {
            $mensaje .= mostrarMensaje('danger', $error);
        }
    } else {
        $todos_guardados = true;
        foreach ($config_a_guardar as $clave => $valor) {
            $resultado = guardarConfig($pdo, $clave, $valor);
            if (!$resultado['éxito']) {
                $todos_guardados = false;
                $mensaje .= mostrarMensaje('danger', "Error al guardar {$clave}");
            }
        }
        
        if ($todos_guardados) {
            $mensaje .= mostrarMensaje('success', '¡Configuración guardada exitosamente!');
        }
    }
}


// CARGAR VALORES ACTUALES

$config_db = [];
if ($conexion_valida) {
    $config_db = cargarTodasLasConfiguraciones($pdo);
}

// Asignar valores actuales (BD o por defecto)
foreach ($defaults as $clave => $valor_defecto) {
    $valor_actual = $config_db[$clave] ?? $valor_defecto;
    ${"config_" . $clave} = htmlspecialchars($valor_actual, ENT_QUOTES, 'UTF-8');
}

// Incluir sidebar
include_once '../includes/silebar_admin.php';
?>

<div class="main-content px-3">
    <div class="container-fluid">
        <h3 class="my-4 ms-3 fw-bold">
            <i class="fas fa-cog me-2"></i>Configuración del Sistema
        </h3>

        <?php echo $mensaje; ?>

        <?php if ($conexion_valida): ?>
            <form method="POST" action="" novalidate>
                <!-- ===== SECCIÓN 1: INFORMACIÓN GENERAL ===== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-info-circle me-2"></i>Información General del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_sistema" class="form-label fw-semibold">
                                    Nombre del Sistema <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="nombre_sistema" 
                                    name="nombre_sistema" 
                                    value="<?php echo $config_nombre_sistema; ?>" 
                                    maxlength="255"
                                    required>
                                <small class="form-text text-muted">Nombre que aparece en encabezados y navegación.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_soporte" class="form-label fw-semibold">
                                    Email de Soporte
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email_soporte" 
                                    name="email_soporte" 
                                    value="<?php echo $config_email_soporte; ?>">
                                <small class="form-text text-muted">Correo para consultas técnicas.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== SECCIÓN 2: APARIENCIA ===== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-secondary text-white">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-palette me-2"></i>Apariencia y Estilos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tema_sistema" class="form-label fw-semibold">Tema Visual</label>
                                <select class="form-select" id="tema_sistema" name="tema_sistema">
                                    <option value="claro" <?php echo $config_tema_sistema === 'claro' ? 'selected' : ''; ?>>
                                        ☀️ Claro (Light Mode)
                                    </option>
                                    <option value="oscuro" <?php echo $config_tema_sistema === 'oscuro' ? 'selected' : ''; ?>>
                                        🌙 Oscuro (Dark Mode)
                                    </option>
                                    <option value="azul" <?php echo $config_tema_sistema === 'azul' ? 'selected' : ''; ?>>
                                        🔵 Azul Empresarial
                                    </option>
                                    <option value="contraste" <?php echo $config_tema_sistema === 'contraste' ? 'selected' : ''; ?>>
                                        ⚫ Alto Contraste
                                    </option>
                                </select>
                                <small class="form-text text-muted">Paleta de colores principal del sistema.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="color_principal" class="form-label fw-semibold">Color Principal</label>
                                <div class="input-group">
                                    <input 
                                        type="color" 
                                        class="form-control form-control-color" 
                                        id="color_principal" 
                                        name="color_principal" 
                                        value="<?php echo $config_color_principal; ?>" 
                                        title="Seleccionar color">
                                    <span class="input-group-text"><?php echo $config_color_principal; ?></span>
                                </div>
                                <small class="form-text text-muted">Color para botones y elementos destacados.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fuente_sistema" class="form-label fw-semibold">Tipografía</label>
                                <select class="form-select" id="fuente_sistema" name="fuente_sistema">
                                    <option value="Arial, sans-serif" <?php echo strpos($config_fuente_sistema, 'Arial') !== false ? 'selected' : ''; ?>>
                                        Arial
                                    </option>
                                    <option value="Roboto, sans-serif" <?php echo strpos($config_fuente_sistema, 'Roboto') !== false ? 'selected' : ''; ?>>
                                        Roboto
                                    </option>
                                    <option value="'Times New Roman', serif" <?php echo strpos($config_fuente_sistema, 'Times New Roman') !== false ? 'selected' : ''; ?>>
                                        Times New Roman
                                    </option>
                                    <option value="'Inter', sans-serif" <?php echo strpos($config_fuente_sistema, 'Inter') !== false ? 'selected' : ''; ?>>
                                        Inter (Moderno)
                                    </option>
                                </select>
                                <small class="form-text text-muted">Fuente utilizada en toda la interfaz.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tamano_letra" class="form-label fw-semibold">Tamaño de Letra</label>
                                <select class="form-select" id="tamano_letra" name="tamano_letra">
                                    <option value="14px" <?php echo $config_tamano_letra === '14px' ? 'selected' : ''; ?>>
                                        14px - Pequeño
                                    </option>
                                    <option value="16px" <?php echo $config_tamano_letra === '16px' ? 'selected' : ''; ?>>
                                        16px - Normal
                                    </option>
                                    <option value="18px" <?php echo $config_tamano_letra === '18px' ? 'selected' : ''; ?>>
                                        18px - Grande
                                    </option>
                                </select>
                                <small class="form-text text-muted">Accesibilidad: ajusta el tamaño para mejor legibilidad.</small>
                            </div>
                        </div>

                        <!-- Vista Previa -->
                        <div class="alert alert-info mt-3">
                            <strong>🔍 Vista Previa:</strong>
                            <div id="preview" style="
                                font-family: <?php echo $config_fuente_sistema; ?>;
                                font-size: <?php echo $config_tamano_letra; ?>;
                                color: <?php echo $config_color_principal; ?>;
                                padding: 10px;
                                background: #f5f5f5;
                                border-left: 4px solid <?php echo $config_color_principal; ?>;
                                margin-top: 10px;">
                                Este es el aspecto de tu sistema con la configuración actual.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== SECCIÓN 3: MANTENIMIENTO ===== -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-warning text-dark">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas fa-tools me-2"></i>Mantenimiento del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-warning btn-sm" id="recargarCache">
                                    <i class="fas fa-sync-alt me-1"></i>Recargar Caché
                                </button>
                                <small class="d-block form-text text-muted mt-2">
                                    Limpia el caché y recarga la configuración.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-info btn-sm" id="generarBackup">
                                    <i class="fas fa-download me-1"></i>Exportar Configuración
                                </button>
                                <small class="d-block form-text text-muted mt-2">
                                    Descarga un backup de la configuración actual.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== BOTONES DE ACCIÓN ===== -->
                <div class="row mb-4">
                    <div class="col-12">
                        <button type="submit" name="guardar_config" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-save me-2"></i>Guardar Todos los Cambios
                        </button>
                    </div>
                </div>
            </form>

        <?php else: ?>
            <div class="alert alert-danger">
                <h4>⚠️ Error de Conexión</h4>
                <p>No se puede acceder a la configuración del sistema debido a un error en la conexión a la base de datos.</p>
                <p>Por favor, contacta al administrador técnico.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Actualizar vista previa en tiempo real
    document.getElementById('tema_sistema')?.addEventListener('change', function() {
        const preview = document.getElementById('preview');
        preview.textContent = 'Vista previa con el tema: ' + this.value;
    });

    document.getElementById('color_principal')?.addEventListener('change', function() {
        const preview = document.getElementById('preview');
        preview.style.color = this.value;
        preview.style.borderLeftColor = this.value;
        const spanColor = this.parentElement.querySelector('.input-group-text');
        if (spanColor) spanColor.textContent = this.value;
    });

    document.getElementById('fuente_sistema')?.addEventListener('change', function() {
        const preview = document.getElementById('preview');
        preview.style.fontFamily = this.value;
    });

    document.getElementById('tamano_letra')?.addEventListener('change', function() {
        const preview = document.getElementById('preview');
        preview.style.fontSize = this.value;
    });

    // Recargar caché
    document.getElementById('recargarCache')?.addEventListener('click', function() {
        if (confirm('¿Estás seguro de que deseas recargar el caché?')) {
            // Aquí puedes añadir una llamada AJAX a un endpoint de limpieza
            alert('Caché recargado exitosamente');
            location.reload();
        }
    });

    // Exportar configuración
    document.getElementById('generarBackup')?.addEventListener('click', function() {
        const configs = {
            nombre_sistema: document.getElementById('nombre_sistema').value,
            email_soporte: document.getElementById('email_soporte').value,
            tema_sistema: document.getElementById('tema_sistema').value,
            fuente_sistema: document.getElementById('fuente_sistema').value,
            tamano_letra: document.getElementById('tamano_letra').value,
            color_principal: document.getElementById('color_principal').value,
            fecha_exportacion: new Date().toLocaleString('es-ES')
        };
        
        const dataStr = JSON.stringify(configs, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'configuracion_themis_' + new Date().getTime() + '.json';
        link.click();
    });
</script>

<?php include_once '../includes/footer.php'; ?>