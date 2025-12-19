<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../../login.php');
    exit;
}

include '../../config/db.php';
include '../../includes/header.php';

// Parámetros de filtro
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
$limit = $_GET['limit'] ?? 10;

// Productos más vendidos
$sql_mas_vendidos = "
    SELECT 
        p.id,
        p.nombre,
        p.categoria,
        p.precio,
        SUM(dv.cantidad) as total_vendido,
        SUM(dv.subtotal) as total_ingresos,
        p.stock,
        p.stock_minimo
    FROM detalle_ventas dv
    JOIN productos p ON dv.id_producto = p.id
    JOIN ventas v ON dv.id_venta = v.id
    WHERE v.fecha BETWEEN ? AND ?
    GROUP BY p.id
    ORDER BY total_vendido DESC
    LIMIT ?
";

$stmt = $pdo->prepare($sql_mas_vendidos);
$stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59', $limit]);
$mas_vendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Productos no vendidos
$sql_no_vendidos = "
    SELECT 
        p.*,
        (SELECT COUNT(*) FROM detalle_ventas dv WHERE dv.id_producto = p.id) as veces_vendido
    FROM productos p
    HAVING veces_vendido = 0
    ORDER BY p.nombre
";

$stmt = $pdo->prepare($sql_no_vendidos);
$stmt->execute();
$no_vendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Productos con bajo rendimiento (vendidos menos de 5 veces)
$sql_bajo_rendimiento = "
    SELECT 
        p.*,
        COUNT(dv.id) as veces_vendido,
        SUM(dv.cantidad) as total_vendido
    FROM productos p
    LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto
    GROUP BY p.id
    HAVING veces_vendido > 0 AND veces_vendido <= 5
    ORDER BY veces_vendido ASC
";

