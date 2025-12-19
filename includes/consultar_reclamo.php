<?php
session_start();
include 'config/db.php';

header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo'])) {
    $codigo = trim($_POST['codigo']);
    
    if (empty($codigo)) {
        echo '<div class="alert alert-warning">Ingrese un código de reclamo</div>';
        exit;
    }
    
    // Consultar reclamo
    $stmt = $pdo->prepare("
        SELECT r.*, 
               DATE_FORMAT(r.fecha_registro, '%d/%m/%Y %H:%i') as fecha_registro_format,
               DATE_FORMAT(r.fecha_incidente, '%d/%m/%Y') as fecha_incidente_format,
               DATE_FORMAT(r.fecha_respuesta, '%d/%m/%Y') as fecha_respuesta_format,
               (SELECT COUNT(*) FROM seguimiento_reclamos sr WHERE sr.reclamo_id = r.id) as num_seguimientos
        FROM reclamos r 
        WHERE r.codigo_reclamo = ?
    ");
    $stmt->execute([$codigo]);
    $reclamo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reclamo) {
        echo '<div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                No se encontró ningún reclamo con el código: <strong>' . htmlspecialchars($codigo) . '</strong>
              </div>';
        exit;
    }
    
    // Obtener seguimientos
    $stmt = $pdo->prepare("
        SELECT *, DATE_FORMAT(fecha_seguimiento, '%d/%m/%Y %H:%i') as fecha_format 
        FROM seguimiento_reclamos 
        WHERE reclamo_id = ? 
        ORDER BY fecha_seguimiento DESC
    ");
    $stmt->execute([$reclamo['id']]);
    $seguimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Colores según estado
    $estado_colores = [
        'registrado' => 'info',
        'en_revision' => 'warning',
        'procesado' => 'primary',
        'resuelto' => 'success',
        'archivado' => 'secondary'
    ];
    
    $estado_texto = [
        'registrado' => 'Registrado',
        'en_revision' => 'En Revisión',
        'procesado' => 'Procesado',
        'resuelto' => 'Resuelto',
        'archivado' => 'Archivado'
    ];
    ?>
    
    <div class="reclamo-info">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Reclamo: <span class="text-primary"><?php echo $reclamo['codigo_reclamo']; ?></span></h4>
                <p class="text-muted mb-0">Registrado el: <?php echo $reclamo['fecha_registro_format']; ?></p>
            </div>
            <span class="badge bg-<?php echo $estado_colores[$reclamo['estado']] ?? 'secondary'; ?> fs-6">
                <?php echo $estado_texto[$reclamo['estado']] ?? $reclamo['estado']; ?>
            </span>
        </div>
        
        <!-- Información básica -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Información del Reclamante</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Nombres:</strong></td>
                        <td><?php echo htmlspecialchars($reclamo['nombres_apellidos']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Documento:</strong></td>
                        <td><?php echo $reclamo['tipo_documento']; ?>: <?php echo $reclamo['numero_documento']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Teléfono:</strong></td>
                        <td><?php echo $reclamo['telefono'] ?: 'No especificado'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo $reclamo['email'] ?: 'No especificado'; ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h6>Detalles del Reclamo</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Tipo:</strong></td>
                        <td><?php echo ucfirst($reclamo['tipo_reclamo']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Fecha Incidente:</strong></td>
                        <td><?php echo $reclamo['fecha_incidente_format']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Monto Reclamado:</strong></td>
                        <td>S/ <?php echo number_format($reclamo['monto_reclamado'], 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Descripción y pedido -->
        <div class="mb-4">
            <h6>Descripción de los Hechos</h6>
            <div class="border p-3 rounded bg-light">
                <?php echo nl2br(htmlspecialchars($reclamo['descripcion_hechos'])); ?>
            </div>
        </div>
        
        <div class="mb-4">
            <h6>Pedido/Reclamo</h6>
            <div class="border p-3 rounded bg-light">
                <?php echo nl2br(htmlspecialchars($reclamo['pedido_reclamo'])); ?>
            </div>
        </div>
        
        <!-- Respuesta de la empresa (si existe) -->
        <?php if ($reclamo['respuesta_empresa']): ?>
        <div class="alert alert-success">
            <h6><i class="bi bi-check-circle me-2"></i> Respuesta de la Empresa</h6>
            <p class="mb-1"><strong>Fecha de respuesta:</strong> <?php echo $reclamo['fecha_respuesta_format']; ?></p>
            <div class="mt-2">
                <?php echo nl2br(htmlspecialchars($reclamo['respuesta_empresa'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Historial de seguimiento -->
        <div class="mt-4">
            <h6 class="border-bottom pb-2">
                <i class="bi bi-clock-history me-2"></i> Historial de Seguimiento
                <span class="badge bg-secondary ms-2"><?php echo $reclamo['num_seguimientos']; ?> registros</span>
            </h6>
            
            <?php if (empty($seguimientos)): ?>
                <p class="text-muted text-center py-3">No hay registros de seguimiento</p>
            <?php else: ?>
                <div class="timeline mt-3">
                    <?php foreach ($seguimientos as $seguimiento): ?>
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-marker bg-primary rounded-circle" 
                                 style="width: 12px; height: 12px; margin-top: 5px;"></div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo htmlspecialchars($seguimiento['accion']); ?></strong>
                                    <small class="text-muted"><?php echo $seguimiento['fecha_format']; ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($seguimiento['descripcion']); ?></p>
                                <?php if ($seguimiento['estado_anterior'] || $seguimiento['estado_nuevo']): ?>
                                    <small class="text-muted">
                                        Estado: <?php echo $seguimiento['estado_anterior'] ?: 'N/A'; ?> 
                                        → <?php echo $seguimiento['estado_nuevo'] ?: 'N/A'; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Información de contacto -->
        <div class="alert alert-info mt-4">
            <h6><i class="bi bi-info-circle me-2"></i> Información de Contacto</h6>
            <p class="mb-1">Si necesita más información sobre su reclamo, puede contactarnos:</p>
            <p class="mb-1"><strong>Teléfono:</strong> (01) 234-5678</p>
            <p class="mb-1"><strong>Email:</strong> reclamos@ferreteria.com</p>
            <p class="mb-0"><strong>Horario de atención:</strong> Lunes a Viernes de 9:00 AM a 6:00 PM</p>
        </div>
    </div>
    
    <style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
    }
    </style>
    <?php
} else {
    echo '<div class="alert alert-danger">Método no permitido</div>';
}
?>