<?php
session_start();
include 'config/db.php';
include 'includes/header_public.php';

if (!isset($_GET['id_venta'])) {
    header('Location: index.php');
    exit;
}

$id_venta = $_GET['id_venta'];

// Obtener datos de la venta
$stmt = $pdo->prepare("SELECT v.*, u.nombre as vendedor FROM ventas v 
                       LEFT JOIN usuarios u ON v.id_usuario = u.id 
                       WHERE v.id = ?");
$stmt->execute([$id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("Venta no encontrada");
}

// Obtener detalles de la venta
$stmt = $pdo->prepare("
    SELECT dv.*, p.nombre, p.imagen 
    FROM detalle_ventas dv
    JOIN productos p ON dv.id_producto = p.id
    WHERE dv.id_venta = ?
");
$stmt->execute([$id_venta]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular IGV (18%)
$igv = $venta['total'] * 0.18;
$subtotal = $venta['total'] - $igv;
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- Comprobante impresion -->
            <div class="card border-dark" id="comprobante">
                <div class="card-body">
                    <!-- Encabezado -->
                    <div class="text-center mb-4">
                        <h1 class="mb-1">FERRE-TODO</h1>
                        <p class="mb-1">Ferretería y Materiales de Construcción</p>
                        <p class="mb-1">Av. Principal 123, Lima - Perú</p>
                        <p class="mb-1">RUC: 20123456789</p>
                        <p class="mb-1">Tel: (01) 234-5678 | WhatsApp: 987 654 321</p>
                        <hr class="my-2">
                        
                        <h2 class="<?php echo $venta['tipo_documento'] === 'boleta' ? 'text-primary' : 'text-success'; ?>">
                            <?php echo $venta['tipo_documento'] === 'boleta' ? 'BOLETA DE VENTA ELECTRÓNICA' : 'FACTURA ELECTRÓNICA'; ?>
                        </h2>
                        <h3 class="mb-0"><?php echo $venta['numero_documento']; ?></h3>
                    </div>
                    
                    <!-- Información del cliente y empresa -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border p-3">
                                <h5 class="mb-3">INFORMACIÓN DEL CLIENTE</h5>
                                <p class="mb-1"><strong>Nombre/Razón Social:</strong></p>
                                <p class="mb-2"><?php echo $venta['nombre_cliente']; ?></p>
                                
                                <p class="mb-1"><strong><?php echo $venta['tipo_documento_cliente']; ?>:</strong></p>
                                <p class="mb-2"><?php echo $venta['numero_documento_cliente']; ?></p>
                                
                                <p class="mb-1"><strong>Dirección:</strong></p>
                                <p class="mb-0"><?php echo $venta['direccion_cliente'] ?? 'No especificada'; ?></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="border p-3">
                                <h5 class="mb-3">INFORMACIÓN DE LA VENTA</h5>
                                <p class="mb-1"><strong>Fecha y Hora:</strong></p>
                                <p class="mb-2"><?php echo date('d/m/Y H:i:s', strtotime($venta['fecha'])); ?></p>
                                
                                <p class="mb-1"><strong>Vendedor:</strong></p>
                                <p class="mb-2"><?php echo $venta['vendedor'] ?? 'Sistema'; ?></p>
                                
                                <p class="mb-1"><strong>Método de Pago:</strong></p>
                                <p class="mb-0">Efectivo</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detalles de productos -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">Cant.</th>
                                    <th width="45%">Descripción</th>
                                    <th width="15%">P. Unit.</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="15%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                <tr>
                                    <td class="text-center"><?php echo $detalle['cantidad']; ?></td>
                                    <td><?php echo $detalle['nombre']; ?></td>
                                    <td class="text-end">S/<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                    <td class="text-end">S/<?php echo number_format($detalle['subtotal'], 2); ?></td>
                                    <td class="text-end">S/<?php echo number_format($detalle['subtotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Totales -->
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="text-end"><strong>OP. GRAVADA:</strong></td>
                                        <td class="text-end">S/<?php echo number_format($subtotal, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end"><strong>I.G.V. (18%):</strong></td>
                                        <td class="text-end">S/<?php echo number_format($igv, 2); ?></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td class="text-end"><strong>IMPORTE TOTAL:</strong></td>
                                        <td class="text-end"><strong>S/<?php echo number_format($venta['total'], 2); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="border p-3 mt-4">
                        <h5 class="mb-3">INFORMACIÓN ADICIONAL</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>SON:</strong></p>
                                <p><?php 
                                    require_once 'numero_a_letras.php'; // Necesitarás esta función
                                    echo num2letras($venta['total']);
                                ?> SOLES</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>OBSERVACIONES:</strong></p>
                                <p>Gracias por su compra. Productos con garantía de 12 meses.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Código QR y firma -->
                    <div class="row mt-4">
                        <div class="col-md-6 text-center">
                            <div class="border p-3">
                                <h6>CÓDIGO QR</h6>
                                <div class="bg-light p-3 d-inline-block">
                                    <!-- Espacio para QR -->
                                    <div style="width: 150px; height: 150px; background: #f8f9fa;" 
                                         class="d-flex align-items-center justify-content-center">
                                        <span class="text-muted">QR CODE</span>
                                    </div>
                                </div>
                                <p class="mt-2 mb-0"><small>Escanear para verificar</small></p>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="border p-3">
                                <h6>FIRMA Y SELLO</h6>
                                <div class="mt-4 pt-4">
                                    <hr>
                                    <p class="mb-0">FIRMA AUTORIZADA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pie de página -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="mb-1"><strong>¡GRACIAS POR SU COMPRA!</strong></p>
                        <p class="mb-1">Representación impresa de comprobante electrónico</p>
                        <p class="mb-0"><small>Conserve este comprobante para cualquier reclamo o garantía</small></p>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="text-center mt-4">
                <div class="btn-group" role="group">
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i> Volver al Inicio
                    </a>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i> Imprimir Comprobante
                    </button>
                    <button class="btn btn-info" onclick="descargarPDF()">
                        <i class="bi bi-download me-2"></i> Descargar PDF
                    </button>
                    <a href="https://wa.me/?text=Hola,%20adjunto%20mi%20comprobante%20de%20compra%20<?php echo $venta['numero_documento']; ?>" 
                       target="_blank" class="btn btn-success" style="background-color: #25D366; border-color: #25D366;">
                        <i class="bi bi-whatsapp me-2"></i> Enviar por WhatsApp
                    </a>
                </div>
            </div>
            
            <!-- Información adicional para el cliente -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Información Importante</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                                <h6>Garantía</h6>
                                <p class="mb-0">Todos nuestros productos tienen garantía de 12 meses.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="bi bi-arrow-return-left display-4 text-primary mb-3"></i>
                                <h6>Devoluciones</h6>
                                <p class="mb-0">Aceptamos devoluciones dentro de los 7 días posteriores a la compra.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="bi bi-headset display-4 text-primary mb-3"></i>
                                <h6>Soporte</h6>
                                <p class="mb-0">¿Necesitas ayuda? Llámanos al (01) 234-5678.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para impresión */
@media print {
    body * {
        visibility: hidden;
    }
    #comprobante, #comprobante * {
        visibility: visible;
    }
    #comprobante {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
        box-shadow: none;
    }
    .no-print {
        display: none !important;
    }
}

#comprobante {
    background-color: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.border {
    border: 1px solid #dee2e6 !important;
}
</style>

<script>
function descargarPDF() {
    // Usar html2pdf.js para convertir a PDF
    alert('Funcionalidad de descarga PDF. Para producción, usar una librería como jsPDF o html2pdf.');
    
    // Ejemplo básico con html2canvas
    html2canvas(document.getElementById('comprobante')).then(canvas => {
        let link = document.createElement('a');
        link.download = 'comprobante-<?php echo $venta['numero_documento']; ?>.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}

// Configurar botones de impresión
document.addEventListener('DOMContentLoaded', function() {
    // Agregar clase a elementos no imprimibles
    document.querySelectorAll('nav, .btn-group, .card:last-child').forEach(el => {
        el.classList.add('no-print');
    });
});
</script>

<!-- Incluir html2canvas para captura -->
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<?php include 'includes/footer_public.php'; ?>