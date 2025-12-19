<?php
session_start();
include 'config/db.php';
include 'includes/header_public.php';

// Función para generar código único
function generarCodigoReclamo() {
    $prefijo = 'REC';
    $fecha = date('Ymd');
    $random = strtoupper(substr(uniqid(), -6));
    return $prefijo . '-' . $fecha . '-' . $random;
}

$mensaje_exito = '';
$mensaje_error = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Datos básicos
        $codigo_reclamo = generarCodigoReclamo();
        $tipo_reclamo = $_POST['tipo_reclamo'] ?? '';
        $fecha_incidente = $_POST['fecha_incidente'] ?? '';
        
        // Datos del reclamante
        $tipo_documento = $_POST['tipo_documento'] ?? '';
        $numero_documento = $_POST['numero_documento'] ?? '';
        $nombres_apellidos = trim($_POST['nombres_apellidos'] ?? '');
        $domicilio = trim($_POST['domicilio'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Detalles del reclamo
        $descripcion_hechos = trim($_POST['descripcion_hechos'] ?? '');
        $pedido_reclamo = trim($_POST['pedido_reclamo'] ?? '');
        $monto_reclamado = floatval($_POST['monto_reclamado'] ?? 0);
        
        // Validaciones
        if (empty($nombres_apellidos)) {
            throw new Exception('Ingrese sus nombres y apellidos completos');
        }
        
        if (empty($descripcion_hechos)) {
            throw new Exception('Describa los hechos del reclamo');
        }
        
        if (empty($pedido_reclamo)) {
            throw new Exception('Especifique qué solicita con este reclamo');
        }
        
        // Insertar reclamo
        $sql = "INSERT INTO reclamos (
            codigo_reclamo, tipo_reclamo, fecha_incidente,
            tipo_documento, numero_documento, nombres_apellidos, domicilio,
            telefono, email, descripcion_hechos, pedido_reclamo, monto_reclamado
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $codigo_reclamo, $tipo_reclamo, $fecha_incidente,
            $tipo_documento, $numero_documento, $nombres_apellidos, $domicilio,
            $telefono, $email, $descripcion_hechos, $pedido_reclamo, $monto_reclamado
        ]);
        
        $reclamo_id = $pdo->lastInsertId();
        
        // Registrar seguimiento
        $sql_seguimiento = "INSERT INTO seguimiento_reclamos (reclamo_id, accion, descripcion) 
                           VALUES (?, 'Registro', 'Reclamo registrado en el sistema')";
        $stmt = $pdo->prepare($sql_seguimiento);
        $stmt->execute([$reclamo_id]);
        
        $pdo->commit();
        
        $mensaje_exito = "✅ Reclamo registrado exitosamente. Su código de reclamo es: <strong>$codigo_reclamo</strong>";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensaje_error = "❌ Error: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="bi bi-journal-text text-primary me-2"></i> Libro de Reclamos Digital
        </h1>
        <div class="d-flex align-items-center">
            <?php 
            // Contador del carrito
            $total_items = 0;
            if (isset($_SESSION['carrito'])) {
                foreach ($_SESSION['carrito'] as $item) {
                    if (is_array($item) && isset($item['cantidad'])) {
                        $total_items += intval($item['cantidad']);
                    }
                }
            }
            ?>
            <!-- Carrito -->
            <button class="btn btn-outline-primary position-relative me-3" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalCarrito">
                <i class="bi bi-cart3"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo $total_items; ?>
                </span>
            </button>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver al inicio
            </a>
        </div>
    </div>
    
    <!-- Información importante -->
    <div class="alert alert-info mb-4">
        <div class="d-flex">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <h5 class="alert-heading">Información Importante</h5>
                <p class="mb-2">Este Libro de Reclamos es oficial según la Ley N° 29571 - Código de Protección y Defensa del Consumidor.</p>
                <p class="mb-0"><strong>Plazo de respuesta:</strong> La empresa tiene 15 días hábiles para responder a su reclamo.</p>
            </div>
        </div>
    </div>
    
    <!-- Mensajes de éxito/error -->
    <?php if ($mensaje_exito): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($mensaje_error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $mensaje_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Formulario simplificado -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Formulario de Registro de Reclamo</h4>
        </div>
        <div class="card-body">
            <form method="post" id="formReclamo" novalidate>
                
                <!-- Datos del Reclamante -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-4">1. Datos del Reclamante</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombres y Apellidos Completos *</label>
                            <input type="text" class="form-control" name="nombres_apellidos" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo de Documento *</label>
                            <select class="form-select" name="tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">Carnet Extranjería</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Número de Documento *</label>
                            <input type="text" class="form-control" name="numero_documento" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Domicilio *</label>
                            <input type="text" class="form-control" name="domicilio" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Teléfono/Celular *</label>
                            <input type="tel" class="form-control" name="telefono" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                </div>
                
                <!-- Detalles del Reclamo -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-4">2. Detalles del Reclamo</h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo de Reclamo *</label>
                            <select class="form-select" name="tipo_reclamo" required>
                                <option value="">Seleccione...</option>
                                <option value="reclamo">Reclamo</option>
                                <option value="queja">Queja</option>
                                <option value="sugerencia">Sugerencia</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Fecha del Incidente *</label>
                            <input type="date" class="form-control" name="fecha_incidente" 
                                   max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Monto Reclamado (S/)</label>
                            <input type="number" class="form-control" name="monto_reclamado" 
                                   min="0" step="0.01" value="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción de los Hechos *</label>
                        <textarea class="form-control" name="descripcion_hechos" rows="4" 
                                  placeholder="Describa detalladamente lo sucedido..." 
                                  required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pedido/Reclamo *</label>
                        <textarea class="form-control" name="pedido_reclamo" rows="3" 
                                  placeholder="¿Qué solicita usted con este reclamo?" 
                                  required></textarea>
                    </div>
                </div>
                
                <!-- Declaración -->
                <div class="mb-4">
                    <div class="border p-4 rounded bg-light">
                        <h6 class="mb-3"><i class="bi bi-shield-check me-2"></i> Declaración del Reclamante</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="declaracion_veracidad" required>
                            <label class="form-check-label" for="declaracion_veracidad">
                                Declaro bajo protesta de decir verdad que la información proporcionada es veraz y completa.
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="acepto_tratamiento" required>
                            <label class="form-check-label" for="acepto_tratamiento">
                                Autorizo el tratamiento de mis datos personales para los fines del presente reclamo.
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                        <i class="bi bi-eraser me-2"></i> Limpiar Formulario
                    </button>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-send-check me-2"></i> Enviar Reclamo
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Información de contacto -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-telephone me-2"></i> Información de Contacto
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Razón Social:</strong> FERRETERÍA TOTTO E.I.R.L.</p>
                    <p class="mb-2"><strong>RUC:</strong> 20123456789</p>
                    <p class="mb-2"><strong>Dirección:</strong> Av. Principal 123, Lima</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Teléfono:</strong> (01) 234-5678</p>
                    <p class="mb-2"><strong>Email:</strong> reclamos@ferreteria.com</p>
                    <p class="mb-2"><strong>Horario de Atención:</strong> Lunes a Viernes 9:00 AM - 6:00 PM</p>
                </div>
            </div>
        </div>
    </div>
</div>




<?php include 'includes/footer_public.php'; ?>