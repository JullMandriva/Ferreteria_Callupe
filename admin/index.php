<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';
include '../includes/header.php';

// Productos más vendidos
 $stmt = $pdo->query("
    SELECT p.nombre, SUM(dv.cantidad) as total_vendido 
    FROM detalle_ventas dv
    JOIN productos p ON dv.id_producto = p.id
    GROUP BY p.id
    ORDER BY total_vendido DESC
    LIMIT 5
");
 $mas_vendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Productos no vendidos
 $stmt = $pdo->query("
    SELECT p.* FROM productos p
    LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto
    WHERE dv.id_producto IS NULL
");
 $no_vendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Productos en quiebre de stock
 $stmt = $pdo->query("
    SELECT * FROM productos 
    WHERE stock <= stock_minimo
    ORDER BY stock ASC
");
 $quiebre_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos/index.php">
                            <i class="bi bi-box-seam me-2"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ventas/index.php">
                            <i class="bi bi-cart-check me-2"></i> Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios/index.php">
                            <i class="bi bi-people me-2"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes/mas_vendidos.php">
                            <i class="bi bi-graph-up me-2"></i> Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contactos.php">
                            <i class="bi bi-envelope me-2"></i> Contactos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Panel de Administración</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="badge bg-primary">Bienvenido, <?php echo $_SESSION['usuario']['nombre']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Alertas de quiebre de stock -->
            <?php if (!empty($quiebre_stock)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">¡Alerta de Quiebre de Stock!</h4>
                    <p>Hay <?php echo count($quiebre_stock); ?> productos con stock bajo.</p>
                    <hr>
                    <ul>
                        <?php foreach ($quiebre_stock as $producto): ?>
                            <li><?php echo $producto['nombre']; ?> - Stock: <?php echo $producto['stock']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Cards de resumen -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Productos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php 
                                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $result['total'];
                                        ?>
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
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventas</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php 
                                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM ventas");
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $result['total'];
                                        ?>
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
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Usuarios</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php 
                                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $result['total'];
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-people fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Quiebre Stock</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count($quiebre_stock); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reportes -->
            <div class="row">
                <!-- Productos más vendidos -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Productos Más Vendidos</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Vendida</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mas_vendidos as $producto): ?>
                                        <tr>
                                            <td><?php echo $producto['nombre']; ?></td>
                                            <td><?php echo $producto['total_vendido']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Productos no vendidos -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Productos No Vendidos</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($no_vendidos as $producto): ?>
                                        <tr>
                                            <td><?php echo $producto['nombre']; ?></td>
                                            <td><?php echo $producto['stock']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>