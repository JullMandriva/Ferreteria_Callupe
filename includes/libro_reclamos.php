<?php
session_start();
include 'config/db.php';
include 'includes/header_public.php';

// Generar código único para el reclamo
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
        $tipo_reclamo = $_POST['tipo_reclamo'];
        $fecha_incidente = $_POST['fecha_incidente'];
        
        // Datos del reclamante
        $tipo_documento = $_POST['tipo_documento'];
        $numero_documento = $_POST['numero_documento'];
        $nombres_apellidos = trim($_POST['nombres_apellidos']);
        $domicilio = trim($_POST['domicilio']);
        $departamento = $_POST['departamento'];
        $provincia = $_POST['provincia'];
        $distrito = $_POST['distrito'];
        $telefono = trim($_POST['telefono']);
        $email = trim($_POST['email']);
        
        // Detalles del reclamo
        $descripcion_hechos = trim($_POST['descripcion_hechos']);
        $pedido_reclamo = trim($_POST['pedido_reclamo']);
        $monto_reclamado = floatval($_POST['monto_reclamado']);
        
        // Información del bien/servicio
        $tipo_bien = $_POST['tipo_bien'];
        $descripcion_bien = trim($_POST['descripcion_bien']);
        $marca = trim($_POST['marca']);
        $modelo = trim($_POST['modelo']);
        $numero_serie = trim($_POST['numero_serie']);
        
        // Información de compra
        $tipo_comprobante = $_POST['tipo_comprobante'];
        $numero_comprobante = trim($_POST['numero_comprobante']);
        $fecha_compra = $_POST['fecha_compra'];
        $monto_compra = floatval($_POST['monto_compra']);
        
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
            departamento, provincia, distrito, telefono, email,
            descripcion_hechos, pedido_reclamo, monto_reclamado,
            tipo_bien, descripcion_bien, marca, modelo, numero_serie,
            tipo_comprobante, numero_comprobante, fecha_compra, monto_compra
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $codigo_reclamo, $tipo_reclamo, $fecha_incidente,
            $tipo_documento, $numero_documento, $nombres_apellidos, $domicilio,
            $departamento, $provincia, $distrito, $telefono, $email,
            $descripcion_hechos, $pedido_reclamo, $monto_reclamado,
            $tipo_bien, $descripcion_bien, $marca, $modelo, $numero_serie,
            $tipo_comprobante, $numero_comprobante, $fecha_compra, $monto_compra
        ]);
        
        $reclamo_id = $pdo->lastInsertId();
        
        // Registrar seguimiento inicial
        $sql_seguimiento = "INSERT INTO seguimiento_reclamos (reclamo_id, accion, descripcion, estado_anterior, estado_nuevo) 
                           VALUES (?, 'Registro', 'Reclamo registrado en el sistema', NULL, 'registrado')";
        $stmt = $pdo->prepare($sql_seguimiento);
        $stmt->execute([$reclamo_id]);
        
        $pdo->commit();
        
        $mensaje_exito = "✅ Reclamo registrado exitosamente. Su código de reclamo es: <strong>$codigo_reclamo</strong>";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensaje_error = "❌ Error: " . $e->getMessage();
    }
}

// Departamentos del Perú (simplificado)
$departamentos = [
    'Amazonas', 'Áncash', 'Apurímac', 'Arequipa', 'Ayacucho',
    'Cajamarca', 'Callao', 'Cusco', 'Huancavelica', 'Huánuco',
    'Ica', 'Junín', 'La Libertad', 'Lambayeque', 'Lima',
    'Loreto', 'Madre de Dios', 'Moquegua', 'Pasco', 'Piura',
    'Puno', 'San Martín', 'Tacna', 'Tumbes', 'Ucayali'
];
?>