$stmt = $pdo->prepare($sql_bajo_rendimiento);
$stmt->execute();
$bajo_rendimiento = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../productos/index.php">
                            <i class="bi bi-box-seam me-2"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../ventas/index.php">
                            <i class="bi bi-cart-check me-2"></i> Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../usuarios/index.php">
                            <i class="bi bi-people me-2"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="mas_vendidos.php">
                            <i class="bi bi-graph-up me-2"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../contactos.php">
                            <i class="bi bi-envelope me-2"></i> Contactos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../../logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Reportes de Ventas</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Filtrar Reporte</h5>
                    <form method="get" class="row g-3">
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                   value="<?php echo $fecha_inicio; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                   value="<?php echo $fecha_fin; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="limit" class="form-label">Top N Productos</label>
                            <select class="form-select" id="limit" name="limit">
                                <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>Top 5</option>
                                <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>Top 10</option>
                                <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>Top 20</option>
                                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>Top 50</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumen estadístico -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Productos Más Vendidos
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count($mas_vendidos); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-trophy fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Productos No Vendidos
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count($no_vendidos); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-x-circle fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Bajo Rendimiento
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count($bajo_rendimiento); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-arrow-down-circle fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Período
                                    </div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        <?php echo date('d/m/Y', strtotime($fecha_inicio)); ?> - 
                                        <?php echo date('d/m/Y', strtotime($fecha_fin)); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-calendar-range fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos más vendidos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i> Top <?php echo $limit; ?> Productos Más Vendidos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Unidades Vendidas</th>
                                    <th>Total Ingresos</th>
                                    <th>Stock Actual</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mas_vendidos as $index => $producto): 
                                    $porcentaje = ($producto['total_vendido'] / array_sum(array_column($mas_vendidos, 'total_vendido'))) * 100;
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary rounded-circle p-2">
                                            <?php echo $index + 1; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../assets/img/<?php echo $producto['imagen'] ?? 'default.jpg'; ?>" 
                                                 width="40" height="40" class="rounded me-2">
                                            <strong><?php echo $producto['nombre']; ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo $producto['categoria'] ?? 'General'; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: <?php echo min($porcentaje, 100); ?>%">
                                                </div>
                                            </div>
                                            <strong><?php echo $producto['total_vendido']; ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-success">
                                        <strong>S/<?php echo number_format($producto['total_ingresos'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $producto['stock'] > $producto['stock_minimo'] * 2 ? 'success' : 
                                                 ($producto['stock'] > $producto['stock_minimo'] ? 'warning' : 'danger'); 
                                        ?>">
                                            <?php echo $producto['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($producto['stock'] <= $producto['stock_minimo']): ?>
                                            <span class="badge bg-danger">Reabastecer</span>
                                        <?php elseif ($porcentaje > 20): ?>
                                            <span class="badge bg-success">Alta demanda</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Demanda normal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Productos no vendidos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-x-circle me-2 text-danger"></i> Productos No Vendidos
                        <span class="badge bg-danger ms-2"><?php echo count($no_vendidos); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($no_vendidos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($no_vendidos as $producto): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../../assets/img/<?php echo $producto['imagen']; ?>" 
                                                     width="40" height="40" class="rounded me-2">
                                                <?php echo $producto['nombre']; ?>
                                            </div>
                                        </td>
                                        <td><?php echo $producto['categoria'] ?? 'General'; ?></td>
                                        <td>S/<?php echo number_format($producto['precio'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $producto['stock'] > $producto['stock_minimo'] ? 'success' : 'danger'; 
                                            ?>">
                                                <?php echo $producto['stock']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($producto['fecha_creacion'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="../productos/editar.php?id=<?php echo $producto['id']; ?>" 
                                                   class="btn btn-outline-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-outline-info" 
                                                        onclick="mostrarSugerencias(<?php echo $producto['id']; ?>)">
                                                    <i class="bi bi-lightbulb"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-1 text-success"></i>
                            <h4 class="mt-3">¡Excelente!</h4>
                            <p class="text-muted">Todos los productos han tenido ventas en este período.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Productos con bajo rendimiento -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-down-circle me-2 text-warning"></i> Productos con Bajo Rendimiento
                        <span class="badge bg-warning ms-2"><?php echo count($bajo_rendimiento); ?></span>
                    </h5>
                    <p class="mb-0 text-muted"><small>Productos vendidos menos de 5 veces</small></p>
                </div>
                <div class="card-body">
                    <?php if (!empty($bajo_rendimiento)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Veces Vendido</th>
                                        <th>Unidades Totales</th>
                                        <th>Última Venta</th>
                                        <th>Precio</th>
                                        <th>Sugerencias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bajo_rendimiento as $producto): 
                                        // Obtener fecha de última venta
                                        $stmt = $pdo->prepare("
                                            SELECT MAX(v.fecha) as ultima_venta 
                                            FROM ventas v
                                            JOIN detalle_ventas dv ON v.id = dv.id_venta
                                            WHERE dv.id_producto = ?
                                        ");
                                        $stmt->execute([$producto['id']]);
                                        $ultima_venta = $stmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../../assets/img/<?php echo $producto['imagen']; ?>" 
                                                     width="40" height="40" class="rounded me-2">
                                                <?php echo $producto['nombre']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">
                                                <?php echo $producto['veces_vendido']; ?> veces
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo $producto['total_vendido'] ?? 0; ?> unidades
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($ultima_venta['ultima_venta']): ?>
                                                <?php echo date('d/m/Y', strtotime($ultima_venta['ultima_venta'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin ventas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>S/<?php echo number_format($producto['precio'], 2); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="mostrarSugerencias(<?php echo $producto['id']; ?>)">
                                                <i class="bi bi-lightbulb me-1"></i> Ver sugerencias
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-emoji-smile display-1 text-success"></i>
                            <h4 class="mt-3">¡Buen trabajo!</h4>
                            <p class="text-muted">No hay productos con bajo rendimiento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal de Sugerencias -->
<div class="modal fade" id="modalSugerencias">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sugerencias para Mejorar Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contenido-sugerencias">
                    <h6>Estrategias recomendadas:</h6>
                    <ul>
                        <li><strong>Reducir precio:</strong> Ofrecer descuentos del 10-20%</li>
                        <li><strong>Promoción:</strong> Incluir en ofertas especiales</li>
                        <li><strong>Exposición:</strong> Colocar en lugares visibles</li>
                        <li><strong>Paquetes:</strong> Vender en combos con productos populares</li>
                        <li><strong>Marketing:</strong> Destacar en redes sociales</li>
                    </ul>
                    <hr>
                    <div class="form-group">
                        <label>Agregar nota:</label>
                        <textarea class="form-control" rows="3" placeholder="Notas sobre este producto..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar Nota</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Función para mostrar sugerencias
function mostrarSugerencias(idProducto) {
    $('#modalSugerencias').modal('show');
}

// Gráfico de distribución de ventas
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.createElement('canvas');
    ctx.id = 'graficoVentas';
    document.querySelector('.card-body').prepend(ctx);
    
    const data = {
        labels: <?php echo json_encode(array_column($mas_vendidos, 'nombre')); ?>,
        datasets: [{
            label: 'Unidades Vendidas',
            data: <?php echo json_encode(array_column($mas_vendidos, 'total_vendido')); ?>,
            backgroundColor: [
                '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                '#20c997', '#fd7e14', '#6610f2', '#e83e8c', '#6c757d'
            ]
        }]
    };
    
    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>