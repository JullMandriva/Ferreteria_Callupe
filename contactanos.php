<?php
session_start();
include 'config/db.php';
include 'includes/header_public.php';

$mensaje_enviado = false;
$error = '';

// Procesar formulario de contacto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $asunto = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);
    $tipo_consulta = $_POST['tipo_consulta'];
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($mensaje) || empty($asunto)) {
        $error = "Por favor complete todos los campos obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingrese un email válido";
    } else {
        try {
            // Guardar en base de datos
            $stmt = $pdo->prepare("INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, tipo_consulta) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $email, $telefono, $asunto, $mensaje, $tipo_consulta]);
            
            // Aquí podrías agregar envío de email
            $mensaje_enviado = true;
            
        } catch (Exception $e) {
            $error = "Error al enviar el mensaje. Por favor intente nuevamente.";
        }
    }
}
?>

<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Contáctanos</h1>
        <div class="d-flex align-items-center">
            <a href="index.php" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
            </a>
            <?php include 'includes/carrito_boton.php'; ?>
        </div>
    </div>
    
    <?php if ($mensaje_enviado): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>¡Mensaje enviado!</strong> Nos pondremos en contacto contigo en las próximas 24 horas.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Información de contacto -->
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title mb-4">Información de Contacto</h4>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-geo-alt fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Dirección</h6>
                            <p class="mb-0">Av. Ferretería 123, Lima 15001<br>Lima, Perú</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-telephone fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Teléfonos</h6>
                            <p class="mb-1">
                                <a href="tel:+5112345678" class="text-decoration-none">(01) 234-5678</a>
                            </p>
                            <p class="mb-0">
                                <a href="tel:+51987654321" class="text-decoration-none">987 654 321</a> (WhatsApp)
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-envelope fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Email</h6>
                            <p class="mb-0">
                                <a href="mailto:info@ferretodo.com" class="text-decoration-none">
                                    info@ferretodo.com
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Horario de Atención</h6>
                            <p class="mb-1">Lunes a Viernes: 8:00 AM - 8:00 PM</p>
                            <p class="mb-0">Sábados: 9:00 AM - 6:00 PM</p>
                            <p class="mb-0">Domingos: 9:00 AM - 2:00 PM</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <i class="bi bi-shop fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Sucursales</h6>
                            <p class="mb-1">• Central: Av. Ferretería 123</p>
                            <p class="mb-1">• Norte: Mall Plaza Norte</p>
                            <p class="mb-0">• Sur: Centro Comercial Sur</p>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Síguenos en redes sociales</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-instagram"></i> Instagram
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulario de contacto -->
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Envíanos un mensaje</h4>
                    
                    <form method="post" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre completo *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="asunto" class="form-label">Asunto *</label>
                                <input type="text" class="form-control" id="asunto" name="asunto" 
                                       value="<?php echo isset($_POST['asunto']) ? htmlspecialchars($_POST['asunto']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="col-12">
                                <label for="tipo_consulta" class="form-label">Tipo de consulta *</label>
                                <select class="form-select" id="tipo_consulta" name="tipo_consulta" required>
                                    <option value="">Seleccione una opción...</option>
                                    <option value="consulta" <?php echo (isset($_POST['tipo_consulta']) && $_POST['tipo_consulta'] == 'consulta') ? 'selected' : ''; ?>>Consulta general</option>
                                    <option value="cotizacion" <?php echo (isset($_POST['tipo_consulta']) && $_POST['tipo_consulta'] == 'cotizacion') ? 'selected' : ''; ?>>Cotización</option>
                                    <option value="soporte" <?php echo (isset($_POST['tipo_consulta']) && $_POST['tipo_consulta'] == 'soporte') ? 'selected' : ''; ?>>Soporte técnico</option>
                                    <option value="reclamo" <?php echo (isset($_POST['tipo_consulta']) && $_POST['tipo_consulta'] == 'reclamo') ? 'selected' : ''; ?>>Reclamo o queja</option>
                                    <option value="garantia" <?php echo (isset($_POST['tipo_consulta']) && $_POST['tipo_consulta'] == 'garantia') ? 'selected' : ''; ?>>Garantía</option>
                                    <option value="empresa" <?php echo (isset($_POST['tipo_consulta']) && $_POST['tipo_consulta'] == 'empresa') ? 'selected' : ''; ?>>Ventas corporativas</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="mensaje" class="form-label">Mensaje *</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" 
                                          rows="5" required><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacidad" required>
                                    <label class="form-check-label" for="privacidad">
                                        Acepto la <a href="#" data-bs-toggle="modal" data-bs-target="#modalPrivacidad">política de privacidad</a> *
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i> Enviar mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Mapa -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Nuestra ubicación</h5>
                    <div class="ratio ratio-16x9">
                        <!-- Mapa de Google Maps (puedes reemplazar el iframe con tu ubicación real) -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3901.013887209508!2d-77.04214242467719!3d-12.12143094429245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c8193f8d36c1%3A0xbb7c5c8b4c8c4c4c!2sPlaza%20Mayor%20de%20Lima!5e0!3m2!1ses!2spe!4v1695678901234!5m2!1ses!2spe" 
                                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Política de Privacidad -->
<div class="modal fade" id="modalPrivacidad">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Política de Privacidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Uso de la información</h6>
                <p>La información que nos proporciona será utilizada únicamente para:</p>
                <ul>
                    <li>Responder a su consulta o solicitud</li>
                    <li>Proveer los servicios solicitados</li>
                    <li>Mejorar nuestra atención al cliente</li>
                    <li>Enviar información sobre promociones (solo si autoriza)</li>
                </ul>
                <h6 class="mt-4">Protección de datos</h6>
                <p>Nos comprometemos a proteger su información personal y no la compartiremos con terceros sin su consentimiento.</p>
                <h6 class="mt-4">Derechos del usuario</h6>
                <p>Usted tiene derecho a acceder, rectificar o eliminar sus datos personales en cualquier momento.</p>
            </div>
        </div>
    </div>
</div>

<!-- Crear tabla de contactos si no existe -->
<?php
// Verificar y crear tabla contactos si no existe
try {
    $pdo->query("CREATE TABLE IF NOT EXISTS contactos (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        telefono VARCHAR(20),
        asunto VARCHAR(200) NOT NULL,
        mensaje TEXT NOT NULL,
        tipo_consulta VARCHAR(50),
        fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        leido BOOLEAN DEFAULT FALSE
    )");
} catch (Exception $e) {
    // Silenciar error si la tabla ya existe
}
?>

<?php include 'includes/footer_public.php'; ?>