<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="bi bi-journal-text text-primary me-2"></i> Libro de Reclamos Digital
        </h1>
        <div class="d-flex align-items-center">
            <?php include 'includes/carrito_boton.php'; ?>
            <a href="../index.php" class="btn btn-outline-secondary ms-3">
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
    
    <!-- Formulario de reclamo -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Formulario de Registro de Reclamo</h4>
        </div>
        <div class="card-body">
            <form method="post" id="formReclamo" novalidate>
                
                <!-- Sección 1: Tipo de Reclamo -->
                <div class="mb-5">
                    <h5 class="border-bottom pb-2 mb-4">
                        <i class="bi bi-card-checklist me-2"></i> 1. Tipo de Reclamo
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo de Reclamo *</label>
                            <select class="form-select" name="tipo_reclamo" required>
                                <option value="">Seleccione...</option>
                                <option value="reclamo">Reclamo</option>
                                <option value="queja">Queja</option>
                                <option value="sugerencia">Sugerencia</option>
                            </select>
                            <div class="invalid-feedback">Seleccione el tipo de reclamo</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha del Incidente *</label>
                            <input type="date" class="form-control" name="fecha_incidente" 
                                   max="<?php echo date('Y-m-d'); ?>" required>
                            <div class="invalid-feedback">Seleccione la fecha del incidente</div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 2: Datos del Reclamante -->
                <div class="mb-5">
                    <h5 class="border-bottom pb-2 mb-4">
                        <i class="bi bi-person-vcard me-2"></i> 2. Datos del Reclamante
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo de Documento *</label>
                            <select class="form-select" name="tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">Carnet Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                            <div class="invalid-feedback">Seleccione tipo de documento</div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Número de Documento *</label>
                            <input type="text" class="form-control" name="numero_documento" required>
                            <div class="invalid-feedback">Ingrese su número de documento</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombres y Apellidos Completos *</label>
                            <input type="text" class="form-control" name="nombres_apellidos" required>
                            <div class="invalid-feedback">Ingrese sus nombres y apellidos</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Domicilio *</label>
                            <input type="text" class="form-control" name="domicilio" required>
                            <div class="invalid-feedback">Ingrese su dirección</div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="departamento" id="departamento">
                                <option value="">Seleccione...</option>
                                <?php foreach ($departamentos as $dep): ?>
                                    <option value="<?php echo $dep; ?>"><?php echo $dep; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Provincia</label>
                            <input type="text" class="form-control" name="provincia">
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Distrito</label>
                            <input type="text" class="form-control" name="distrito">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Teléfono/Celular *</label>
                            <input type="tel" class="form-control" name="telefono" required>
                            <div class="invalid-feedback">Ingrese su teléfono</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                            <div class="invalid-feedback">Ingrese un email válido</div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 3: Detalles del Reclamo -->
                <div class="mb-5">
                    <h5 class="border-bottom pb-2 mb-4">
                        <i class="bi bi-chat-left-text me-2"></i> 3. Detalles del Reclamo
                    </h5>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción de los Hechos *</label>
                            <textarea class="form-control" name="descripcion_hechos" rows="5" 
                                      placeholder="Describa detalladamente lo sucedido, incluyendo fechas, lugares, personas involucradas..." 
                                      required></textarea>
                            <div class="invalid-feedback">Describa los hechos del reclamo</div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Pedido/Reclamo *</label>
                            <textarea class="form-control" name="pedido_reclamo" rows="3" 
                                      placeholder="¿Qué solicita usted con este reclamo? (Ej: devolución, cambio, reparación, descuento...)" 
                                      required></textarea>
                            <div class="invalid-feedback">Especifique qué solicita con este reclamo</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Monto Reclamado (S/)</label>
                            <input type="number" class="form-control" name="monto_reclamado" 
                                   min="0" step="0.01" value="0">
                        </div>
                    </div>
                </div>
                
                <!-- Sección 4: Información del Bien/Servicio -->
                <div class="mb-5">
                    <h5 class="border-bottom pb-2 mb-4">
                        <i class="bi bi-box-seam me-2"></i> 4. Información del Bien o Servicio
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select class="form-select" name="tipo_bien" required>
                                <option value="">Seleccione...</option>
                                <option value="producto">Producto</option>
                                <option value="servicio">Servicio</option>
                                <option value="ambos">Ambos</option>
                            </select>
                            <div class="invalid-feedback">Seleccione el tipo</div>
                        </div>
                        
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Descripción del Bien/Servicio</label>
                            <textarea class="form-control" name="descripcion_bien" rows="2" 
                                      placeholder="Describa el producto o servicio involucrado"></textarea>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Número de Serie</label>
                            <input type="text" class="form-control" name="numero_serie">
                        </div>
                    </div>
                </div>
                
                <!-- Sección 5: Información de Compra -->
                <div class="mb-5">
                    <h5 class="border-bottom pb-2 mb-4">
                        <i class="bi bi-receipt me-2"></i> 5. Información de Compra (si aplica)
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo de Comprobante</label>
                            <select class="form-select" name="tipo_comprobante">
                                <option value="ninguno">No tengo</option>
                                <option value="boleta">Boleta</option>
                                <option value="factura">Factura</option>
                                <option value="ticket">Ticket</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Número de Comprobante</label>
                            <input type="text" class="form-control" name="numero_comprobante">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Fecha de Compra</label>
                            <input type="date" class="form-control" name="fecha_compra" 
                                   max="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Monto de Compra (S/)</label>
                            <input type="number" class="form-control" name="monto_compra" 
                                   min="0" step="0.01" value="0">
                        </div>
                    </div>
                </div>
                
                <!-- Sección 6: Declaración y envío -->
                <div class="mb-4">
                    <div class="border p-4 rounded bg-light">
                        <h6 class="mb-3"><i class="bi bi-shield-check me-2"></i> Declaración del Reclamante</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="declaracion_veracidad" required>
                            <label class="form-check-label" for="declaracion_veracidad">
                                Declaro bajo protesta de decir verdad que la información proporcionada es veraz y completa.
                            </label>
                            <div class="invalid-feedback">Debe aceptar la declaración de veracidad</div>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="acepto_tratamiento" required>
                            <label class="form-check-label" for="acepto_tratamiento">
                                Autorizo el tratamiento de mis datos personales para los fines del presente reclamo.
                            </label>
                            <div class="invalid-feedback">Debe autorizar el tratamiento de datos</div>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de envío -->
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
                <i class="bi bi-telephone me-2"></i> Información de Contacto - Ferretería
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
    
    <!-- Consulta de reclamo existente -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-search me-2"></i> Consultar Estado de Reclamo
            </h5>
            <p>Si ya tiene un código de reclamo, ingréselo para consultar su estado:</p>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="codigoConsulta" 
                               placeholder="Ej: REC-20231215-ABC123">
                        <button class="btn btn-outline-primary" type="button" onclick="consultarReclamo()">
                            <i class="bi bi-search me-1"></i> Consultar
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="resultado-consulta" class="mt-3"></div>
        </div>
    </div>
