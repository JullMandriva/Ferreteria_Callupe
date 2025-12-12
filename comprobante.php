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
 $stmt = $pdo->prepare("SELECT * FROM ventas WHERE id = ?");
 $stmt->execute([$id_venta]);
 $venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("Venta no encontrada");
}

// Obtener detalles de la venta
 $stmt = $pdo->prepare("
    SELECT dv.*, p.nombre 
    FROM detalle_ventas dv
    JOIN productos p ON dv.id_producto = p.id
    WHERE dv.id_venta = ?
");
 $stmt->execute([$id_venta]);
 $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para formatear número correctamente
function formatearNumero($numero) {
    // Asegurarse de que sea un número
    $numero = floatval($numero);
    // Formatear con 2 decimales
    return number_format($numero, 2, '.', ',');
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3><?php echo $venta['tipo_documento'] === 'boleta' ? 'BOLETA DE VENTA' : 'FACTURA'; ?></h3>
                    <p class="mb-0">N° <?php echo $venta['numero_documento']; ?></p>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Datos del Cliente</h5>
                            <p><strong>Nombre:</strong> <?php echo $venta['nombre_cliente']; ?></p>
                            <p><strong><?php echo $venta['tipo_documento_cliente']; ?>:</strong> <?php echo $venta['numero_documento_cliente']; ?></p>
                            <p><strong>Email:</strong> <?php echo $venta['email_cliente']; ?></p>
                            <p><strong>Teléfono:</strong> <?php echo $venta['telefono_cliente']; ?></p>
                            <p><strong>Dirección:</strong> <?php echo $venta['direccion_cliente']; ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>Datos de la Empresa</h5>
                            <p><strong>Ferretodo</strong></p>
                            <p><strong>RUC:</strong> 12345678901</p>
                        </div>
                    </div>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Descripción</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $detalle): ?>
                                <tr>
                                    <td><?php echo $detalle['cantidad']; ?></td>
                                    <td><?php echo $detalle['nombre']; ?></td>
                                    <td>S/<?php echo formatearNumero($detalle['precio_unitario']); ?></td>
                                    <td>S/<?php echo formatearNumero($detalle['subtotal']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong>S/<?php echo formatearNumero($venta['total']); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Fecha: <?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></p>
                        <p class="mb-0">¡Gracias por su compra!</p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-primary">Volver al inicio</a>
                        <button class="btn btn-secondary" onclick="window.print()">Imprimir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer_public.php'; ?>