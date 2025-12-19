<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'cajero'])) {
    header('Location: ../../login.php');
    exit;
}

include '../../config/db.php';
include '../../includes/header.php';

// Verificar si existe la tabla ventas
try {
    $pdo->query("SELECT 1 FROM ventas LIMIT 1");
} catch (Exception $e) {
    // Crear tabla si no existe
    $pdo->query("CREATE TABLE IF NOT EXISTS ventas (
        id INT PRIMARY KEY AUTO_INCREMENT,
        id_usuario INT,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total DECIMAL(10,2),
        tipo_documento ENUM('boleta', 'factura'),
        numero_documento VARCHAR(20),
        nombre_cliente VARCHAR(100),
        tipo_documento_cliente VARCHAR(20),
        numero_documento_cliente VARCHAR(20),
        email_cliente VARCHAR(100),
        telefono_cliente VARCHAR(20),
        direccion_cliente TEXT,
        estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'completada'
    )");
}


// Obtener ventas del día
$fecha_hoy = date('Y-m-d');
$stmt = $pdo->prepare("SELECT v.*, u.nombre as vendedor FROM ventas v 
                       LEFT JOIN usuarios u ON v.id_usuario = u.id 
                       WHERE DATE(v.fecha) = ? 
                       ORDER BY v.fecha DESC");
$stmt->execute([$fecha_hoy]);
$ventas_hoy = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total del día
$stmt = $pdo->prepare("SELECT SUM(total) as total_dia FROM ventas WHERE DATE(fecha) = ?");
$stmt->execute([$fecha_hoy]);
$total_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total_dia'] ?? 0;
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar para cajero -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2 me-2"></i> Panel Cajero
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nueva_venta.php">
                            <i class="bi bi-plus-circle me-2"></i> Nueva Venta
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="historial.php">
                            <i class="bi bi-clock-history me-2"></i> Historial
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="devoluciones.php">
                            <i class="bi bi-arrow-return-left me-2"></i> Devoluciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="corte.php">
                            <i class="bi bi-cash-stack me-2"></i> Corte de Caja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../../logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
                
                <!-- Resumen rápido -->
                <div class="mt-4 p-3 bg-white border rounded">
                    <h6 class="mb-3">Resumen Hoy</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ventas:</span>
                        <strong><?php echo count($ventas_hoy); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total:</span>
                        <strong class="text-success">S/<?php echo number_format($total_dia, 2); ?></strong>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Panel de Cajero</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="badge bg-primary">Bienvenido, <?php echo $_SESSION['usuario']['nombre']; ?></span>
                    </div>
                    <a href="nueva_venta.php" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Nueva Venta
                    </a>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ventas Hoy</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count($ventas_hoy); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-cart-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Hoy</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        S/<?php echo number_format($total_dia, 2); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-cash-stack fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Productos Vendidos</div>
                                    <?php 
                                    $stmt = $pdo->prepare("SELECT SUM(dv.cantidad) as total FROM detalle_ventas dv 
                                                          JOIN ventas v ON dv.id_venta = v.id 
                                                          WHERE DATE(v.fecha) = ?");
                                    $stmt->execute([$fecha_hoy]);
                                    $productos_vendidos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                                    ?>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $productos_vendidos; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ticket Promedio</div>
                                    <?php 
                                    $ticket_promedio = count($ventas_hoy) > 0 ? $total_dia / count($ventas_hoy) : 0;
                                    ?>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        S/<?php echo number_format($ticket_promedio, 2); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-receipt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventas recientes -->
            <div class="card">
                <div class="card-header">
                    <h5>Ventas del Día (<?php echo date('d/m/Y'); ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>N° Comprobante</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Hora</th>
                                    <th>Vendedor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas_hoy as $venta): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $venta['numero_documento']; ?></strong>
                                    </td>
                                    <td><?php echo $venta['nombre_cliente']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $venta['tipo_documento'] == 'boleta' ? 'info' : 'primary'; ?>">
                                            <?php echo ucfirst($venta['tipo_documento']); ?>
                                        </span>
                                    </td>
                                    <td class="text-success">
                                        <strong>S/<?php echo number_format($venta['total'], 2); ?></strong>
                                    </td>
                                    <td><?php echo date('H:i', strtotime($venta['fecha'])); ?></td>
                                    <td><?php echo $venta['vendedor'] ?? 'Sistema'; ?></td>
                                    <td>
                                        <a href="detalle.php?id=<?php echo $venta['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="imprimir.php?id=<?php echo $venta['id']; ?>" 
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($ventas_hoy)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-cart-x display-4 text-muted"></i>
                                        <p class="mt-3">No hay ventas registradas hoy</p>
                                        <a href="nueva_venta.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Realizar primera venta
                                        </a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Métodos de pago -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6>Métodos de Pago Hoy</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoPagos" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6>Productos Más Vendidos Hoy</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php
                                $stmt = $pdo->prepare("SELECT p.nombre, SUM(dv.cantidad) as total 
                                                      FROM detalle_ventas dv
                                                      JOIN productos p ON dv.id_producto = p.id
                                                      JOIN ventas v ON dv.id_venta = v.id
                                                      WHERE DATE(v.fecha) = ?
                                                      GROUP BY p.id
                                                      ORDER BY total DESC
                                                      LIMIT 5");
                                $stmt->execute([$fecha_hoy]);
                                $top_productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                
                                <?php foreach ($top_productos as $index => $producto): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
                                        <?php echo $producto['nombre']; ?>
                                    </div>
                                    <span class="badge bg-success rounded-pill">
                                        <?php echo $producto['total']; ?> unidades
                                    </span>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if (empty($top_productos)): ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-bar-chart"></i>
                                    <p class="mb-0">No hay datos de ventas</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de métodos de pago (datos de ejemplo)
const ctx = document.getElementById('graficoPagos').getContext('2d');
const graficoPagos = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Efectivo', 'Tarjeta', 'Yape/Plín', 'Transferencia'],
        datasets: [{
            data: [65, 20, 10, 5],
            backgroundColor: [
                '#0d6efd',
                '#198754',
                '#ffc107',
                '#6f42c1'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include '../../includes/footer.php'; ?>