</div>

<!-- Modal para mostrar resultados de consulta -->
<div class="modal fade" id="modalConsultaReclamo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estado del Reclamo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-consulta-content">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
// Validación del formulario
document.getElementById('formReclamo').addEventListener('submit', function(event) {
    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    this.classList.add('was-validated');
});

// Limpiar formulario
function limpiarFormulario() {
    if (confirm('¿Está seguro de limpiar todo el formulario?')) {
        document.getElementById('formReclamo').reset();
        document.getElementById('formReclamo').classList.remove('was-validated');
    }
}

// Consultar reclamo por código
function consultarReclamo() {
    const codigo = document.getElementById('codigoConsulta').value.trim();
    
    if (!codigo) {
        alert('Ingrese un código de reclamo');
        return;
    }
    
    // Mostrar loading
    const resultado = document.getElementById('resultado-consulta');
    resultado.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div></div>';
    
    // Consultar al servidor
    fetch('consultar_reclamo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'codigo=' + encodeURIComponent(codigo)
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('modal-consulta-content').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('modalConsultaReclamo'));
        modal.show();
        resultado.innerHTML = '';
    })
    .catch(error => {
        resultado.innerHTML = '<div class="alert alert-danger">Error al consultar el reclamo</div>';
        console.error('Error:', error);
    });
}

// Validación en tiempo real de DNI/RUC
document.querySelector('input[name="numero_documento"]').addEventListener('input', function(e) {
    const tipo = document.querySelector('select[name="tipo_documento"]').value;
    const valor = e.target.value.replace(/\D/g, '');
    
    if (tipo === 'DNI' && valor.length > 8) {
        e.target.value = valor.substring(0, 8);
    } else if (tipo === 'RUC' && valor.length > 11) {
        e.target.value = valor.substring(0, 11);
    }
});

// Fecha máxima hoy
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.max = new Date().toISOString().split('T')[0];
});
</script>

<style>
<style>
/* Estilos específicos para el libro de reclamos */
.libro-reclamos-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Encabezado */
.libro-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    color: white;
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
}

.libro-header h1 {
    font-weight: 700;
    font-size: 2.5rem;
}

.libro-header .badge {
    background-color: rgba(255, 255, 255, 0.2);
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

/* Formulario */
.form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid #0d6efd;
    transition: all 0.3s ease;
}

.form-section:hover {
    background: #f0f7ff;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.form-section h5 {
    color: #0d6efd;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.form-section h5 i {
    font-size: 1.2em;
}

/* Etiquetas de formulario */
.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

/* Campos de formulario */
.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #dee2e6;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    transform: translateY(-2px);
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545;
}

/* Textareas */
textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

/* Checkboxes personalizados */
.form-check-input {
    width: 1.2em;
    height: 1.2em;
    margin-top: 0.2em;
    border: 2px solid #6c757d;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label {
    color: #495057;
    font-weight: 500;
}

/* Botones */
.btn-libro {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-libro-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    color: white;
}

.btn-libro-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
}

.btn-libro-secondary {
    background: #6c757d;
    border: none;
    color: white;
}

.btn-libro-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

/* Cards informativas */
.info-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.info-card .card-header {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
    color: white;
    font-weight: 600;
    border: none;
    padding: 1rem 1.5rem;
}

.info-card .card-body {
    padding: 1.5rem;
}

/* Alertas personalizadas */
.alert-libro {
    border-radius: 10px;
    border: none;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.alert-libro-info {
    background: linear-gradient(135deg, #e7f1ff 0%, #d4e3ff 100%);
    border-left: 4px solid #0d6efd;
    color: #084298;
}

.alert-libro-success {
    background: linear-gradient(135deg, #d1e7dd 0%, #bfe3ce 100%);
    border-left: 4px solid #198754;
    color: #0f5132;
}

.alert-libro-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
    border-left: 4px solid #dc3545;
    color: #842029;
}

/* Timeline para seguimiento */
.timeline-libro {
    position: relative;
    padding-left: 30px;
}

.timeline-libro::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #0d6efd, #198754);
    border-radius: 3px;
}

.timeline-item-libro {
    position: relative;
    margin-bottom: 2rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.timeline-item-libro::before {
    content: '';
    position: absolute;
    left: -25px;
    top: 20px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #0d6efd;
    border: 3px solid white;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
}

.timeline-item-libro.estado-registrado::before { background: #6c757d; }
.timeline-item-libro.estado-revision::before { background: #ffc107; }
.timeline-item-libro.estado-procesado::before { background: #0d6efd; }
.timeline-item-libro.estado-resuelto::before { background: #198754; }

/* Badges personalizados */
.badge-estado {
    font-size: 0.9em;
    font-weight: 600;
    padding: 0.5em 1em;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-estado-registrado { background-color: #6c757d; color: white; }
.badge-estado-revision { background-color: #ffc107; color: #000; }
.badge-estado-procesado { background-color: #0d6efd; color: white; }
.badge-estado-resuelto { background-color: #198754; color: white; }
.badge-estado-archivado { background-color: #6c757d; color: white; }

/* Responsive */
@media (max-width: 768px) {
    .libro-header {
        padding: 1.5rem;
        text-align: center;
    }
    
    .libro-header h1 {
        font-size: 2rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .btn-libro {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .timeline-libro {
        padding-left: 20px;
    }
    
    .timeline-item-libro::before {
        left: -17px;
    }
}

/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-section {
    animation: fadeIn 0.5s ease-out;
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}

/* Placeholders personalizados */
::placeholder {
    color: #6c757d;
    opacity: 0.7;
}

/* Tablas */
.table-libro {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.table-libro thead {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    color: white;
}

.table-libro th {
    font-weight: 600;
    border: none;
    padding: 1rem;
}

.table-libro td {
    border-color: #e9ecef;
    padding: 1rem;
    vertical-align: middle;
}

/* Contador de caracteres */
.char-counter {
    font-size: 0.85rem;
    color: #6c757d;
    text-align: right;
    margin-top: 0.25rem;
}

.char-counter.near-limit {
    color: #ffc107;
}

.char-counter.over-limit {
    color: #dc3545;
}

/* Spinner personalizado */
.spinner-libro {
    width: 3rem;
    height: 3rem;
    border-width: 0.25em;
    color: #0d6efd;
}

/* Modal personalizado */
.modal-libro .modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-libro .modal-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    border: none;
    padding: 1.5rem;
}

.modal-libro .modal-body {
    padding: 2rem;
}

/* Tooltips */
.tooltip-libro {
    font-size: 0.9rem;
    border-radius: 6px;
}

/* Iconos */
.icon-libro {
    font-size: 1.2em;
    vertical-align: middle;
    margin-right: 0.5rem;
}

/* Separadores */
.separator {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 2rem 0;
    color: #6c757d;
}

.separator::before,
.separator::after {
    content: '';
    flex: 1;
    border-bottom: 2px solid #dee2e6;
}

.separator span {
    padding: 0 1rem;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
}

/* Accordion personalizado */
.accordion-libro .accordion-item {
    border: none;
    border-radius: 8px;
    margin-bottom: 1rem;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.accordion-libro .accordion-button {
    background: #f8f9fa;
    font-weight: 600;
    color: #0d6efd;
    border: none;
    padding: 1rem 1.5rem;
}

.accordion-libro .accordion-button:not(.collapsed) {
    background: #e7f1ff;
    color: #0b5ed7;
    box-shadow: none;
}

.accordion-libro .accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}
</style>


<?php include 'includes/footer_public.php'; ?>